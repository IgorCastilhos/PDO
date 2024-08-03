<?php

use Alura\Pdo\Infrastructure\Repository\PdoStudentRepository;

$pdo = new PDO('mysql:');
$repository = new PdoStudentRepository($pdo);

empty($repository->allStudents());
