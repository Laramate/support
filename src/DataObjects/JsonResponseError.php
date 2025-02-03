<?php

namespace Laramate\Support\DataObjects;

use Laramate\Support\Traits\Makeable;

class JsonResponseError
{
    use Makeable;

    public function __construct(
        public ?string $message = null,
        public ?array $errors = null,
    ) {}

    public function toArray(): array
    {
        return
            collect([
                'message' => $this->message,
                'errors' => $this->errors,
            ])
                ->filter()
                ->toArray();
    }
}
