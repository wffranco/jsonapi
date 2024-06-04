<?php

namespace App\JsonApi;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Assert as PHPUnit;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\ExpectationFailedException;

class JsonApiTestResponseMixin
{
    public function assertJsonApiCollection()
    {
        $parseResourse = $this->parseJsonApiResource();

        return function (Collection $collection, array $attributeKeys, array $missingKeys = []) use ($parseResourse): TestResponse {
            /** @var TestResponse $this */
            return $this->assertJsonApiCollectionStructure($attributeKeys)
                ->assertJson([
                    'data' => $collection->map(fn (Model $model) => $parseResourse($model, $attributeKeys))->all(),
                    'links' => [
                        'self' => route('api.v1.'.$collection[0]->getResourceType().'.index'),
                    ],
                    'meta' => ['total' => $collection->count()],
                ])
                ->when(! empty($missingKeys), function (TestResponse $response) use ($collection, $missingKeys) {
                    $collection->each(fn (Model $model) => $response->assertJsonApiMissingAttributes($model, $missingKeys));
                });
        };
    }

    public function assertJsonApiCollectionStructure()
    {
        return function (array $attributeKeys = []): TestResponse {
            /** @var TestResponse $this */
            return $this->assertJsonStructure([
                'data' => [
                    '*' => array_merge(
                        ['type', 'id'],
                        empty($attributeKeys) ? ['attributes'] : ['attributes' => $attributeKeys],
                        ['links' => ['self']],
                    ),
                ],
                'links' => ['self', 'first', 'last', 'prev', 'next'],
                'meta' => ['total'],
            ]);
        };
    }

    public function assertJsonApiHeaderContentType()
    {
        return function (): TestResponse {
            /** @var TestResponse $this */
            try {
                $this->assertHeader('Content-Type', 'application/vnd.api+json');
            } catch (ExpectationFailedException $e) {
                PHPUnit::fail("Failed to find a valid JSON:API header 'Content-Type'".PHP_EOL.PHP_EOL.$e->getMessage());
            }

            return $this;
        };
    }

    public function assertJsonApiHeaderLocation()
    {
        return function (Model $model): TestResponse {
            /** @var TestResponse $this */
            try {
                $this->assertHeader('Location', route('api.v1.'.$model->getResourceType().'.show', $model));
            } catch (ExpectationFailedException $e) {
                PHPUnit::fail("Failed to find a valid JSON:API header 'Location'".PHP_EOL.PHP_EOL.$e->getMessage());
            }

            return $this;
        };
    }

    public function assertJsonApiMissingAttributes()
    {
        return function (Model $model, array $attributeKeys): TestResponse {
            /** @var TestResponse $this */
            $attributes = array_combine($attributeKeys, array_map(fn ($key) => $model->{$key}, $attributeKeys));

            return $this->assertJsonMissing(['data' => ['attributes' => $attributes]])
                ->assertJsonMissing(['data' => ['attributes' => array_map(fn () => null, $attributes)]]);
        };
    }

    public function assertJsonApiResource()
    {
        $parseResourse = $this->parseJsonApiResource();

        return function (Model $model, array $attributeKeys = [], array $missingKeys = []) use ($parseResourse): TestResponse {
            /** @var TestResponse $this */
            return $this->assertJsonApiResourceStructure($attributeKeys)
                ->assertJson(['data' => $parseResourse($model, $attributeKeys)])
                ->when(! empty($missingKeys), fn (TestResponse $response) => $response->assertJsonApiMissingAttributes($model, $missingKeys));
        };
    }

    public function assertJsonApiResourceStructure()
    {
        return function (array $attributeKeys = []): TestResponse {
            /** @var TestResponse $this */
            return $this->assertJsonStructure([
                'data' => array_merge(
                    ['type', 'id'],
                    empty($attributeKeys) ? ['attributes'] : ['attributes' => $attributeKeys],
                    ['links' => ['self']],
                ),
            ]);
        };
    }

    public function assertJsonApiValidationErrors()
    {
        return function (string $attribute): TestResponse {
            /** @var TestResponse $this */
            try {
                if (! \Str::startsWith($attribute, 'data')) {
                    $attribute = "data.attributes.{$attribute}";
                }
                $this->assertJsonFragment([
                    'source' => ['pointer' => '/'.str_replace('.', '/', $attribute)],
                ]);
            } catch (ExpectationFailedException $e) {
                PHPUnit::fail("Failed to find a JSON:API validation error for attribute '{$attribute}'".PHP_EOL.PHP_EOL.$e->getMessage());
            }
            try {
                $this->assertJsonStructure([
                    'errors' => [
                        ['title', 'detail', 'source' => ['pointer']],
                    ],
                ]);
            } catch (ExpectationFailedException $e) {
                PHPUnit::fail('Failed to find a valid JSON:API error response'.PHP_EOL.PHP_EOL.$e->getMessage());
            }
            $this->assertJsonApiHeaderContentType();

            return $this->assertStatus(422);
        };
    }

    private function parseJsonApiResource()
    {
        return fn (Model $model, array $attributeKeys = []): array => JsonApiDocument::make()
            ->type($model->getResourceType())
            ->id($model->getRouteKey())
            ->attributes($model->only($attributeKeys))
            ->links([
                'self' => route('api.v1.'.$model->getResourceType().'.show', $model),
            ])
            ->get('data');
    }
}
