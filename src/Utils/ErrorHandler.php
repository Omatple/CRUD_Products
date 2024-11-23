<?php

namespace App\Utils;

class ErrorHandler
{
    public static function displayError(string $key): void
    {
        if (isset($_SESSION["error_$key"])) {
            echo "<p class='text-xs font-bold text-red-700'>{$_SESSION["error_$key"]}</p>";
            unset($_SESSION["error_$key"]);
        }
    }
}
