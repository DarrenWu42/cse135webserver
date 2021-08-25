<?php
require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

echo $_ENV['DB_HOST'];

// test code:
// it will output: localhost
// when you run $ php start.php