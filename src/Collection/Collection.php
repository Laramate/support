<?php

namespace Laramate\Support\Collection;

class Collection extends \Illuminate\Support\Collection
{
    public function __get($name)
    {
        return $this->get($name);
    }
}
