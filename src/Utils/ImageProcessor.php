<?php

namespace App\Utils;

class ImageProcessor
{
    public static function isUploadSuccessful(int $errorCode): bool
    {
        return $errorCode === 0;
    }

    private static function isUploadedFileValid(string $tmpName): bool
    {
        if (!is_uploaded_file($tmpName)) {
            $_SESSION["error_image"] = "The file was not uploaded correctly.";
            return false;
        }
        return true;
    }

    private static function isMimeTypeAllowed(string $mimeType): bool
    {
        if (!in_array($mimeType, ImageConstants::ALLOWED_MIME_TYPES)) {
            $_SESSION["error_image"] = "Invalid file type. Please upload an image.";
            return false;
        }
        return true;
    }

    private static function isFileSizeWithinLimit(int $fileSize): bool
    {
        if ($fileSize > ImageConstants::IMAGE_MAX_SIZE) {
            $_SESSION["error_image"] = "The image size exceeds the 2MB limit.";
            return false;
        }
        return true;
    }

    public static function moveUploadedFile(string $tmpPath, string $imageName): string|false
    {
        $destinationPath = self::generateUniqueImagePath($imageName);
        if (!move_uploaded_file($tmpPath, $destinationPath)) {
            $_SESSION["error_image"] = "Failed to move the uploaded file.";
            return false;
        }
        return $destinationPath;
    }

    public static function deletePreviousImage(string $imageName): void
    {
        $imagePath = __DIR__ . "/../../public/" . $imageName;
        if (basename($imageName) !== ImageConstants::DEFAULT_IMAGE_FILENAME && file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    private static function generateUniqueImagePath(string $imageName): string
    {
        return __DIR__ . "/../../public/img/" . uniqid() . "-" . $imageName;
    }

    public static function validateImageData(array $imageData): bool
    {
        return self::isUploadedFileValid($imageData["tmp_name"])
            && self::isMimeTypeAllowed($imageData["type"])
            && self::isFileSizeWithinLimit($imageData["size"]);
    }
}
