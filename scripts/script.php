<?php

use App\Database\Product;

require __DIR__ . "/../vendor/autoload.php";

do {
    $amount = (int) readline("Enter the number of fake products to create (5-25), or '0' to exit: ");
    if ($amount === 0) exit("\nExiting as requested by the user..." . PHP_EOL);
    if ($amount < 5 || $amount > 25) echo "ERROR: Please enter a number between 5 and 25." . PHP_EOL;
} while ($amount < 5 || $amount > 25);

Product::generateFakeProducts($amount);
echo "$amount fake products created successfully." . PHP_EOL;
