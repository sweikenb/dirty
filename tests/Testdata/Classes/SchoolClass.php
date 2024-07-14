<?php

namespace Sweikenb\Library\Dirty\Tests\Testdata\Classes;

class SchoolClass
{
    public function __construct(
        private Person $teacher,
        private array $students
    ) {
    }

    public function getTeacher(): Person
    {
        return $this->teacher;
    }

    public function setTeacher(Person $teacher): void
    {
        $this->teacher = $teacher;
    }

    public function getStudents(): array
    {
        return $this->students;
    }

    public function setStudents(Person ...$students): void
    {
        $this->students = $students;
    }
}
