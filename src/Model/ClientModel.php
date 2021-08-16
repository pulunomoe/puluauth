<?php

namespace Com\Pulunomoe\PuluAuth\Model;

use Ramsey\Uuid\Uuid;

class ClientModel extends Model implements FindOneInterface
{
	public function findAll(): array
	{
		$stmt = $this->prepare('SELECT id, identifier, name, description FROM clients WHERE id > 1 ORDER BY name');
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public function findOne(int $id): array|bool
	{
		$stmt = $this->prepare('SELECT id, identifier, name, description FROM clients WHERE id = ? AND id > 1');
		$stmt->execute([$id]);
		return  $stmt->fetch();
	}

	public function findOneByIdentifier(string $identifier): array|bool
	{
		$stmt = $this->prepare('SELECT id, identifier, name, description FROM clients WHERE identifier = ? AND id > 1');
		$stmt->execute([$identifier]);
		return  $stmt->fetch();
	}

	public function validateSecret(string $identifier, string $secret): bool
	{
		$stmt = $this->prepare('SELECT secret FROM clients WHERE identifier = ? AND id > 1');
		$stmt->execute([$identifier]);

		return password_verify($secret, $stmt->fetchColumn() ?? '');
	}

	public function create(string $name, string $description): array
	{
		$identifier = Uuid::uuid4();
		$secret = password_hash(Uuid::uuid4(), PASSWORD_DEFAULT);
		$encryptedSecret = password_hash($secret, PASSWORD_DEFAULT);

		$stmt = $this->prepare('INSERT INTO clients (identifier, name, secret, description) VALUES (?, ?, ?, ?)');
		$stmt->execute([$identifier, $name, $encryptedSecret, $description]);

		$client = $this->findOne($this->lastInsertId());
		$client['secret'] = $secret;

		return $client;
	}

	public function update(int $id, string $name, string $description): void
	{
		$stmt = $this->prepare('UPDATE clients SET name = ?, description = ? WHERE id = ? AND id > 1');
		$stmt->execute([$name, $description, $id]);
	}

	public function newSecret(int $id): string
	{
		$secret = password_hash(Uuid::uuid4(), PASSWORD_DEFAULT);
		$encryptedSecret = password_hash($secret, PASSWORD_DEFAULT);

		$stmt = $this->prepare('UPDATE clients SET secret = ? WHERE id = ? AND id > 1');
		$stmt->execute([$encryptedSecret, $id]);

		return $secret;
	}

	public function updateAdminPassword(string $password): void
	{
		$stmt = $this->prepare('UPDATE clients SET secret = ? WHERE id = 1');
		$stmt->execute([password_hash($password, PASSWORD_DEFAULT)]);
	}

	public function delete(int $id): void
	{
		$stmt = $this->prepare('DELETE FROM clients WHERE id = ?');
		$stmt->execute([$id]);
	}

	public function validate(?int $id, string $name): array
	{
		$errors = [];

		if (!empty($id)) {
			if (empty($this->findOne($id))) {
				$errors[] = 'client not found';
			}
		}

		if (empty($name)) {
			$errors[] = 'client name is required';
		}

		return $errors;
	}
}
