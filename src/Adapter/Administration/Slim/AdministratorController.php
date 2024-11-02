<?php

namespace App\Adapter\Administration\Slim;

use App\Adapter\Administration\Slim\Exception\HttpBadRequestException;
use App\Adapter\Administration\Slim\Service\JsonResponseFactory;
use App\Application\Exception\AdapterException;
use App\Application\UseCase\Administration\Administrator\FindOneAdministratorByCodeUseCase;
use App\Domain\Administrator\AdministratorCode;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class AdministratorController extends Controller
{
    /**
     * @throws HttpBadRequestException
     * @throws AdapterException
     */
    public function findOneByCode(ServerRequestInterface $request): ResponseInterface
    {
        $code = $request->getQueryParams()['code'] ?? null;
        if (!$code) {
            throw new HttpBadRequestException('Parameter `code` is required');
        }

        $findOneAdministratorByCode = $this->get(FindOneAdministratorByCodeUseCase::class);

        $result = $findOneAdministratorByCode->execute(new AdministratorCode($code));
        $result = $result ? $result->serialize() : [];

        return JsonResponseFactory::create(200, $result);
    }
}
