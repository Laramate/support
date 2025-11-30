<?php

namespace Laramate\Support\Version\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait Versioning
{
    public function getVersioningIdColumn(): string
    {
        return $this->versioning['id_column'] ?? 'version_id';
    }

    public function getVersioningLabelColumn(): string
    {
        return $this->versioning['label_column'] ?? 'version_label';
    }

    public function getVersionAuthorColumn(): string
    {
        return $this->versioning['author_column'] ?? 'version_author_id';
    }

    public function generateVersionId(): string
    {
        return uniqid();
    }

    protected static function bootVersioning(): void
    {
        static::creating(function ($model) {
            $versionIdColumn = $model->getVersioningIdColumn();
            $versionLabelColumn = $model->getVersioningLabelColumn();
            $model->{$versionIdColumn} = $model->{$versionIdColumn} ?? $model->generateVersionId();
            $model->{$versionLabelColumn} = $model->{$versionLabelColumn} ?? __('Initial Version');
        });
    }

    public function newVersion(?string $label = null): self
    {
        return tap($this->replicate(), function ($version) use ($label) {
            $versionLabelColumn = $this->getVersioningLabelColumn();
            $versionAuthorColumn = $this->getVersionAuthorColumn();

            $createdAt = now()->setMicro(0);
            $latestVersionAt = $this->latestVersion()->created_at;

            // to avoid duplicate version error
            if (! $createdAt->gt($latestVersionAt)) {
                $createdAt = $latestVersionAt->addSecond();
            }

            $version->created_at = $createdAt;
            $version->updated_at = $createdAt;
            $version->{$versionLabelColumn} = $label;
            $version->{$versionAuthorColumn} = Auth::id();

            if (method_exists($version, 'renewUuid')) {
                $version->renewUuid();
            }

            $version->saveQuietly();
        });
    }

    public function scopeVersioned(Builder $query): Builder
    {
        $versionIdColumn = $this->getVersioningIdColumn();
        $table = $this->getTable();

        return $query
            ->select("{$table}.*")
            ->joinSub(function ($query) use ($versionIdColumn, $table) {
                $query->select($versionIdColumn, DB::raw('MAX(created_at) as latest_created_at'))
                    ->from($table)
                    ->groupBy($versionIdColumn);
            }, 'latest_items', function ($join) use ($table, $versionIdColumn) {
                $join->on("{$table}.{$versionIdColumn}", '=', "latest_items.{$versionIdColumn}")
                    ->on("{$table}.created_at", '=', 'latest_items.latest_created_at');
            });
    }

    public function latestVersion(): static
    {
        return $this->versions->first();
    }

    public function versions(): HasMany
    {
        return $this
            ->hasMany(static::class, 'version_id', 'version_id')
            ->orderBy('created_at', 'desc');
    }

    public function resetVersion(): static
    {
        $versionIdColumn = $this->getVersioningIdColumn();
        $versionLabelColumn = $this->getVersioningLabelColumn();

        $this->{$versionIdColumn} = $this->generateVersionId();
        $this->{$versionLabelColumn} = __('Initial Version');

        return $this;
    }
}
