<?php

namespace App\Utils;

class UtilsSession
{
    public static function refreshPage(): void
    {
        header("Location: {$_SERVER['PHP_SELF']}");
        exit();
    }

    public static function redirectTo(string $urlPage): void
    {
        header("Location: $urlPage");
        exit();
    }
}
