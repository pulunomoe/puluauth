<?php

namespace Com\Pulunomoe\PuluAuth\Model;

interface FindOneInterface
{
	public function findOne(int $id): array|bool;
}
