<?php

namespace App\Utils;

class DisplayError
{
    public static function errorDisplay(string $error)
    {
        if (isset($_SESSION["error_$error"])) {
            echo "<p class='text-xs bold text-red-700'>{$_SESSION["error_$error"]}</p>";
            unset($_SESSION["error_$error"]);
        }
    }
}
