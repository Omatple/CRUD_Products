<?php

namespace App\Utils;

class ImageConstants
{
    public const IMAGE_MAX_SIZE = 2 * 1024 * 1024;
    public const ALLOWED_MIME_TYPES = ['image/gif', 'image/png', 'image/jpeg', 'image/bmp', 'image/webp'];
    public const DEFAULT_IMAGE_FILENAME = "default.png";
}
