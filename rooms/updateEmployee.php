<?php
require "../includes/bootstrap.inc.php";

final class CurrentPage extends BaseDBPage {

    const STATE_FORM_REQUESTED = 1;
    const STATE_FORM_SENT = 2;
    const STATE_PROCESSED = 3;

    const RESULT_SUCCESS = 1;
    const RESULT_FAIL = 2;

    private int $state;
    private EmployeeModel $employee;
    private int $result = 0;

    //když nepřišla data a není hlášení o výsledku, chci zobrazit formulář
    //když přišla data
      //validuj
      //když jsou validní
        //ulož a přesměruj zpět (PRG)
        //jinak vrať do formuláře
    public function __construct()
    {
        parent::__construct();
        $this->title = "New Employee";
    }


    protected function setUp(): void
    {
        parent::setUp();

        $this->state = $this->getState();

        if ($this->state == self::STATE_PROCESSED) {
            //reportuju

        } elseif ($this->state == self::STATE_FORM_SENT) {
            //přišla data
            //načíst

            $this->employee = EmployeeModel::readPostData();

            //validovat
            $isOk = $this->employee->validate();

            //když jsou validní
            if ($isOk) {
                //uložit
                if ($this->employee->update()) {
                    //přesměruj, ohlas úspěch
                    $this->redirect(self::RESULT_SUCCESS);
                } else {
                    //přesměruj, ohlas chybu
                    $this->redirect(self::RESULT_FAIL);
                }
            } else {
                $this->state = self::STATE_FORM_REQUESTED;
            }
        } else { //vyžádán formulář, nebylo updatováno
            $this->state = self::STATE_FORM_REQUESTED;
            $employeeId = filter_input(INPUT_POST, "employee_id");
            $this->employee = EmployeeModel::findById($employeeId);
            //neřešena chyba, pokud neexistuje v DB nebo nebylo ID vůbec posláno
        }

    }


    protected function body(): string
    {
        if ($this->state == self::STATE_FORM_REQUESTED)
            return $this->m->render(
                "employeeUpdate",
                [
                    'employee' => $this->employee,
                    'errors' => $this->employee->getValidationErrors(),
                    'action' => "update"
                ]
            );
        elseif ($this->state == self::STATE_PROCESSED){
            //vypiš výsledek zpracování
            if ($this->result == self::RESULT_SUCCESS) {
                return $this->m->render("employeeSuccess", ['message' => "Employee updated successfully."]);
            } else {
                return $this->m->render("employeeFail", ['message' => "Employee update failed."]);
            }
        }
        return "";
    }

    protected function getState() : int
    {
        //když mám result -> zpracováno
        $result = filter_input(INPUT_GET, 'result', FILTER_VALIDATE_INT);

        if ($result == self::RESULT_SUCCESS) {
            $this->result = self::RESULT_SUCCESS;
            return self::STATE_PROCESSED;
        } elseif($result == self::RESULT_FAIL) {
            $this->result = self::RESULT_FAIL;
            return self::STATE_PROCESSED;
        }

        //nebo když mám post -> zvaliduju a buď uložím nebo form
        $action = filter_input(INPUT_POST, 'action');
        if ($action == "update"){
            return self::STATE_FORM_SENT;
        }
        //jinak chci form
        return self::STATE_FORM_REQUESTED;
    }

    private function redirect(int $result) : void {
        $location = strtok($_SERVER['REQUEST_URI'], '?');
        header("Location: {$location}?result={$result}");
        exit;
    }

}

(new CurrentPage())->render();
