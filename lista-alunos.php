<?php

use Alura\Pdo\Domain\Model\Student;

require_once 'vendor/autoload.php';

$databasePath = __DIR__ . '/banco.sqlite';
$pdo = new PDO('sqlite:' . $databasePath);

// Busca de dados
$statement = $pdo->query('SELECT * FROM students;');

// Lista de arrays associativos
$studentsDataList = $statement->fetchAll(PDO::FETCH_ASSOC);
$studentsList = [];

// Hidratação dos dados:
// Cada item $studentData é um
// array associativo. Esses arrays
// são usados para criar uma instância
// de um objeto da classe "Student",
// passando valores como 'id', 'name'
// e 'birth_date' como parâmetros
// para o construtor da classe.
// O array $studentsList armazena
// todas as instâncias de Student
// criadas, resultando em uma lista de
// objetos que representam os estudantes.
foreach ($studentsDataList as $studentData) {
    $studentsList[] = new Student(
        $studentData['id'],
        $studentData['name'],
        new DateTimeImmutable($studentData['birth_date'])
    );
}

var_dump($studentsList);
