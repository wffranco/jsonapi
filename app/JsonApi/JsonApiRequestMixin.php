<?php

namespace App\JsonApi;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @mixin Request
 */
class JsonApiRequestMixin
{
    public function getAttributes()
    {
        return function ($key = null, $default = null) {
            $relationship = $this->validatedData('attributes', []);

            return Arr::get($relationship, $key, $default);
        };
    }

    public function getRelationshipId()
    {
        return function (string $key, ?string $default = null) {
            return $this->getRelationships("{$key}.data.id", $default);
        };
    }

    public function getRelationships()
    {
        return function ($key = null, $default = null) {
            if (! $this->hasRelationships($key)) {
                return $default;
            }

            $relationship = $this->validatedData('relationships', []);

            return Arr::get($relationship, $key, $default);
        };
    }

    public function getResourceId()
    {
        return function (): string {
            return $this->hasJsonApiContent('data.id')
                ? $this->input('data.id')
                : Str::of($this->path())->afterNext('api/v1/')->afterNext('/')->before('/');
        };
    }

    public function getResourceType()
    {
        return function (): string {
            return $this->hasJsonApiContent('data.type')
                ? $this->input('data.type')
                : Str::of($this->path())->afterNext('api/v1/')->before('/');
        };
    }

    public function hasJsonApiContent()
    {
        return function (?string $key = null): bool {
            return $this->header('Content-Type') === 'application/vnd.api+json' && (! $key || $this->filled($key));
        };
    }

    public function hasRelationships()
    {
        return function (?string $key = null): bool {
            return Arr::has($this->validatedData(), 'relationships'.($key ? ".{$key}" : ''));
        };
    }

    public function isJsonApi()
    {
        return function (): bool {
            return $this->header('Accept') === 'application/vnd.api+json';
        };
    }

    public function validatedData()
    {
        return function ($key = null, $default = null) {
            /** @var Request|FormRequest $this */
            $validated = method_exists($this, 'validated') ? $this->validated('data', []) : $this->input('data', []);

            return Arr::get($validated, $key, $default);
        };
    }
}
