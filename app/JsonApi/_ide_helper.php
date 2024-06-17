<?php
/**
 * Don't import or include this file in any part of the application.
 * It's just a custom IDE helper to improve the autocompletion.
**/

namespace Illuminate\Database\Eloquent {
    use Illuminate\Contracts\Pagination\LengthAwarePaginator;

    class Builder extends \Illuminate\Database\Eloquent\Builder {
        private static \App\JsonApi\JsonApiEloquentBuilderMixin $mixin;
        /** @return static */ public function allowedIncludes(array $allowed = []) { return static::$mixin->allowedIncludes()($allowed); }
        /** @return static */ public function filterableBy(array $allowed = []) { return static::$mixin->filterableBy()($allowed); }
        /** @return string */ public function getResourceType() { return static::$mixin->getResourceType()(); }
        /** @return static|LengthAwarePaginator */ public function paginated(array|string|null $appends = null) { return static::$mixin->paginated()($appends); }
        /** @return static */ public function sortableBy(array $allowed = []) { return static::$mixin->sortableBy()($allowed); }
        /** @return static */ public function sparseFields(array $allowed = []) { return static::$mixin->sparseFields()($allowed); }
    }

    abstract class Model extends \Illuminate\Database\Eloquent\Model {
        private static Builder $builder;
        /** @return static */ public static function allowedIncludes(array $allowed = []) { return static::$builder->allowedIncludes($allowed); }
        /** @return static */ public static function filterableBy(array $allowed = []) { return static::$builder->filterableBy($allowed); }
        /** @return string */ public static function getResourceType() { return static::$builder->getResourceType(); }
        /** @return static|LengthAwarePaginator */ public static function paginated(array|string|null $appends = null) { return static::$builder->paginated($appends); }
        /** @return static */ public static function sortableBy(array $allowed = []) { return static::$builder->sortableBy($allowed); }
        /** @return static */ public static function sparseFields(array $allowed = []) { return static::$builder->sparseFields($allowed); }
    }
}

namespace Illuminate\Testing {
    use Illuminate\Database\Eloquent\Collection;
    use Illuminate\Database\Eloquent\Model;

    class TestResponse extends \Illuminate\Testing\TestResponse {
        private static \App\JsonApi\JsonApiTestResponseMixin $mixin;
        /** @return static */
        public function assertJsonApiCollection(Collection $collection, array $attributeKeys, array $missingKeys = [])
        { return static::$mixin->assertJsonApiCollection()($collection, $attributeKeys, $missingKeys); }
        /** @return static */
        public function assertJsonApiCollectionStructure(array $attributeKeys = [])
        { return static::$mixin->assertJsonApiCollectionStructure()($attributeKeys); }
        /** @return static */
        public function assertJsonApiError(?string $title = null, ?string $detail = null, ?int $status = null)
        { return static::$mixin->assertJsonApiError()($title, $detail, $status); }
        /** @return static */
        public function assertJsonApiHeaderContentType()
        { return static::$mixin->assertJsonApiHeaderContentType()(); }
        /** @return static */
        public function assertJsonApiHeaderLocation($model)
        { return static::$mixin->assertJsonApiHeaderLocation()($model); }
        /** @return static */
        public function assertJsonApiMissingAttributes(Model $model, array $attributeKeys)
        { return static::$mixin->assertJsonApiMissingAttributes()($model, $attributeKeys); }
        /** @return static */
        public function assertJsonApiResource(Model $model, array $attributeKeys = [], array $missingKeys = [])
        { return static::$mixin->assertJsonApiResource()($model, $attributeKeys, $missingKeys); }
        /** @return static */
        public function assertJsonApiResourceStructure(array $attributeKeys = [])
        { return static::$mixin->assertJsonApiResourceStructure()($attributeKeys); }
        /** @return static */
        public function assertJsonApiRelationshipLinks(Model $model, array $relationshipKeys)
        { return static::$mixin->assertJsonApiRelationshipLinks()($model, $relationshipKeys); }
        /** @return static */
        public function assertJsonApiValidationErrors(string $attribute, bool $raw = false)
        { return static::$mixin->assertJsonApiValidationErrors()($attribute, $raw); }
    }
}
