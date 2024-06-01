<?php

namespace App\JsonApi\Tests;

use Illuminate\Testing\Assert as PHPUnit;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\ExpectationFailedException;

/**
 * @mixin \Tests\TestCase
 */
trait JsonApiRequests
{
    protected function setUp(): void
    {
        parent::setUp();
        TestResponse::macro('assertJsonApiValidationErrors', function ($attribute) {
            /** @var TestResponse $this */
            try {
                $this->assertJsonFragment([
                    'source' => ['pointer' => "/data/attributes/{$attribute}"],
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
        });
    }

    public function json($method, $uri, array $data = [], array $headers = [], $options = 0)
    {
        /** @var TestResponse $response */
        $response = parent::json($method, $uri, $data, $headers, $options);
        if ($response->status() === 406 && ! isset($headers['Accept'])) {
            throw new ExpectationFailedException("Response expect 'Accept' header. Maybe you wanted to use the 'JsonApi' method.");
        }

        return $response;
    }

    public function jsonApi($method, $uri, array $data = [], array $headers = [], $options = 0): TestResponse
    {
        $headers['Accept'] = 'application/vnd.api+json';

        return $this->json($method, $uri, $data, $headers, $options);
    }

    public function deleteJsonApi($uri, array $data = [], array $headers = [], $options = 0): TestResponse
    {
        return $this->jsonApi('DELETE', $uri, $data, $headers, $options);
    }

    public function getJsonApi($uri, array $headers = [], $options = 0): TestResponse
    {
        return $this->jsonApi('GET', $uri, [], $headers, $options);
    }

    public function patchJsonApi($uri, array $data = [], array $headers = [], $options = 0): TestResponse
    {
        $headers['Content-Type'] = 'application/vnd.api+json';

        return $this->jsonApi('PATCH', $uri, $data, $headers, $options);
    }

    public function postJsonApi($uri, array $data = [], array $headers = [], $options = 0): TestResponse
    {
        $headers['Content-Type'] = 'application/vnd.api+json';

        return $this->jsonApi('POST', $uri, $data, $headers, $options);
    }
}
