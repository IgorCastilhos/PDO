<?php

use Alura\Pdo\Domain\Model\Student;
use Alura\Pdo\Infrastructure\Persistence\ConnectionCreator;

$pdo = ConnectionCreator::createConnection();

//$statement = $pdo->query('SELECT * FROM students WHERE id = 1;');
$statement = $pdo->query('SELECT * FROM students;');

// Iterando um aluno por um e apagando eles da memória depois
while ($studentData = $statement->fetch(PDO::FETCH_ASSOC)) {
    $student = new Student(
        $studentData['id'],
        $studentData['name'],
        new DateTimeImmutable($studentData['birth_date'])
    );
    echo $student->age() . PHP_EOL;
}
exit();
