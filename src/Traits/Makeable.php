<?php

namespace Laramate\Support\Traits;

trait Makeable
{
    /**
     * Create a new instance of the given class.
     *
     * @param  array  $parameters
     */
    public static function make(...$parameters): static
    {
        return new static(...$parameters);
    }
}
