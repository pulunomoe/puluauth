<?php

use DI\ContainerBuilder;
use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createMutable(__DIR__ . '/../', '.env.test');
$dotenv->load();

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/../config/dependencies.php');
$container = $containerBuilder->build();

global $app;
$app = require __DIR__ . '/../config/bootstrap.php';
