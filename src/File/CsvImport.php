<?php

namespace Laramate\Support\File;

use Laramate\Support\Traits\Makeable;
use SplFileObject;

class CsvImport
{
    use Makeable;

    protected ?array $keys;

    public function __construct(
        protected string $uri,
        protected $first_line_as_keys = false,
        protected string $separator = ',',
        protected string $enclosure = '"',
        protected string $escape = '\\',
    ) {}

    public function handle(): array
    {
        $result = [];
        $file = new SplFileObject($this->uri);

        if ($this->first_line_as_keys) {
            $this->keys = $this->getLineAsArray($file);
        }

        while (! $file->eof()) {
            $line = $file->fgetcsv();

            $line = collect($line)
                ->mapWithKeys(function ($item, $index) {
                    $key = $this->keys ? $this->keys[$index] : $index;

                    $item = $item == 'NULL' ? null : $item;

                    return [$key => $item];

                })
                ->toArray();

            $result[] = $this->keys
                ? collect($this->keys)->flip()->map(fn () => null)->merge($line)->toArray()
                : $line;
        }

        return $result ?? [];
    }

    protected function getLineAsArray(SplFileObject &$file): array
    {
        return $file->fgetcsv($this->separator, $this->enclosure, $this->escape);
    }
}
