<?php
session_start();
require "../includes/bootstrap.inc.php";
require "../includes/session.inc.php";

final class CurrentPage extends BaseDBPage {
    protected string $title = "Výpis místností";

    protected function body(): string
    {

        $stmt = $this->pdo->prepare("SELECT * FROM `room` ORDER BY `name`");
        $stmt->execute([]);

        return $this->m->render("roomList", ["roomDetail" => "room.php", "admin" => $_SESSION['admin'], "rooms" => $stmt]);
    }
}

(new CurrentPage())->render();
