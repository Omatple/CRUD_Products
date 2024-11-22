<?php

namespace App\Utils;

class ImageValidator
{

    public static function validateImageError(int $codeError): bool
    {
        return $codeError === 0;
    }

    private static function validateImageUpload(string $tmpName): bool
    {
        if (!is_uploaded_file($tmpName)) {
            $_SESSION["error_image"] = "Image is not upload file.";
            return false;
        }
        return true;
    }

    private static function validateImageTypeMime(string $type): bool
    {
        if (!in_array($type, Constants::VALIDATE_TYPE_MIME)) {
            $_SESSION["error_image"] = "This file is not a image.";
            return false;
        }
        return true;
    }

    private static function validateImageSize(int $size): bool
    {
        if ($size > Constants::MAX_SIZE_IMAGE) {
            $_SESSION["error_image"] = "Image can not up 2MB.";
            return false;
        }
        return true;
    }

    public static function validateImageMove(string $tmpPath, string $nameImage): string|false
    {
        if (!move_uploaded_file($tmpPath, $imagePath = self::getImageUniqidPath($nameImage))) {
            $_SESSION["error_image"] = "File can not move.";
            return false;
        }
        return $imagePath;
    }

    public static function deleteOldImage(string $oldImageName): void
    {
        if (basename($oldImageName) !== Constants::DEFAULT_IMAGE_NAME && file_exists(__DIR__ . "/../../public/" . $oldImageName))
            unlink(__DIR__ . "/../../public/" . $oldImageName);
    }

    private static function getImageUniqidPath(string $image): string
    {
        return __DIR__ . "/../../public/img/" . uniqid() . "-" . $image;
    }

    public static function validateImage(array $imageData): bool
    {
        return self::validateImageUpload($imageData["tmp_name"])
            && self::validateImageTypeMime($imageData["type"])
            && self::validateImageSize($imageData["size"]);
    }
}
