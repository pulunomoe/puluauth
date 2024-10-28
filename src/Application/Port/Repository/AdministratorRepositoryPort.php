<?php

namespace App\Application\Port\Repository;

use App\Application\Exception\AdapterException;
use App\Domain\Administrator\Administrator;
use App\Domain\Administrator\AdministratorCode;

interface AdministratorRepositoryPort
{
    /**
     * @throws AdapterException
     */
    public function findOneByCode(AdministratorCode $code): ?Administrator;
}
