<?php

use App\Database\Product;
use App\Utils\Constants;
use App\Utils\DisplayError;
use App\Utils\ImageValidator;
use App\Utils\ProductValidator;
use App\Utils\TypeProduct;
use App\Utils\UtilsSession;
use App\Utils\UtilsValidator;
use Faker\Provider\bn_BD\Utils;

session_start();

require __DIR__ . "/../vendor/autoload.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
if (!$id || Product::isAttributeTaken("id", $id)) UtilsSession::redirectTo("products.php");
$product = Product::getProductById($id);
$imgName = $product["image"];
if (isset($_POST["deleteImage"])) {
    (new Product)
        ->setName($name)
        ->setType($type)
        ->setStock($stock)
        ->setImage("img/" . Constants::DEFAULT_IMAGE_NAME)
        ->setDescription($description)
        ->update($id);
    ImageValidator::deleteOldImage($imgName);
    $imgName = "img/" . Constants::DEFAULT_IMAGE_NAME;
} else {
    if (isset($_POST["name"])) {
        $name = ProductValidator::sanitizeInput($_POST["name"]);
        $type = ProductValidator::sanitizeInput($_POST["type"]);
        $description = ProductValidator::sanitizeInput($_POST["description"]);
        $stock = (int) ProductValidator::sanitizeInput($_POST["stock"]);
        $hasErrors = false;
        if (!ProductValidator::validateNameLength($name)) $hasErrors = true;
        if (!ProductValidator::isNameTaken($name, $id)) $hasErrors = true;
        if (!ProductValidator::validateType($type)) $hasErrors = true;
        if (!ProductValidator::validateDescriptionLength($description)) $hasErrors = true;
        if (!ProductValidator::validateStockRange($stock)) $hasErrors = true;
        if (ImageValidator::validateImageError((int) $_FILES["image"]["error"])) {
            (!$hasErrors) ?
                $hasErrors = !ImageValidator::validateImage($_FILES["image"])
                : !ImageValidator::validateImage($_FILES["image"]);
        }
        if ($hasErrors) UtilsSession::redirectTo($_SERVER['PHP_SELF'] . "?id=" . $id);
        if (ImageValidator::validateImageError((int) $_FILES["image"]["error"]) && !$imgName = ImageValidator::validateImageMove($_FILES["image"]["tmp_name"], $_FILES["image"]["name"])) UtilsSession::redirectTo($_SERVER['PHP_SELF'] . "?id=" . $id);
        (new Product)
            ->setName($name)
            ->setType($type)
            ->setStock($stock)
            ->setImage(basename($imgName))
            ->setDescription($description)
            ->update($id);
        ImageValidator::deleteOldImage($imgName);
        $_SESSION["message"] = "Product update success";
        UtilsSession::redirectTo("products.php");
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="Ángel Martínez Otero">
    <title>New Product</title>
    <!-- CDN SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- CDN Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- CDN FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="bg-gray-50 dark:bg-gray-900 antialiased">
    <section class="bg-white dark:bg-gray-900">
        <div class="px-4 mx-auto max-w-2xl lg:py-16">
            <h2 class="mb-4 text-2xl font-bold text-gray-900 dark:text-white">Add a new product</h2>
            <form action="<?= $_SERVER["PHP_SELF"] . "?id=" . $id ?>" method="POST" enctype="multipart/form-data" novalidate>
                <div class="grid gap-4 sm:grid-cols-2 sm:gap-6">
                    <div class="sm:col-span-2">
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Product Name</label>
                        <input type="text" value="<?= $product['name'] ?>" name="name" id="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Product name" required="">
                        <?= DisplayError::errorDisplay("name"); ?>
                    </div>
                    <div class="w-full">
                        <label for="stock" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Stock</label>
                        <input type="number" value="<?= $product['stock'] ?>" name="stock" id="stock" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Product stock" required="">
                        <?= DisplayError::errorDisplay("stock"); ?>
                    </div>
                    <div>
                        <label for="type" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Type</label>
                        <select id="type" name="type" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                            <option>Select type</option>
                            <?php
                            foreach (TypeProduct::cases() as $type) {
                                $selected = ($type->toString() === $product['type']) ? ' selected=""' : "";
                                echo "<option value='{$type->toString()}'$selected>{$type->toString()}</option>";
                            }
                            ?>
                        </select>
                        <?= DisplayError::errorDisplay("type"); ?>
                    </div>
                    <div>
                        <div class="flex items-center space-x-6">
                            <div class="shrink-0">
                                <img id='preview_img' class="h-16 w-16 object-cover rounded-full" src="<?= $product['image'] ?>" alt="Current profile photo" />
                            </div>
                            <label class="block">
                                <span class="sr-only">Choose product photo</span>
                                <input type="file" accept="image/*" id="image" name="image" class="block w-full text-sm text-slate-500
                                                      file:mr-4 file:py-2 file:px-4
                                                      file:rounded-full file:border-0
                                                      file:text-sm file:font-semibold
                                                      file:bg-violet-50 file:text-violet-700
                                                      hover:file:bg-violet-100
                                                    " oninput="handlerFilePreview(this);" />
                            </label>
                            <button type="button" name="deleteImage" onclick="handlerFileDefault();" data-modal-target="delete-modal" data-modal-toggle="delete-modal" class="flex items-center text-red-700 hover:text-white border border-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-3 py-2 text-center dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 -ml-0.5" viewbox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                Delete
                            </button>
                        </div>
                        <?= DisplayError::errorDisplay("image"); ?>
                    </div>
                    <div class="sm:col-span-2">
                        <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Description</label>
                        <textarea id="description" name="description" rows="8" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Your description here"><?= $product['description'] ?></textarea>
                        <?= DisplayError::errorDisplay("description"); ?>
                    </div>
                </div>
                <button type="submit" class="inline-flex items-center px-5 py-2.5 mt-4 sm:mt-6 text-sm font-medium text-center text-white bg-green-700 rounded-lg focus:ring-4 focus:ring-green-200 dark:focus:ring-green-900 hover:bg-green-800">
                    Edit product
                </button>
                <button type="reset" class="inline-flex items-center px-5 py-2.5 mt-4 sm:mt-6 text-sm font-medium text-center text-white bg-purple-700 rounded-lg focus:ring-4 focus:ring-purple-200 dark:focus:ring-purple-900 hover:bg-purple-800 ml-4">
                    Reset
                </button>
                <a href="products.php" class="inline-flex items-center px-5 py-2.5 mt-4 sm:mt-6 text-sm font-medium text-center text-white bg-red-700 rounded-lg focus:ring-4 focus:ring-red-200 dark:focus:ring-red-900 hover:bg-red-800 ml-4">
                    Back
                </a>
            </form>
        </div>
    </section>
</body>

<script>
    function handlerFilePreview(input) {
        file = input.files[0];
        if (file) {
            previewImg = document.getElementById("preview_img");
            previewImg.src = URL.createObjectURL(file);
        }
    }

    function handlerFileDefault() {
        previewImg = document.getElementById("preview_img");
        previewImg.src = "img/<?= Constants::DEFAULT_IMAGE_NAME ?>";
    }
</script>

</html>