<?php

use App\Database\Product;

$amount = (int) readline("Write amount for creates fakes products (5-25), or '0' if you want exit: ");
if ($amount === 0) exit("\nExits for request of user..." . PHP_EOL);
while ($amount < 5 | $amount > 25) {
    $amount = (int) readline("ERROR: Write amount for creates fakes products (5-25), or '0' if you want exit: ");
    if ($amount === 0) exit("\nExits for request of user..." . PHP_EOL);
}
require __DIR__ . "/../vendor/autoload.php";

Product::generateFakesProducts($amount);
echo "$amount fakes products creates" . PHP_EOL;
