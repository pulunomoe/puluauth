<?php

namespace App\Application\UseCase\Administration\Administrator;

use App\Application\Exception\AdapterException;
use App\Application\Port\Repository\AdministratorRepositoryPort;
use App\Domain\Administrator\Administrator;
use App\Domain\Administrator\AdministratorCode;

readonly class FindOneAdministratorByCodeUseCase
{
    public function __construct(
        private AdministratorRepositoryPort $administratorRepository
    ) {
    }

    /**
     * @throws AdapterException
     */
    public function execute(AdministratorCode $code): ?Administrator
    {
        return $this->administratorRepository->findOneByCode($code);
    }
}
