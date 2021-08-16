<?php

use Com\Pulunomoe\PuluAuth\Auth\Repository\AccessTokenRepository;
use Com\Pulunomoe\PuluAuth\Auth\Repository\ClientRepository;
use Com\Pulunomoe\PuluAuth\Auth\Repository\ScopeRepository;
use Com\Pulunomoe\PuluAuth\Controller\AuthController;
use Com\Pulunomoe\PuluAuth\Controller\ClientController;
use Com\Pulunomoe\PuluAuth\Controller\ScopeController;
use Defuse\Crypto\Key;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Twig\Extension\DebugExtension;

session_start();

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/dbconfig.php';

$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);

$twig = Twig::create(__DIR__ . '/../templates/', ['debug' => true]);
$twig->addExtension(new DebugExtension());
$twig->getEnvironment()->addGlobal('session', $_SESSION);
$app->add(TwigMiddleware::create($app, $twig));

$clientRepository = new ClientRepository($pdo);
$scopeRepository = new ScopeRepository($pdo);
$accessTokenRepository = new AccessTokenRepository($pdo);

$privateKey = new CryptKey('file://'.__DIR__.'/../keys/private.key', null, false);
$encryptionKey = trim(file_get_contents('file://'.__DIR__.'/../keys/defuse.key'));
$encryptionKey = Key::loadFromAsciiSafeString($encryptionKey);

$clientCredentialsGrant = new ClientCredentialsGrant();
$accessTokenTtl = new DateInterval('PT1H');

$authServer = new AuthorizationServer($clientRepository, $accessTokenRepository, $scopeRepository, $privateKey, $encryptionKey);
$authServer->enableGrantType($clientCredentialsGrant, $accessTokenTtl);

$authController = new AuthController($authServer);
$clientController = new ClientController($pdo);
$scopeController = new ScopeController($pdo);

$app->post('/access_token', [$authController, 'accessToken']);

$app->get('/admin/clients', [$clientController, 'index']);
$app->get('/admin/clients/view/{clientId}', [$clientController, 'view']);
$app->get('/admin/clients/form[/{clientId}]', [$clientController, 'form']);
$app->post('/admin/clients/form', [$clientController, 'formPost']);
$app->get('/admin/clients/newsecret/{clientId}', [$clientController, 'newSecret']);
$app->post('/admin/clients/newsecret', [$clientController, 'newSecretPost']);
$app->get('/admin/clients/delete/{clientId}', [$clientController, 'delete']);
$app->post('/admin/clients/delete', [$clientController, 'deletePost']);

$app->get('/admin/scopes', [$scopeController, 'index']);
$app->get('/admin/scopes/view/{scopeId}', [$scopeController, 'view']);
$app->get('/admin/scopes/form[/{scopeId}]', [$scopeController, 'form']);
$app->post('/admin/scopes/form', [$scopeController, 'formPost']);
$app->get('/admin/scopes/delete/{scopeId}', [$scopeController, 'delete']);
$app->post('/admin/scopes/delete', [$scopeController, 'deletePost']);

$app->run();
