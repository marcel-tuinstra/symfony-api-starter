<?php

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

if (file_exists(dirname(__DIR__) . '/.env.test')) {
    (new Dotenv())->loadEnv(dirname(__DIR__) . '/.env.test');
} else {
    (new Dotenv())->loadEnv(dirname(__DIR__) . '/.env');
}

$_SERVER['APP_ENV'] = $_ENV['APP_ENV'] = $_SERVER['APP_ENV'] ?? 'test';
$_SERVER['APP_DEBUG'] = $_ENV['APP_DEBUG'] = $_SERVER['APP_DEBUG'] ?? '1';
$_SERVER['KERNEL_CLASS'] = $_ENV['KERNEL_CLASS'] = $_SERVER['KERNEL_CLASS'] ?? Kernel::class;
