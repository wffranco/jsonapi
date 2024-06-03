<?php

namespace App\JsonApi;

use Illuminate\Testing\Assert as PHPUnit;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\ExpectationFailedException;

class JsonApiTestResponseMixin
{
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
            try {
                $this->assertHeader('Content-Type', 'application/vnd.api+json');
            } catch (ExpectationFailedException $e) {
                PHPUnit::fail('Failed to find a valid JSON:API header'.PHP_EOL.PHP_EOL.$e->getMessage());
            }

            return $this->assertStatus(422);
        };
    }
}
