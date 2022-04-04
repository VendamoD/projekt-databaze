<?php

final class LoginModel
{
    public string $name;
    public string $password;

    private array $validationErrors = [];
    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }

    public function __construct(array $roomData = [])
    {
        _construct();
        $this->room_id = $id;
        $this->name = $roomData['name'] ?? "";
        $this->password = $roomData['password'] ?? "";
    }

    public function validate() : bool
    {
        $isOk = true;

        if (!$this->name) {
            $isOk = false;
            $this->validationErrors['name'] = "Name cannot be empty";
        }
        if (!$this->password) {
            $isOk = false;
            $this->validationErrors['password'] = "Password cannot be empty";
        }

        return $isOk;
    }

    public static function readPostData() : LoginModel
    {
        return new self($_POST); //není úplně košer, nefiltruju
    }
}