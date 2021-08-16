<?php

namespace Com\Pulunomoe\PuluAuth\Model;

use DateTimeImmutable;
use PDO;

class TokenModel extends Model
{
	private ClientModel $clientModel;

	public function __construct(PDO $pdo)
	{
		parent::__construct($pdo);
		$this->clientModel = new ClientModel($pdo);
	}

	public function findOneByIdentider(string $identifier): array|bool
	{
		$stmt = $this->prepare('SELECT * FROM tokens_view WHERE identifier = ?');
		$stmt->execute([$identifier]);
		return $stmt->fetch();
	}

	public function create(string $clientIdentifier, string $identifier, DateTimeImmutable $expiry, array $scopes): void
	{
		$client = $this->clientModel->findOneByIdentifier($clientIdentifier);
		$scopesString = '';
		foreach ($scopes as $scope) {
			$scopesString .= ' '.$scope->getIdentifier();
		}
		$expiry = $expiry->format('Y-m-d H:i:s');

		$stmt = $this->prepare('INSERT INTO tokens (client_id, identifier, expiry, scopes) VALUES (?, ?, ?, ?)');
		$stmt->execute([$client['id'], $identifier, $expiry, $scopesString]);
	}

	public function delete(string $identifier): void
	{
		$stmt = $this->prepare('DELETE FROM tokens WHERE identifier = ?');
		$stmt->execute([$identifier]);
	}
}
