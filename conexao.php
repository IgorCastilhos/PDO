<?php

$caminhoAbsoluto = __DIR__ . '/banco.sqlite';
$pdo = new PDO('sqlite:' . $caminhoAbsoluto);

echo "Conexão realizada com sucesso!";

$pdo->exec('CREATE TABLE students (id INTEGER PRIMARY KEY, name TEXT, birth_date TEXT);');
