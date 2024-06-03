<?php

namespace App\JsonApi\Tests;

use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\ExpectationFailedException;

/**
 * @mixin \Tests\TestCase
 */
trait JsonApiRequests
{
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
        if (! isset($data['data'])) {
            $segments = explode('/', (string) \Str::of(parse_url($uri, PHP_URL_PATH))->afterNext('api/v1/'));
            $data = ['data' => array_filter([
                'type' => $segments[0] ?? null,
                'id' => $segments[1] ?? null,
                'attributes' => $data,
            ])];
        }

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
