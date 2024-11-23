<?php

namespace App\Utils;

class Navigation
{
    public static function reloadPage(): void
    {
        header("Location: {$_SERVER['PHP_SELF']}");
        exit();
    }

    public static function redirectToUrl(string $url): void
    {
        header("Location: $url");
        exit();
    }
}
