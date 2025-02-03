<?php

namespace Laramate\Support\NumberingFormatter;

use Laramate\Support\NumberingFormatter\Interfaces\NumberingFormatterInterface;

class NaturalNumbers implements NumberingFormatterInterface
{
    public static function format(int $position, int $trailing = 0): string
    {
        $value = $position + 1;

        return $trailing
            ? str_pad($value, $trailing + 1, '0', STR_PAD_LEFT)
            : $value;
    }
}
