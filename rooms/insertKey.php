<?php
require "../includes/bootstrap.inc.php";

final class CurrentPage extends BaseDBPage {

    const STATE_FORM_REQUESTED = 1;
    const STATE_FORM_SENT = 2;
    const STATE_PROCESSED = 3;

    const RESULT_SUCCESS = 1;
    const RESULT_FAIL = 2;

    private int $state;
    private KeyModel $key;
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
        $this->title = "New Key";
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

            $this->key = KeyModel::readPostData();

            //validovat
            $isOk = $this->key->validate();

            //když jsou validní
            if ($isOk) {
                //uložit
                if ($this->key->insert()) {
                    //přesměruj, ohlas úspěch
                    $this->redirect(self::RESULT_SUCCESS);
                } else {
                    //přesměruj, ohlas chybu
                    $this->redirect(self::RESULT_FAIL);
                }
            } else {
                $this->state = self::STATE_FORM_REQUESTED;
            }
        } else {
            $this->state = self::STATE_FORM_REQUESTED;
            $this->key = new KeyModel();
        }

    }


    protected function body(): string
    {
        
        if ($this->state == self::STATE_FORM_REQUESTED) {
        $stmt = $this->pdo->prepare("SELECT name, surname, employee_id FROM `employee` ORDER BY `name`");
        $stmt->execute([]);
        $stmt2 = $this->pdo->prepare("SELECT name, room_ID FROM `room` ORDER BY `name`");
        $stmt2->execute([]);
        $stmt3 = $this->pdo->prepare("SELECT * FROM `key`");
        $stmt3->execute([]);        

            return $this->m->render(
                "keys",
                [
                    "keysDetail" => "insertKey.php",
                    "room" => $stmt2, "key" => $stmt3,
                    "employees" => $stmt,
                    'key' => $this->key,
                    'errors' => $this->key->getValidationErrors(),
                    'action' => "create"
                ]
            );
        }
        else if ($this->state == self::STATE_PROCESSED){
            //vypiš výsledek zpracování
            if ($this->result == self::RESULT_SUCCESS) {
                return $this->m->render("keySuccess", ['message' => "New key created successfully."]);
            } else {
                return $this->m->render("keyFail", ['message' => "Key creation failed."]);
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
        if ($action == "create"){
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
