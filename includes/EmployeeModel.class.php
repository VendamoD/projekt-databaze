<?php

final class EmployeeModel
{
    public ?int $employee_id;
    public string $name;
    public string $surname;
    public ?string $job;
    public ?int $wage;
    public ?int $room;
    public string $login;
    public string $password;
    public ?int $admin;

    private array $validationErrors = [];
    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }

    public function __construct(array $employeeData = [])
    {
        $id = $employeeData['employee_id'] ?? null;
        if (is_string($id))
            $id = filter_var($id, FILTER_VALIDATE_INT);

        $this->employee_id = $id;
        $this->name = $employeeData['name'] ?? "";
        $this->surname = $employeeData['surname'] ?? "";
        $this->job = $employeeData['job'] ?? "";
        $this->wage = $employeeData['wage'] ?? null;
        $this->room = $employeeData['room'] ?? null;
        $this->login = $employeeData['login'] ?? "";
        $this->password = $employeeData['password'] ?? "";
        $this->admin = $employeeData['admin'] ?? null;
    }

    public function validate() : bool
    {
        $isOk = true;

        if (!$this->name) {
            $isOk = false;
            $this->validationErrors['name'] = "Name cannot be empty";
        }
        if (!$this->surname) {
            $isOk = false;
            $this->validationErrors['surname'] = "Surname cannot be empty";
        }
        if (!$this->job){
            $isOk = false;
            $this->getValidationErrors['job'] = "Job cannot be empty";
        }
        if (!$this->wage) {
            $isOk = false;
            $this->getValidationErrors['wage'] = "Wage cannot be empty";
        }
        if (!$this->room) {
            $isOk = false;
            $this->getValidationErrors['room'] = "Room cannot be empty";
        }
        if (!$this->login) {
            $isOk = false;
            $this->getValidationErrors['login'] = "Login cannot be empty";
        }
        if (!$this->password) {
            $isOk = false;
            $this->getValidationErrors['password'] = "Password cannot be empty";
        }


        return $isOk;
    }

    public function insert() : bool
    {
        $query = "INSERT INTO employee (name, surname, job, wage, room, login, password, admin) VALUES (:name, :surname, :job, :wage, :room, :login, :password, 0)";

        $stmt = DB::getConnection()->prepare($query);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':surname', $this->surname);
        $stmt->bindParam(':job', $this->job);
        $stmt->bindparam(':wage', $this->wage);
        $stmt->bindparam(':room', $this->room);
        $stmt->bindparam(':login', $this->login);
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        $stmt->bindparam(':password', $this->password);

        if (!$stmt->execute())
            return false;
        $this->employee_id = DB::getConnection()->lastInsertId();
        return true;
    }

    public function update() : bool
    {
        
        $query = "UPDATE employee SET name=:name, surname=:surname, job=:job, wage=:wage, room=:room, login=:login, password=:password, admin=:admin WHERE employee_id=:employeeId";

        $stmt = DB::getConnection()->prepare($query);
        $stmt->bindParam(':employeeId', $this->employee_id);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':surname', $this->surname);
        $stmt->bindParam(':job', $this->job);
        $stmt->bindparam(':wage', $this->wage);
        $stmt->bindparam(':room', $this->room);
        $stmt->bindparam(':login', $this->login);
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        $stmt->bindparam(':password', $this->password);
        $stmt->bindparam(':admin', $this->admin);

        return $stmt->execute();
    }

    public function delete() : bool
    {
        return self::deleteById($this->employee_id);
    }

    public static function deleteById(int $employee_id) : bool {

        $query = "DELETE FROM employee WHERE employee_id=:employeeId";

        $stmt = DB::getConnection()->prepare($query);
        $stmt->bindParam(':employeeId', $employee_id);

        return $stmt->execute();
    }

    public static function findById(int $employee_id) : ?EmployeeModel
    {
        $query = "SELECT * FROM employee WHERE employee_id=:employeeId";

        $stmt = DB::getConnection()->prepare($query);
        $stmt->bindParam(':employeeId', $employee_id);

        $stmt->execute();

        $dbData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dbData)
            return null;

        return new self($dbData);
    }

    public static function readPostData() : EmployeeModel
    {
        return new self($_POST); //není úplně košer, nefiltruju
    }
}