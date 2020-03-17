<?php

namespace Laramate\Support\Eloquent;

use Illuminate\Support\Str;

trait AutoGenerateUuid
{
    /**
     * Boot auto create uuid trait.
     */
    public static function bootAutoGenerateUuid()
    {
        static::creating(function ($model) {
            $model->generateUuidIfEmpty();
        });
    }

    /**
     * Generate Uuid, if empty.
     *
     * @return string
     */
    public function generateUuidIfEmpty(): string
    {
        $key = $this->autoGenerateUuidField();

        return $this->$key = $this->$key ?? (string) Str::uuid();
    }

    /**
     * Get the column name for slug attribute.
     *
     * @return string
     */
    protected function autoGenerateUuidField(): string
    {
        return $this->auto_create_uuid ?? 'uuid';
    }
}
