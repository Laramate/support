<?php

namespace Laramate\Support\Mapper;

use BackedEnum;
use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Traversable;

abstract class Mapper
{
    protected array $attributes = [];

    protected array $map = [];

    protected array $defaults = [];

    protected array $casts = [];

    protected array $data = [];

    /**
     * Normalize the input data to an array. Accepts arrays, Arrayables
     * (e.g. Eloquent models, collections), Traversables and plain
     * objects like stdClass (converted recursively).
     */
    protected function normalizeData(array|object $data): array
    {
        return match (true) {
            is_array($data) => $data,
            $data instanceof Arrayable => $data->toArray(),
            $data instanceof Traversable => iterator_to_array($data),
            default => json_decode(json_encode($data), true),
        };
    }

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
     * Get the defined casts.
     */
    public function casts(): array
    {
        return $this->casts;
    }

    /**
     * Get the input data.
     */
    public function data(): array
    {
        return $this->data;
    }

    /**
     * Get a value from the input data. Supports dot notation.
     */
    protected function get(string $key, mixed $default = null): mixed
    {
        return Arr::get($this->data, $key, $default);
    }

    /**
     * Convert the given input data using the map, the defaults and the casts.
     */
    public static function convert(array|object $data): array
    {
        $mapper = new static;

        $mapper->data = $mapper->normalizeData($data);

        $result = [];

        foreach ($mapper->attributes() as $attribute) {
            $result[$attribute] = $mapper->castAttribute(
                $attribute,
                $mapper->resolveValue($attribute)
            );
        }

        return $result;
    }

    /**
     * Convert multiple input data rows.
     */
    public static function convertMany(iterable $rows): array
    {
        $result = [];

        foreach ($rows as $key => $row) {
            $result[$key] = static::convert($row);
        }

        return $result;
    }

    /**
     * Resolve the value for a single attribute. The default is only
     * used if the attribute could not be mapped at all. An explicit
     * null value in the input data is kept as null.
     */
    protected function resolveValue(string $attribute): mixed
    {
        if ($this->hasMapMethod($attribute)) {
            return $this->useMapMethod($attribute);
        }

        if ($this->hasNameMap($attribute)) {
            return $this->useNameMap($attribute);
        }

        if (Arr::has($this->data, $attribute)) {
            return $this->get($attribute);
        }

        return $this->getDefault($attribute);
    }

    /**
     * Determinate, if a map method is defined.
     */
    protected function hasMapMethod(string $attribute): bool
    {
        return method_exists($this, $this->composeMethodName('map', $attribute));
    }

    /**
     * Use the map method to get the mapped attribute value.
     */
    protected function useMapMethod(string $attribute): mixed
    {
        return $this->{$this->composeMethodName('map', $attribute)}();
    }

    /**
     * Determinate, if a simple name map is defined.
     */
    protected function hasNameMap(string $attribute): bool
    {
        return array_key_exists($attribute, $this->map());
    }

    /**
     * Use the name map to get the mapped attribute value. The map value
     * may be a data key (dot notation supported) or a closure that
     * receives the input data.
     */
    protected function useNameMap(string $attribute): mixed
    {
        $mapped = $this->map()[$attribute];

        if ($mapped instanceof Closure) {
            return $mapped($this->data);
        }

        if (Arr::has($this->data, $mapped)) {
            return $this->get($mapped);
        }

        return $this->getDefault($attribute);
    }

    /**
     * Determinate, if a default value is defined.
     */
    protected function hasDefault(string $attribute): bool
    {
        return $this->hasDefaultMap($attribute) || $this->hasDefaultMethod($attribute);
    }

    /**
     * Get the default value.
     */
    protected function getDefault(string $attribute): mixed
    {
        if ($this->hasDefaultMethod($attribute)) {
            return $this->{$this->composeMethodName('default', $attribute)}();
        }

        if ($this->hasDefaultMap($attribute)) {
            $default = $this->defaults()[$attribute];

            return $default instanceof Closure ? $default($this->data) : $default;
        }

        return null;
    }

    /**
     * Determinate, if a simple default value is defined.
     */
    protected function hasDefaultMap(string $attribute): bool
    {
        return array_key_exists($attribute, $this->defaults());
    }

    /**
     * Determinate, if a default method is defined.
     */
    protected function hasDefaultMethod(string $attribute): bool
    {
        return method_exists($this, $this->composeMethodName('default', $attribute));
    }

    /**
     * Cast the resolved attribute value. Null values are never cast.
     */
    protected function castAttribute(string $attribute, mixed $value): mixed
    {
        if (is_null($value) || ! array_key_exists($attribute, $this->casts())) {
            return $value;
        }

        $cast = $this->casts()[$attribute];

        if ($cast instanceof Closure) {
            return $cast($value);
        }

        if (is_subclass_of($cast, BackedEnum::class)) {
            return $value instanceof $cast ? $value : $cast::from($value);
        }

        return match ($cast) {
            'int', 'integer' => (int) $value,
            'float', 'double' => (float) $value,
            'bool', 'boolean' => filter_var($value, FILTER_VALIDATE_BOOL),
            'string' => (string) $value,
            'array' => (array) $value,
            'date' => Carbon::parse($value)->startOfDay(),
            'datetime' => Carbon::parse($value),
            default => throw new InvalidArgumentException("Invalid cast [{$cast}] for attribute [{$attribute}]."),
        };
    }

    /**
     * Compose a prefixed method name for the given attribute.
     * Dots are treated like underscores: "user.name" => "mapUserName".
     */
    protected function composeMethodName(string $prefix, string $attribute): string
    {
        return $prefix.Str::of($attribute)->replace('.', '_')->studly();
    }
}
