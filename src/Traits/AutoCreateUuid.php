<?php

namespace Laramate\Support\Traits;

use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

trait AutoCreateUuid
{
    /**
     * Boot auto create uuid trait.
     */
    public static function bootAutoCreateUuid()
    {
        // Auto populate uuid column on model creation
        static::creating(function ($model) {
            if (empty($model->{$model->getUuidColumn()}) || ! Uuid::isValid($model->{$model->getUuidColumn()})) {
                $model->renewUuid();
            }
        });
    }

    /**
     * Get the column name for uuid attribute.
     */
    public function getUuidColumn(): string
    {
        return ! empty($this->uuid_column) ? $this->uuid_column : 'uuid';
    }

    /**
     * Renew uuid attribute.
     */
    public function renewUuid(): static
    {
        $this->{$this->getUuidColumn()} = Str::uuid()->toString();

        return $this;
    }
}
