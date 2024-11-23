<?php

namespace App\Utils;

class InputValidator
{
    public static function hasValidLength(string $input, int $min, int $max): bool
    {
        $length = strlen($input);
        return $length >= $min && $length <= $max;
    }
}
