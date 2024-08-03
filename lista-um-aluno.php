<?php

use Alura\Pdo\Domain\Model\Student;

require_once 'vendor/autoload.php';

$databasePath = __DIR__ . '/banco.sqlite';
$pdo = new PDO('sqlite:' . $databasePath);

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
