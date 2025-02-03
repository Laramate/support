<?php

namespace Laramate\Support\Enum;

use Laramate\Support\Enum\Interfaces\TranslatableEnum;

trait Enum
{
    public static function asSelectArray(): array
    {
        foreach (static::cases() as $case) {
            $result[$case->value] = is_a(self::class, TranslatableEnum::class, true)
                ? $case->translatedValue()
                : $case->value;
        }

        return $result ?? [];
    }

    public function translatedValue(): string
    {
        $key = sprintf(static::getTranslationKey(), get_called_class(), $this->value);

        return trans($key);
    }

    public static function getTranslationKey(): string
    {
        return 'enums.%s.%s';
    }
}
