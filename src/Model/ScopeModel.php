<?php

namespace Com\Pulunomoe\PuluAuth\Model;

use Ramsey\Uuid\Uuid;

class ScopeModel extends Model implements FindOneInterface
{
	public function findAll(): array
	{
		$stmt = $this->prepare('SELECT * FROM scopes ORDER BY name');
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public function findOne(int $id): array|bool
	{
		$stmt = $this->prepare('SELECT * FROM scopes WHERE id = ?');
		$stmt->execute([$id]);
		return  $stmt->fetch();
	}

	public function findOneByName(string $name): array|bool
	{
		$stmt = $this->prepare('SELECT * FROM scopes WHERE name = ?');
		$stmt->execute([$name]);
		return  $stmt->fetch();
	}

	public function create(string $name, string $description): int
	{
		$stmt = $this->prepare('INSERT INTO scopes (identifier, name, description) VALUES (?, ?, ?)');
		$stmt->execute([Uuid::uuid4(), $name, $description]);

		return $this->lastInsertId();
	}

	public function update(int $id, string $name, string $description): void
	{
		$stmt = $this->prepare('UPDATE scopes SET name = ?, description = ? WHERE id = ?');
		$stmt->execute([$name, $description, $id]);
	}

	public function delete(int $id): void
	{
		$stmt = $this->prepare('DELETE FROM scopes WHERE id = ?');
		$stmt->execute([$id]);
	}

	public function validate(?int $id, string $name): array
	{
		$errors = [];

		if (!empty($id)) {
			if (empty($this->findOne($id))) {
				$errors[] = 'scope not found';
			}
		}

		if (empty($name)) {
			$errors[] = 'scope name is required';
		}

		return $errors;
	}
}
