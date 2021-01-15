<?php

require __DIR__ . '/vendor/autoload.php';


use Generators\OrderGenerator;
use Generators\ProductGenerator;
use Generators\CustomerGenerator;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


$orderGenerator = new OrderGenerator();
$productGenerator = new ProductGenerator();
$customerGenerator = new CustomerGenerator();


$orderGenerator->generate(10);
$productGenerator->generate(10);
$customerGenerator->generate(10);
