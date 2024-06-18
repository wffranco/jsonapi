<?php

namespace App\JsonApi;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

trait JsonApiAuthorize
{
    public function authorize(string $ability, $arguments = []): void
    {
        if ($arguments instanceof Collection) {
            $arguments->each(fn ($argument) => $this->authorize($ability, $argument));
        } elseif (is_iterable($arguments)) {
            foreach ($arguments as $argument) {
                $this->authorize($ability, $argument);
            }
        } else {
            Gate::authorize($ability, $arguments);
        }
    }
}
