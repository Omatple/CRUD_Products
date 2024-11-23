<?php

use App\Database\Product;
use App\Utils\Navigation;

require __DIR__ . "/../vendor/autoload.php";
session_start();

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
if (!$id || Product::isFieldUnique("id", $id)) Navigation::redirectToUrl("products.php");
Product::delete($id);
$_SESSION["message"] = "The product has been successfully deleted.";
Navigation::reloadPage("products.php");
