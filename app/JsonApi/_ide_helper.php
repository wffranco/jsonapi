<?php
/**
 * Don't import or include this file in any part of the application.
 * It's just a custom IDE helper to improve the autocompletion.
**/

namespace Illuminate\Database\Eloquent {
    use Illuminate\Contracts\Pagination\LengthAwarePaginator;

    class Builder extends \Illuminate\Database\Eloquent\Builder {
        private static \App\JsonApi\JsonApiEloquentBuilderMixin $mixin;
        /** @return static */ public function filterableBy(array $allowed = []) { return static::$mixin->filterableBy()($allowed); }
        /** @return static|LengthAwarePaginator */ public function paginated(array|string|null $appends = null) { return static::$mixin->paginated()($appends); }
        /** @return static */ public function sortableBy(array $allowed = []) { return static::$mixin->sortableBy()($allowed); }
        /** @return static */ public function sparseFields(array $allowed = []) { return static::$mixin->sparseFields()($allowed); }
    }

    abstract class Model extends \Illuminate\Database\Eloquent\Model {
        private static Builder $builder;
        /** @return static */ public static function filterableBy(array $allowed = []) { return static::$builder->filterableBy($allowed); }
        /** @return static|LengthAwarePaginator */ public static function paginated(array|string|null $appends = null) { return static::$builder->paginated($appends); }
        /** @return static */ public static function sortableBy(array $allowed = []) { return static::$builder->sortableBy($allowed); }
        /** @return static */ public static function sparseFields(array $allowed = []) { return static::$builder->sparseFields($allowed); }
    }
}

namespace Illuminate\Testing {
    class TestResponse extends \Illuminate\Testing\TestResponse {
        private static \App\JsonApi\JsonApiTestResponseMixin $mixin;
        public function assertJsonApiValidationErrors(string $attribute) { return static::$mixin->assertJsonApiValidationErrors()($attribute); }
    }
}
