<?php

namespace Laramate\Support\Data\Traits;

trait ObjectArrayAccess
{
    public function offsetExists($offset): bool 
    {
        return true;
    }

    public function offsetGet($offset) 
    {
        return $this->$offset ?? null;
    }

    public function offsetSet($offset, $value): void 
    {
        $this->$offset = $value;
    }

    public function offsetUnset($offset): void 
    {
        $this->$offset = null;
    }
}
