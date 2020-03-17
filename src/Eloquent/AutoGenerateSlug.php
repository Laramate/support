<?php

namespace Laramate\Support\Slug;

use Illuminate\Support\Str;

trait AutoGenerateSlug
{
    /**
     * Boot auto create uuid trait.
     */
    public static function bootAutoGenerateSlug()
    {
        static::creating(function ($model) {
            $model->generateSlugIfEmpty();
        });
    }

    /**
     * Generate Uuid, if empty.
     *
     * @return string
     */
    public function generateSlugIfEmpty(): string
    {
        $field = $this->autoGenerateSlugField();
        $from = $this->autoGenerateSlugFromField();

        return $this->$field = $this->$field ?? (string) Str::slug($this->$from);
    }

    /**
     * Get the column name for slug attribute.
     *
     * @return string
     */
    public function autoGenerateSlugField(): string
    {
        return $this->auto_slug_field ?? 'slug';
    }

    /**
     * Get the column name for slug from attribute.
     *
     * @return string
     */
    public function autoGenerateSlugFromField(): string
    {
        return $this->auto_slug_from ?? 'title';
    }
}
