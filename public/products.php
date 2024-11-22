<?php

use App\Database\Product;
use App\Utils\MessageSweet;

session_start();

require __DIR__ . "/../vendor/autoload.php";

$products = Product::read();
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
    <title>Users</title>
    <!-- CDN SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- CDN Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- CDN FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="bg-gray-50 dark:bg-gray-900 p-3 sm:p-5 antialiased">
    <section class="bg-gray-50 dark:bg-gray-900 p-3 sm:p-5 antialiased">
        <div class="mt-18 flex justify-center">
            <h2
                class="py-10 text-4xl tracking-tight leading-10 font-extrabold text-gray-900 sm:text-5xl sm:leading-none md:text-6xl">
                <span class="text-indigo-600">Products</span>
            </h2>
        </div>
        <div class="mx-auto max-w-screen-2xl px-4 lg:px-12">
            <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
                <div class="flex flex-col md:flex-row items-stretch md:items-center md:space-x-3 space-y-3 md:space-y-0 justify-end mx-4 py-4 border-t dark:border-gray-700">
                    <div class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">
                        <a href="new.php" id="createProductButton" data-modal-toggle="createProductModal" class="flex items-center justify-center text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                            <svg class="h-3.5 w-3.5 mr-1.5 -ml-1" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path clip-rule="evenodd" fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                            </svg>
                            Add product
                        </a>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="p-4">Id</th>
                                <th scope="col" class="p-4">Name</th>
                                <th scope="col" class="p-4">Type</th>
                                <th scope="col" class="p-4">Stock</th>
                                <th scope="col" class="p-4">Description</th>
                                <th scope="col" class="p-4">Actions</th>
                            </tr>
                        </thead>
                        <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
                            <tbody>
                                <?php
                                foreach ($products as $product) {
                                    $stockColor = match (true) {
                                        ($product['stock'] >= 80) => "green",
                                        ($product['stock'] >= 50) => "teal",
                                        ($product['stock'] >= 25) => "yellow",
                                        default => "red",
                                    };
                                    echo <<< TXT
                                        <tr class="border-b dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700">
                                            <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">{$product['id']}</td>
                                            <th scope="row" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                <div class="flex items-center mr-3">
                                                    <img src="{$product['image']}" alt="{$product['name']} Image" class="h-8 w-auto mr-3">
                                                    {$product['name']}
                                                </div>
                                            </th>
                                            <td class="px-4 py-3">
                                                <span class="bg-primary-100 text-primary-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-primary-900 dark:text-primary-300">{$product['type']}</span>
                                            </td>
                                            <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                <div class="flex items-center">
                                                    <div class="h-4 w-4 rounded-full inline-block mr-2 bg-$stockColor-700"></div>
                                                    {$product['stock']}
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 font-medium text-gray-900 max-w-xs dark:text-white">{$product['description']}</td>
                                            <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                    <form action="delete.php" method="POST">
                                                        <div class="flex items-center space-x-4">
                                                            <input type="text" name="id" value="{$product['id']}" hidden />
                                                            <a href="update.php?id={$product['id']}" data-drawer-target="drawer-update-product" data-drawer-show="drawer-update-product" aria-controls="drawer-update-product" class="py-2 px-3 flex items-center text-sm font-medium text-center text-white bg-green-700 rounded-lg hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 -ml-0.5" viewbox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                                    <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                                                    <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" />
                                                                </svg>
                                                                Edit
                                                            </a>
                                                            <button type="submit" data-modal-target="delete-modal" data-modal-toggle="delete-modal" class="flex items-center text-red-700 hover:text-white border border-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-3 py-2 text-center dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 -ml-0.5" viewbox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                                </svg>
                                                                Delete
                                                            </button>
                                                        </div>
                                                    </form>
                                            </td>
                                        </tr>
                                    TXT;
                                }
                                ?>
                            </tbody>
                        </form>
                    </table>
                </div>
            </div>
        </div>
    </section>
</body>
<?= MessageSweet::displaySweetAlertMessage(); ?>

</html>