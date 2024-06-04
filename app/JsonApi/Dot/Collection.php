<?php

namespace App\JsonApi\Dot;

use Illuminate\Support\Arr;

/**
 * A collection that allows you to handle values using dot notation.
 */
class Collection extends \Illuminate\Support\Collection
{
    public function get($key, $default = null)
    {
        if (Arr::has($this->items, $key)) {
            return Arr::get($this->items, $key);
        }

        return value($default);
    }

    public function has($key): bool
    {
        $keys = is_array($key) ? $key : func_get_args();

        foreach ($keys as $value) {
            if (! Arr::has($this->items, $value)) {
                return false;
            }
        }

        return true;
    }

    public function isEmpty($key = null): bool
    {
        return is_null($key)
            ? parent::isEmpty()
            : empty($this->get($key));
    }

    public function offsetSet($key, $value): void
    {
        if (is_null($key)) {
            $this->items[] = $value;
        } elseif (is_null($value)) {
            $this->offsetUnset($key);
        } else {
            Arr::set($this->items, $key, $value);
        }
    }

    public function offsetUnset($key): void
    {
        Arr::forget($this->items, $key);
    }

    /**
     * If the given key is not already filled, set the given value.
     */
    public function unfilled($key, $value): static
    {
        if (! Arr::has($this->items, $key)) {
            $this->offsetSet($key, $value);
        }

        return $this;
    }
}
