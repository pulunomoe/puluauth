<?php

namespace Com\Pulunomoe\PuluAuth\Auth\Repository;

use Com\Pulunomoe\PuluAuth\Auth\Entity\ClientEntity;
use Com\Pulunomoe\PuluAuth\Model\ClientModel;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use PDO;

class ClientRepository implements ClientRepositoryInterface
{
	private ClientModel $clientModel;

	public function __construct(PDO $pdo)
	{
		$this->clientModel = new ClientModel($pdo);
	}

	public function getClientEntity($clientIdentifier): ?ClientEntity
	{
		$client = $this->clientModel->findOneByIdentifier($clientIdentifier);
		if (empty($client)) return null;

		return new ClientEntity($clientIdentifier, $client['name']);
	}

	public function validateClient($clientIdentifier, $clientSecret, $grantType): bool
	{
		return $this->clientModel->validateSecret($clientIdentifier, $clientSecret);
	}
}
