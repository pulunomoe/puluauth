<?php

namespace App\Adapter\Repository\Pdo;

use App\Application\Exception\AdapterException;
use App\Application\Port\Repository\AdministratorRepositoryPort;
use App\Domain\Administrator\Administrator;
use App\Domain\Administrator\AdministratorCode;
use PDOException;

readonly class AdministratorRepository extends PdoRepository implements AdministratorRepositoryPort
{
    /**
     * @throws AdapterException
     */
    public function findOneByCode(AdministratorCode $code): ?Administrator
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM administrators_view WHERE code = :code LIMIT 1');
            $stmt->bindValue(':code', $code->getValue());
            $stmt->execute();
            $row = $stmt->fetch();
        } catch (PDOException $e) {
            throw new AdapterException($e);
        }

        if (!$row) {
            return null;
        }

        return new Administrator(
            $row['id'],
            new AdministratorCode($row['code']),
            $row['name'],
            $row['email']
        );
    }
}
