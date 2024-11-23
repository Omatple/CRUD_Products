<?php

use App\Database\Product;
use App\Utils\ErrorHandler;
use App\Utils\ImageConstants;
use App\Utils\ImageProcessor;
use App\Utils\Navigation;
use App\Utils\ProductInputValidator;
use App\Utils\TypeProduct;

session_start();

require __DIR__ . "/../vendor/autoload.php";

if (isset($_POST["name"])) {
    $name = ProductInputValidator::sanitize($_POST["name"]);
    $type = ProductInputValidator::sanitize($_POST["type"]);
    $description = ProductInputValidator::sanitize($_POST["description"]);
    $stock = (int) ProductInputValidator::sanitize($_POST["stock"]);
    $hasErrors = false;
    if (!ProductInputValidator::isNameLengthValid($name)) $hasErrors = true;
    if (!$hasErrors && !ProductInputValidator::isNameUnique($name)) $hasErrors = true;
    if (!ProductInputValidator::isValidType($type)) $hasErrors = true;
    if (!ProductInputValidator::isDescriptionLengthValid($description)) $hasErrors = true;
    if (!ProductInputValidator::isStockInRange($stock)) $hasErrors = true;
    if (ImageProcessor::isUploadSuccessful((int) $_FILES["image"]["error"])) {
        (!$hasErrors) ?
            $hasErrors = !ImageProcessor::validateImageData($_FILES["image"])
            : !ImageProcessor::validateImageData($_FILES["image"]);
    }
    if ($hasErrors) Navigation::reloadPage();
    $imgName = ImageConstants::DEFAULT_IMAGE_FILENAME;
    if (ImageProcessor::isUploadSuccessful((int) $_FILES["image"]["error"]) && !$imgName = ImageProcessor::moveUploadedFile($_FILES["image"]["tmp_name"], $_FILES["image"]["name"])) Navigation::reloadPage();
    (new Product)
        ->setName($name)
        ->setType($type)
        ->setStock($stock)
        ->setImage(basename($imgName))
        ->setDescription($description)
        ->create();
    ImageProcessor::deletePreviousImage($imgName);
    $_SESSION["message"] = "The new product has been successfully";
    Navigation::redirectToUrl("products.php");
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
    <!-- CDN Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 dark:bg-gray-900 antialiased">
    <section class="bg-white dark:bg-gray-900">
        <div class="px-4 mx-auto max-w-2xl lg:py-16">
            <h2 class="mb-4 text-2xl font-bold text-gray-900 dark:text-white">Add a new product</h2>
            <form action="<?= $_SERVER["PHP_SELF"] ?>" method="POST" enctype="multipart/form-data" novalidate>
                <div class="grid gap-4 sm:grid-cols-2 sm:gap-6">
                    <div class="sm:col-span-2">
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Product Name</label>
                        <input type="text" name="name" id="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Product name" required="">
                        <?= ErrorHandler::displayError("name"); ?>
                    </div>
                    <div class="w-full">
                        <label for="stock" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Stock</label>
                        <input type="number" name="stock" id="stock" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Product stock" required="">
                        <?= ErrorHandler::displayError("stock"); ?>
                    </div>
                    <div>
                        <label for="type" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Type</label>
                        <select id="type" name="type" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                            <option selected="">Select type</option>
                            <?php
                            foreach (TypeProduct::cases() as $type) {
                                echo "<option value='{$type->toString()}'>{$type->toString()}</option>";
                            }
                            ?>
                        </select>
                        <?= ErrorHandler::displayError("type"); ?>
                    </div>
                    <div>
                        <div class="flex items-center space-x-6">
                            <div class="shrink-0">
                                <img id='preview_img' class="h-16 w-16 object-cover rounded-full" src="<?= "img/" . ImageConstants::DEFAULT_IMAGE_FILENAME ?>" alt="Current product photo" />
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
                        </div>
                        <?= ErrorHandler::displayError("image"); ?>
                    </div>
                    <div class="sm:col-span-2">
                        <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Description</label>
                        <textarea id="description" name="description" rows="8" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Your description here"></textarea>
                        <?= ErrorHandler::displayError("description"); ?>
                    </div>
                </div>
                <button type="submit" class="inline-flex items-center px-5 py-2.5 mt-4 sm:mt-6 text-sm font-medium text-center text-white bg-blue-700 rounded-lg focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900 hover:bg-blue-800">
                    Add product
                </button>
                <button type="reset" class="inline-flex items-center px-5 py-2.5 mt-4 sm:mt-6 text-sm font-medium text-center text-white bg-yellow-700 rounded-lg focus:ring-4 focus:ring-yellow-200 dark:focus:ring-yellow-900 hover:bg-yellow-800 ml-4">
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
</script>

</html>