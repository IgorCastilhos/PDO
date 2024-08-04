<?php

use Alura\Pdo\Domain\Model\Student;
use Alura\Pdo\Infrastructure\Persistence\ConnectionCreator;
use Alura\Pdo\Infrastructure\Repository\PdoStudentRepository;

require_once 'vendor/autoload.php';

$conn = ConnectionCreator::createConnection();
$studentRepository = new PdoStudentRepository($conn);

$conn->beginTransaction();
try {
    $aStudent = new Student(
        null,
        'Fulano 1',
        new DateTimeImmutable('1999-04-06')
    );
    $studentRepository->save($aStudent);

    $anotherStudent = new Student(
        null,
        "Fulano 2",
        new DateTimeImmutable('1967-04-12')
    );
    $studentRepository->save($anotherStudent);

    $conn->commit();
} catch (PDOException $e) {
    echo $e->getMessage();
    $conn->rollBack();
}
