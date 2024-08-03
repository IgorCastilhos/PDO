<?php

namespace Alura\Pdo\Infrastructure\Repository;

use Alura\Pdo\Domain\Model\Student;
use Alura\Pdo\Domain\Repository\StudentRepository;
use DateTimeImmutable;
use PDO;

class PdoStudentRepository implements StudentRepository
{
    private \PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    #[\Override] public function allStudents(): array
    {
        $sqlSearchAllStudentsQuery = 'SELECT * FROM students;';
        $statement = $this->connection->query($sqlSearchAllStudentsQuery);

        return $this->hydrateStudentList($statement);
    }

    #[\Override] public function studentsBirthAt(DateTimeImmutable $birthDate): array
    {
        $sqlStudentsBirthQuery = 'SELECT * FROM students WHERE birth_date = ?;';
        $preparedStatement = $this->connection->prepare($sqlStudentsBirthQuery);
        $preparedStatement->bindValue(1, $birthDate->format('Y-m-d'));
        $preparedStatement->execute();

        return $this->hydrateStudentList($preparedStatement);
    }

    private function hydrateStudentList(false|\PDOStatement $statement)
    {
        $studentsDataList = $statement->fetchAll(PDO::FETCH_ASSOC);
        $studentsList = [];

        foreach ($studentsDataList as $studentData) {
            $studentsList[] = new Student(
                $studentData['id'],
                $studentData['name'],
                new DateTimeImmutable($studentData['birth_date'])
            );
        }
        return $studentsList;
    }

    #[\Override] public function save(Student $student): bool
    {
        if ($student->id() === null) {
            $this->insert($student);
        }

        return $this->update($student);
    }

    private function insert(Student $student): bool
    {
        /*
        USANDO O PLACEHOLDER DE SINAL DE INTERROGAÇÃO
        $sqlInsert = "INSERT INTO students (name, birth_date) VALUES (?, ?);";
        $statement = $this->connection->prepare($sqlInsert);

        $success = $statement->execute([
            $student->name(),
            $student->birthDate()->format('Y-m-d')
        ]);
         */

        // USANDO NAMED PLACEHOLDERS
        $sqlInsert = "INSERT INTO students (name, birth_date) VALUES (:name, :birth_date);";
        $statement = $this->connection->prepare($sqlInsert);

        $success = $statement->execute([
            ':name', $student->name(),
            ':birth_date', $student->birthDate()->format('Y-m-d')
        ]);

        if ($success) {
            $student->defineId($this->connection->lastInsertId());
        }

        return $success;
    }

    private function update(Student $student): bool
    {
        $sqlUpdateQuery = 'UPDATE students SET name = :name, birth_date = :birth_date WHERE id = :id;';
        $preparedStatement = $this->connection->prepare($sqlUpdateQuery);
        $preparedStatement->bindValue(':name', $student->name());
        $preparedStatement->bindValue(':birth_date', $student->birthDate());
        $preparedStatement->bindValue('id', $student->id(), PDO::PARAM_INT);

        return $preparedStatement->execute();
    }

    #[\Override] public function remove(Student $student): bool
    {
        $preparedStatement = $this->connection->prepare('DELETE FROM students WHERE id = ?;');
        $preparedStatement->bindValue(1, $student->id(), PDO::PARAM_INT);

        return $preparedStatement->execute();
    }
}
