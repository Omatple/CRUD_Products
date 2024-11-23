<?php

namespace App\Utils;

use App\Database\Product;

require __DIR__ . "/../../vendor/autoload.php";

class ProductInputValidator
{
    public static function sanitize(string $input): string
    {
        return htmlspecialchars(trim($input));
    }

    public static function isNameLengthValid(string $name): bool
    {
        $minLength = 3;
        $maxLength = 60;
        if (!InputValidator::hasValidLength($name, $minLength, $maxLength)) {
            $_SESSION["error_name"] = "The product name must be between $minLength and $maxLength characters.";
            return false;
        }
        return true;
    }

    public static function isDescriptionLengthValid(string $description): bool
    {
        $maxLength = 200;
        if (!InputValidator::hasValidLength($description, 0, $maxLength)) {
            $_SESSION["error_description"] = "The description must not exceed $maxLength characters.";
            return false;
        }
        return true;
    }

    public static function isStockInRange(int $stock): bool
    {
        $minStock = 0;
        $maxStock = 100;
        if ($stock < $minStock || $stock > $maxStock) {
            $_SESSION["error_stock"] = "The stock must be between $minStock and $maxStock.";
            return false;
        }
        return true;
    }

    public static function isValidType(string $type): bool
    {
        if (!in_array($type, array_map(fn($type) => $type->toString(), TypeProduct::cases()))) {
            $_SESSION["error_type"] = "Please select a valid product type.";
            return false;
        }
        return true;
    }

    public static function isNameUnique(string $name, ?string $id = null): bool
    {
        if (!Product::isFieldUnique("name", $name, $id)) {
            $_SESSION['error_name'] = "This product name is already in use.";
            return false;
        }
        return true;
    }
}
