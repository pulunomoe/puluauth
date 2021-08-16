<?php

$pdo = new PDO(
	'mysql:host=localhost;dbname=puluauth;charset=utf8mb4',
	'mysqldev',
	'mysqldev123',
	[
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	]
);
