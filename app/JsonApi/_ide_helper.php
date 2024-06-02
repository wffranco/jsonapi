<?php
/**
 * Don't import or include this file in any part of the application.
 * It's just a custom IDE helper to improve the autocompletion.
**/

namespace Illuminate\Database\Eloquent {
    /**
     * @see \App\JsonApi\ServiceProvider
     */
    class Builder extends \Illuminate\Database\Eloquent\Builder {
        public function sortableBy(array $allowed = []) { return $this; }
    }

    abstract class Model extends \Illuminate\Database\Eloquent\Model {
        /** @var \Illuminate\Database\Eloquent\Builder $builder */ private $builder;
        /** @return static */ public static function sortableBy(array $allowed = []) { return static::$builder->sortableBy($allowed); }
    }
}

namespace Illuminate\Testing {
    /**
     * @see \App\JsonApi\Tests\MakesJsonApiRequests
     *
     * @method static assertJsonApiValidationErrors(string $attribute)
     */
    class TestResponse {}
}
