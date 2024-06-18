<?php

namespace App\JsonApi\Tests;

use App\JsonApi\JsonApiDocument;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Testing\TestResponse;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\ExpectationFailedException;

/**
 * @mixin \Tests\TestCase
 */
trait JsonApiRequests
{
    public function assertJsonApiPaginationLinks(TestResponse $response, int $number = 1, int $size = 15, array $queries = [], ?int $last = null, int $first = 1): static
    {
        $links = [
            'first' => ['page[number]' => $first, 'page[size]' => $size],
            'last' => ['page[number]' => $last, 'page[size]' => $size],
            'prev' => ['page[number]' => $number === $first ? $first : $number - 1, 'page[size]' => $size],
            'next' => ['page[number]' => $number === $last ? $last : $number + 1, 'page[size]' => $size],
        ];
        foreach ($links as $name => $query) {
            $link = urldecode($response->json("links.{$name}"));
            foreach ($query as $key => $value) {
                $this->assertStringContainsString("{$key}={$value}", $link);
                foreach ($queries as $query) {
                    $this->assertStringContainsString($query, $link);
                }
            }
        }

        return $this;
    }

    public function actingAs(?UserContract $user = null, $abilities = null): static
    {
        Sanctum::actingAs(
            $user ?? User::factory()->createOne(),
            $abilities ?? [],
        );

        return $this;
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
        if (! isset($data['data'])) {
            $segments = explode('/', (string) \Str::of(parse_url($uri, PHP_URL_PATH))->afterNext('api/v1/'));
            $document = JsonApiDocument::make()
                ->type($segments[0] ?? null)
                ->id($segments[1] ?? null);
            if (isset($data['attributes'])) {
                $document->attributes($data['attributes'], false)->relationshipData($data['relationships'] ?? null);
            } else {
                $document->attributes($data, false);
            }
            $data = $document->all();
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
