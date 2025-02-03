<?php

namespace Laramate\Support\NumberingFormatter\Interfaces;

interface NumberingFormatterInterface
{
    public static function format(int $position, int $trailing = 0): string;
}
