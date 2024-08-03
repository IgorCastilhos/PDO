<?php

use Alura\Pdo\Domain\Model\Student;

require_once 'vendor/autoload.php';

$databasePath = __DIR__ . '/banco.sqlite';
$pdo = new PDO('sqlite:' . $databasePath);

$student = new Student(
    null,
    "Cristiano Ronaldo",
    new DateTimeImmutable('1990-12-07')
);

// Evitando SQL Injection
$sqlInsert = "INSERT INTO students (name, birth_date)
              VALUES (:name, :birth_date);";

// Prepared statement
$statement = $pdo->prepare($sqlInsert);

$statement->bindValue(':name', $student->name());

$statement->bindValue(':birth_date', $student->birthDate()->format('Y-m-d'));

if ($statement->execute()) {
    echo "Aluno incluído!";
}

exit();

//var_dump($pdo->exec($sqlInsert));
