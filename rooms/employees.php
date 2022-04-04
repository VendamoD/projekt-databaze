<?php
session_start();
require "../includes/bootstrap.inc.php";
require "../includes/session.inc.php";

final class CurrentPage extends BaseDBPage {
    protected string $title = "Výpis Zaměstnanců";

    protected function body(): string
    {

        $stmt = $this->pdo->prepare("SELECT * FROM `employee` ORDER BY `name`");
        $stmt->execute([]);


        return $this->m->render("employeeList",  ["employeeDetail" => "employees.php", "isadmin" => $_SESSION['admin'],"employees" => $stmt]);
    }
}

(new CurrentPage())->render();
