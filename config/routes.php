<?php

use App\Adapter\Administration\Slim\Controller\AdministratorController;
use Slim\App;

return function (App $app): void {
    $app->get('/administrators', [AdministratorController::class, 'findOneByCode']);
};
