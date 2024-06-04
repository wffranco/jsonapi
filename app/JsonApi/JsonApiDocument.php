<?php

namespace App\JsonApi;

use Closure;
use Illuminate\Database\Eloquent\Model;

class JsonApiDocument extends Dot\Collection
{
    protected ?Model $model = null;

    /** @param array|Model $items */
    public function __construct($items = [], ?Closure $onModel = null)
    {
        parent::__construct(is_array($items) ? $items : ['data' => []]);
        if ($items instanceof Model) {
            $this->model = $items;
            // format a model
            $this->type()->id()->apply($onModel);
        }
    }

    /** @param array|Model $items */
    public static function make($items = [], ?Closure $onModel = null): static
    {
        return new static($items, $onModel);
    }

    public function type(?string $type = null): static
    {
        return $this->put('data.type', $type ?? $this->model?->getResourceType());
    }

    public function id(?string $id = null): static
    {
        return $this->put('data.id', $id ?? $this->model?->getRouteKey());
    }

    /**
     * @param  null|array<string>|array<string, string>  $attributes
     * @param  bool  $filter  Remove empty values from the attributes.
     */
    public function attributes(?array $attributes = null, bool $filter = true): static
    {
        if ($this->model) {
            if (! $attributes) {
                $keys = $this->model?->jsonApiAttributeKeys ?? ($this->model->getVisible() ?: $this->model->getFillable());
                $attributes = $keys ?: $this->model->getAttributes() ?: null;
            }
            if ($attributes) {
                if (array_is_list($attributes)) {
                    $attributes = $this->model->only($attributes);
                }
                $attributes = collect($attributes)->except($this->model->getHidden())->all();
            }
        } elseif ($attributes && array_is_list($attributes)) {
            throw new \InvalidArgumentException('Invalid attributes format');
        }

        return $this->put('data.attributes', $filter ? array_filter($attributes) : $attributes);
    }

    public function links(array $links, string $field = 'data.links'): static
    {
        return $this->put($field, $links);
    }

    public function apply(?callable $callback = null)
    {
        if ($callback instanceof Closure) {
            $callback($this);
        }

        return $this;
    }
}
