<?php

namespace Alura\Pdo\Infrastructure\Repository;

use Alura\Pdo\Domain\Model\Phone;
use Alura\Pdo\Domain\Model\Student;
use Alura\Pdo\Domain\Repository\StudentRepository;
use PDO;

class PdoStudentRepository implements StudentRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function allStudents(): array
    {
        $sqlSearchAllStudentsQuery = 'SELECT * FROM students;';
        $statement = $this->connection->query($sqlSearchAllStudentsQuery);

        return $this->hydrateStudentList($statement);
    }

    public function studentsBirthAt(\DateTimeInterface $birthDate): array
    {
        $sqlStudentsBirthQuery = 'SELECT * FROM students WHERE birth_date = ?;';
        $preparedStatement = $this->connection->prepare($sqlStudentsBirthQuery);
        $preparedStatement->bindValue(1, $birthDate->format('Y-m-d'));
        $preparedStatement->execute();

        return $this->hydrateStudentList($preparedStatement);
    }

    private function hydrateStudentList(\PDOStatement $statement): array
    {
        $studentsDataList = $statement->fetchAll();
        $studentsList = [];

        foreach ($studentsDataList as $studentData) {
            $studentsList[] = new Student(
                $studentData['id'],
                $studentData['name'],
                new \DateTimeImmutable($studentData['birth_date'])
            );
        }

        return $studentsList;
    }

    public function save(Student $student): bool
    {
        if ($student->id() === null) {
            return $this->insert($student);
        }

        return $this->update($student);
    }

    private function insert(Student $student): bool
    {
        $insertQuery = 'INSERT INTO students (name, birth_date) VALUES (:name, :birth_date);';
        $stmt = $this->connection->prepare($insertQuery);

        $success = $stmt->execute([
            ':name' => $student->name(),
            ':birth_date' => $student->birthDate()->format('Y-m-d'),
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
        $preparedStatement->bindValue(':birth_date', $student->birthDate()->format('Y-m-d'));
        $preparedStatement->bindValue('id', $student->id(), PDO::PARAM_INT);

        return $preparedStatement->execute();
    }

    public function remove(Student $student): bool
    {
        $preparedStatement = $this->connection->prepare('DELETE FROM students WHERE id = ?;');
        $preparedStatement->bindValue(1, $student->id(), PDO::PARAM_INT);

        return $preparedStatement->execute();
    }

    public function studentsWithPhones(): array
    {
        $sqlQuery = 'SELECT students.id,
                            students.name,
                            students.birth_date,
                            phones.id AS phone_id,
                            phones.area_code,
                            phones.number
                        FROM students
                        JOIN phones ON students.id = phones.student_id;';
        $statement = $this->connection->query($sqlQuery);
        $result = $statement->fetchAll();
        $studentList = [];

        foreach ($result as $row) {
            if (!array_key_exists($row['id'], $studentList)) {
                $studentList[$row['id']] = new Student(
                    $row['id'],
                    $row['name'],
                    new \DateTimeImmutable($row['birth_date'])
                );
            }
            $phone = new Phone($row['phone_id'], $row['area_code'], $row['number']);
            $studentList[$row['id']]->addPhone($phone);
        }

        return $studentList;
    }
}
