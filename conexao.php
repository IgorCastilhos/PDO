<?php

use Alura\Pdo\Infrastructure\Persistence\ConnectionCreator;

$pdo = ConnectionCreator::createConnection();

echo "Conexão realizada com sucesso!";

$pdo->exec('CREATE TABLE students (id INTEGER PRIMARY KEY, name TEXT, birth_date TEXT);');
