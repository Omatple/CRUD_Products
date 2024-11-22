<?php

namespace App\Utils;

use App\Database\Product;

require __DIR__ . "/../../vendor/autoload.php";

class ProductValidator
{
    public static function sanitizeInput(string $input): string
    {
        return htmlspecialchars(trim($input));
    }

    public static function validateNameLength(string $name): bool
    {
        $minChars = 3;
        $maxChars = 60;
        if (!UtilsValidator::validateLength($name, $minChars, $maxChars)) {
            $_SESSION["error_name"] = "Name need beetwen $minChars and $maxChars.";
            return false;
        }
        return true;
    }

    public static function validateDescriptionLength(string $description): bool
    {
        $minChars = 0;
        $maxChars = 200;
        if (!UtilsValidator::validateLength($description, $minChars, $maxChars)) {
            $_SESSION["error_description"] = "Description need beetwen $minChars and $maxChars.";
            return false;
        }
        return true;
    }

    public static function validateStockRange(int $stock): bool
    {
        $minRange = 0;
        $maxRange = 100;
        if ($stock < $minRange || $stock > $maxRange) {
            $_SESSION["error_stock"] = "Stock range need beetwen $minRange and $maxRange.";
            return false;
        }
        return true;
    }

    public static function validateType(string $type): bool
    {
        if (!in_array($type, array_map(fn($type) => $type->toString(), TypeProduct::cases()))) {
            $_SESSION["error_type"] = "Select a valid type.";
            return false;
        }
        return true;
    }

    public static function isNameTaken(string $name, ?string $id = null): bool
    {
        if (!Product::isAttributeTaken("name", $name, $id)) {
            $_SESSION['error_name'] = "This name of product already exists.";
            return false;
        }
        return true;
    }
}
