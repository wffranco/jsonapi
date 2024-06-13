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
        return function (Collection $collection, array $attributeKeys, array $missingKeys = []): TestResponse {
            /** @var TestResponse $this */
            return $this->assertJsonApiCollectionStructure($attributeKeys)
                ->assertJson([
                    'data' => $collection->map(
                        fn (Model $model) => JsonApiDocument::make($model)
                            ->attributes($attributeKeys)
                            ->links()
                            ->get('data')
                    )->all(),
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
        return function (Model $model, array $attributeKeys = [], array $missingKeys = []): TestResponse {
            /** @var TestResponse $this */
            return $this->assertJsonApiResourceStructure($attributeKeys)
                ->assertJson([
                    'data' => JsonApiDocument::make($model)->attributes($attributeKeys)->links()->get('data'),
                ])
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

    public function assertJsonApiRelationshipLinks()
    {
        return function (Model $model, array $relationshipKeys): TestResponse {
            /** @var TestResponse $this */
            foreach ($relationshipKeys as $type) {
                try {
                    $this->assertJson([
                        'data' => [
                            'relationships' => [
                                $type => [
                                    'links' => [
                                        'self' => route("api.v1.{$model->getResourceType()}.relationships.{$type}", $model),
                                        'related' => route("api.v1.{$model->getResourceType()}.{$type}", $model),
                                    ],
                                ],
                            ],
                        ],
                    ]);
                } catch (ExpectationFailedException $e) {
                    PHPUnit::fail(implode(PHP_EOL, [
                        "Failed to find a valid JSON:API relationship links for '{$type}'.",
                        'Be sure to include the `getRelationshipKeys` method in your resource class.',
                        '',
                        $e->getMessage(),
                    ]));
                }
            }

            return $this;
        };
    }

    public function assertJsonApiValidationErrors()
    {
        return function (string $attribute, bool $raw = false): TestResponse {
            /** @var TestResponse $this */
            try {
                if ($raw) {
                    // The attribute is already in the correct format
                } elseif (\Str::startsWith($attribute, 'relationships')) {
                    $attribute = "data.{$attribute}.data.id";
                } elseif (! \Str::startsWith($attribute, 'data')) {
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
}
