<?php

use App\Database\Product;
use App\Utils\UtilsSession;

require __DIR__ . "/../vendor/autoload.php";
session_start();

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
if (!$id || Product::isAttributeTaken("id", $id)) UtilsSession::redirectTo("$.php");
Product::delete($id);
$_SESSION["message"] = "Product delete success";
UtilsSession::redirectTo("products.php");
