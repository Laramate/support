<?php

namespace Laramate\Support\Mapper;

use Illuminate\Support\Str;

abstract class Mapper
{
    protected array $attributes = [];

    protected array $map = [];

    protected array $defaults = [];

    public function __construct(
        protected array $data
    ) {}

    /**
     * Get the defined attributes.
     */
    public function attributes(): array
    {
        return $this->attributes;
    }

    /**
     * Get the defined map.
     */
    public function map(): array
    {
        return $this->map;
    }

    /**
     * Get the defined defaults.
     */
    public function defaults(): array
    {
        return $this->defaults;
    }

    /**
     * Convert the input data using the mapper and the defaults.
     */
    public function convert(): array
    {
        foreach ($this->attributes() as $attribute) {
            // Get the value by using the mapper
            $value = $this->mapAttribute($attribute);

            // Get the default value, if value wasn't mapped
            if (is_null($value) && $this->hasDefault($attribute)) {
                $value = $this->getDefault($attribute);
            }

            $result[$attribute] = $value;
        }

        return $result ?? [];
    }

    /**
     * Map a single attribute.
     */
    protected function mapAttribute(string|int $attribute): mixed
    {
        if ($this->hasMapMethod($attribute)) {
            return $this->useMapMethod($attribute);
        }

        if ($this->hasNameMap($attribute)) {
            return $this->useNameMap($attribute);
        }

        if (array_key_exists($attribute, $this->data)) {
            return $this->data[$attribute];
        }

        return null;
    }

    /**
     * Determinate, if a map method is defined.
     */
    protected function hasMapMethod(string|int $attribute): bool
    {
        $methodName = $this->composeMapMethodName($attribute);

        return method_exists($this, $methodName);
    }

    /**
     * Use the map method to get the mapped attribute value.
     */
    protected function useMapMethod(string|int $attribute): mixed
    {
        $methodName = $this->composeMapMethodName($attribute);

        return $this->{$methodName}();
    }

    /**
     * Compose the map method name.
     */
    protected function composeMapMethodName(string|int $attribute): string
    {
        $attribute = Str::of($attribute)->camel()->ucfirst();

        return "map{$attribute}";
    }

    /**
     * Determinate, if a simple name map is defined.
     */
    protected function hasNameMap(string|int $attribute): bool
    {
        return array_key_exists($attribute, $this->map());
    }

    /**
     * Use the simple name map to get the mapped attribute value.
     */
    protected function useNameMap(string|int $attribute): mixed
    {
        $key = $this->map()[$attribute];

        return $this->data[$key] ?? null;
    }

    /**
     * Determinate, if a default value is defined.
     */
    protected function hasDefault(string|int $attribute): bool
    {
        return $this->hasDefaultMap($attribute) || $this->hasDefaultMethod($attribute);
    }

    /**
     * Get the default value.
     */
    protected function getDefault(string|int $attribute): mixed
    {
        if ($this->hasDefaultMethod($attribute)) {
            return $this->useDefaultMethod($attribute);
        }

        if ($this->hasDefaultMap($attribute)) {
            return $this->defaults()[$attribute];
        }

        return null;
    }

    /**
     * Determinate, if a simple default value is defined.
     */
    protected function hasDefaultMap(string|int $attribute): bool
    {
        return array_key_exists($attribute, $this->defaults());
    }

    /**
     * Determinate, if a simple default method is defined.
     */
    protected function hasDefaultMethod(string|int $attribute): bool
    {
        $methodName = $this->composeDefaultMethodName($attribute);

        return method_exists($this, $methodName);
    }

    /**
     * Compose the default method name.
     */
    protected function composeDefaultMethodName(string|int $attribute): string
    {
        $attribute = Str::of($attribute)->camel()->ucfirst();

        return "default{$attribute}";
    }

    /**
     * Use the default method.
     */
    protected function useDefaultMethod(string|int $attribute): mixed
    {
        $methodName = $this->composeDefaultMethodName($attribute);

        return $this->{$methodName}();
    }
}