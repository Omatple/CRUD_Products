<?php

namespace App\Utils;

class UtilsValidator
{
    public static function validateLength(string $chain, int $minChars, int $maxChars): bool
    {
        return strlen($chain) >= $minChars && strlen($chain) <= $maxChars;
    }
}
