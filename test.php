<?php

use Alura\Pdo\Infrastructure\Repository\PdoStudentRepository;

$pdo = new PDO('sqlite:');
$repository = new PdoStudentRepository($pdo);

