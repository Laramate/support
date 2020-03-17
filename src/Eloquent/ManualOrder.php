<?php

namespace Laramate\Support\Eloquent;

use Illuminate\Database\Eloquent\Builder;

trait ManualOrder
{
    /**
     * Boot auto create uuid trait.
     */
    public static function bootManualOrder()
    {
        static::addGlobalScope('manual_order', function (Builder $query) {
            $query->orderBy($this->manualOrderField(), 'asc');
        });
    }

    /**
     * Get the column name for slug attribute.
     *
     * @return string
     */
    protected function manualOrderField(): string
    {
        return $this->manual_order_field ?? 'order_number';
    }
}
