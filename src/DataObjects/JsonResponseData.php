<?php

namespace Laramate\Support\DataObjects;

use Laramate\Support\Traits\Makeable;

class JsonResponseData
{
    use Makeable;

    public function __construct(
        public ?string $message = null,
        public ?array $data = null,
    ) {}

    public function toArray(): array
    {
        return
            collect([
                'message' => $this->message,
                'data' => $this->data,
            ])
                ->filter()
                ->toArray();
    }
}
