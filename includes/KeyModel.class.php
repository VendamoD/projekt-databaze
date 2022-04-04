<?php

final class KeyModel
{
    public ?int $key_id;
    public ?int $employee;
    public ?int $room;

    private array $validationErrors = [];
    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }

    public function __construct(array $keyData = [])
    {
        $id = $keyData['key_id'] ?? null;
        if (is_string($id))
            $id = filter_var($id, FILTER_VALIDATE_INT);

        $this->key_id = $id;
        $this->employee = $keyData['employee'] ?? null;
        $this->room = $keyData['room'] ?? null;
    }

    public function validate() : bool
    {
        $isOk = true;

        if (!$this->employee) {
            $isOk = false;
            $this->validationErrors['employee'] = "Employee cannot be empty";
        }
        if (!$this->room) {
            $isOk = false;
            $this->validationErrors['room'] = "Room cannot be empty";
        }

        return $isOk;
    }

    public function insert() : bool
    {
        $query = "INSERT INTO key (employee, room) VALUES (:employee, :room)";

        $stmt = DB::getConnection()->prepare($query);
        $stmt->bindParam(':employee', $this->employee);
        $stmt->bindParam(':room', $this->room);

        if (!$stmt->execute())
            return false;

        $this->key_id = DB::getConnection()->lastInsertId();
        return true;
    }

    public function update() : bool
    {

        $query = "UPDATE key SET employee=:employee, room=:room WHERE key_id=:keyId";

        $stmt = DB::getConnection()->prepare($query);
        $stmt->bindParam(':keyId', $this->key_id);
        $stmt->bindParam(':employee', $this->employee);
        $stmt->bindParam(':room', $this->room);
        return $stmt->execute();
    }

    public function delete() : bool
    {
        return self::deleteById($this->key_id);
    }

    public static function deleteById(int $key_id) : bool {

        $query = "DELETE FROM key WHERE key_id=:keyId";

        $stmt = DB::getConnection()->prepare($query);
        $stmt->bindParam(':keyId', $key_id);

        return $stmt->execute();
    }

    public static function findById(int $key_id) : ?KeyModel
    {
        $query = "SELECT * FROM key WHERE key_id=:keyId";

        $stmt = DB::getConnection()->prepare($query);
        $stmt->bindParam(':keyId', $key_id);

        $stmt->execute();

        $dbData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dbData)
            return null;

        return new self($dbData);
    }

    public static function readPostData() : KeyModel
    {
        return new self($_POST); //není úplně košer, nefiltruju
    }
}