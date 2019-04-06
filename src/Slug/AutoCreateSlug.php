<?php

namespace Laramate\Support\Slug;

use Illuminate\Support\Str;

trait AutoCreateSlug
{
    /**
     * Boot auto create uuid trait.
     */
    public static function bootAutoCreateSlug()
    {
        // Auto populate uuid column on model creation
        static::creating(function ($model) {
            $slugColumn = $model->getSlugColumn();

            if (empty($model->$slugColumn)) {
                $model->$slugColumn = Str::slug($model->{$model->getSlugFromColumn()});
            }
        });
    }

    /**
     * Get the column name for slug attribute.
     *
     * @return string
     */
    public function getSlugColumn(): string
    {
        return ! empty($this->slug_column) ? $this->slug_column : 'slug';
    }

    /**
     * Get the column name for slug from attribute.
     *
     * @return string
     */
    public function getSlugFromColumn(): string
    {
        return ! empty($this->slug_from_column) ? $this->slug_from_column : 'title';
    }
}
