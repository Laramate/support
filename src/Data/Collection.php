<?php

namespace Laramate\Support\Data;

class Collection extends \Illuminate\Support\Collection
{
    public function __get($name)
    {
        return $this->get($name);
    }
    
    public function __set($name, $value)
    {
        $this->put($name, $value);
    }
}
