<?php
/**
 * Don't import or include this file in any part of the application.
 * It's just a custom IDE helper to improve the autocompletion.
**/

namespace Illuminate\Contracts\Auth {
    /**
     * @method static \App\Models\AppUser|null user()
     *
     * @extends \Illuminate\Contracts\Auth\Guard
     */
    interface Guard {}
}

namespace Illuminate\Database\Eloquent {
    use Illuminate\Contracts\Database\Query\Expression;

    /**
     * @mixin \App\Models\Concerns\Builder
     * @mixin \Illuminate\Database\Eloquent\Builder
     */
    abstract class Model extends \Illuminate\Database\Eloquent\Model {
        private static \Illuminate\Database\Eloquent\Builder $builder;
        /** @return static */ public static function create(array $attributes = []) { return static::$builder->create($attributes); }
        /** @return static */ public static function find(mixed $id, array $columns = ['*']) { return static::$builder->find($id, $columns); }
        /** @return static */ public static function findOrFail(mixed $id, array $columns = ['*']) { return static::$builder->findOrFail($id, $columns); }
        /** @return static */ public static function first(array|string $columns = ['*']) { return static::$builder->first($columns); }
        /** @return static */ public static function orderBy(string $column, string $direction = 'asc') { return static::$builder->orderBy($column, $direction); }
        /** @return static */ public static function query() { return parent::query(); }
        /** @return static */ public static function truncate() { return static::$builder->truncate(); }
        /** @return static */ public static function where(string|array $column, mixed $operator = null, mixed $value = null, string $boolean = 'and')
        { return static::$builder->where($column, $operator, $value, $boolean); }
        /** @return static */ public static function whereIn(string|Expression $column, $values, string $boolean = 'and', bool $not = false)
        { return static::$builder->whereIn($column, $values, $boolean, $not); }
    }
}

namespace Illuminate\Http {
    /**
     * @method \App\Models\AppUser|null user()
     * @mixin \Illuminate\Http\Request
     */
    class Request {}
}
