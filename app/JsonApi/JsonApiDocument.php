<?php

namespace App\JsonApi;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class JsonApiDocument extends Dot\Collection
{
    protected ?Collection $collection = null;

    protected ?Model $model = null;

    /** @param array|Model|Collection $items */
    public function __construct($items = [], ?Closure $onModel = null)
    {
        parent::__construct(is_array($items) ? $items : ['data' => []]);
        if ($items instanceof Collection) {
            $this->collection = $items;
            // format a collection of models
            $this->put('data', $items->map(fn ($item) => static::make($item, $onModel)->get('data'))->all());
        } elseif ($items instanceof Model) {
            $this->model = $items;
            // format a model
            $this->type()->id()->apply($onModel);
        }
    }

    /** @param array|Model|Collection $items */
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
        $id = $id ?: $this->model?->getRouteKey();

        return $this->put('data.id', is_null($id) ? null : (string) $id);
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

    public function links(?array $links = null, string $prefix = 'data'): static
    {
        $prefix .= $prefix ? '.' : '';
        if (! empty($links)) {
            $this->put("{$prefix}links", $links);
        }
        if ($this->isEmpty("{$prefix}links.self") && $this->has("{$prefix}type") && $this->has("{$prefix}id")) {
            $this->put("{$prefix}links.self", route('api.v1.'.$this->get("{$prefix}type").'.show', $this->get("{$prefix}id")));
        }

        return $this;
    }

    public function relationshipData(?array $relationships = null, ?callable $onModel = null): static
    {
        if (is_null($relationships)) {
            $relationships = $this?->model?->getRelations() ?? [];
        }
        foreach (Arr::undot($relationships) as $type => $relationship) {
            $this->put("data.relationships.$type", match (true) {
                $relationship instanceof Collection => $relationship->map(fn ($model) => static::make($model, $onModel))->all(),
                $relationship instanceof Model => static::make($relationship, $onModel)->all(),
                default => $relationship,
            });
        }

        return $this;
    }

    public function relationshipsLinks(?array $relationships = null): static
    {
        if (is_null($relationships)) {
            $relationships = array_keys($this->get('data.relationships'));
        }
        foreach ($relationships as $type) {
            $this->unfilled('data.relationships.'.$type.'.links.self', route('api.v1.'.$this->get('data.type').'.relationships.'.$type, $this->get('data.id')));
            $this->unfilled('data.relationships.'.$type.'.links.related', route('api.v1.'.$this->get('data.type').'.'.$type, $this->get('data.id')));
        }

        return $this;
    }

    public function apply(?callable $callback = null)
    {
        if ($callback instanceof Closure) {
            $callback($this);
        }

        return $this;
    }
}
