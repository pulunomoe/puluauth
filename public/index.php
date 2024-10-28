<?php

use App\Adapter\Administration\Slim\SlimAdministrationAdapter;
use Nyholm\Psr7Server\ServerRequestCreator;
use Slim\ResponseEmitter;

$app = require __DIR__ . '/../config/bootstrap.php';

$serverRequest = $app->getContainer()->get(ServerRequestCreator::class)->fromGlobals();
$httpRequestPort = new SlimAdministrationAdapter($app);
$response = $httpRequestPort->handleRequest($serverRequest);

$responseEmitter = new ResponseEmitter();
$responseEmitter->emit($response);
