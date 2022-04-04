<?php
session_start();
require "../includes/bootstrap.inc.php";

if (isset($_SESSION['login'])) {
    header('Location: ./prohlizec');
    exit;
}

final class CurrentPage extends BaseDBPage {
   
   protected string $title = "Login";

   protected function body(): string {  
       $error = '';
       $login = filter_input(INPUT_POST, 'name') ?? '';
       $password = filter_input(INPUT_POST, 'password') ?? '';

       if($login && $password ) {
           $stmt = $this->pdo->prepare("SELECT password, admin FROM employee WHERE login=:login");
           $stmt->bindParam(':login', $login);
           $stmt->execute();
           $dbData = $stmt->fetch();
           if($dbData) {
               if(password_verify($password, $dbData->password)) {
                   $_SESSION['login'] = $login;
                   $_SESSION['admin'] = $dbData->admin;
                   $_SESSION['loggedIn'] = 1;
                   
                   return $this->m->render("loginSuccess", ['message' => "Logged in successfully."]);
                   //header('Location: ./prohlizec.php');
                   exit;
               } else {
                   $error = "Špatné heslo";
                   $_SESSION['loggedIn'] = 0;
               }
           } else {
               $error = "Špatné jméno";
               $_SESSION['loggedIn'] = 0;
           }
       }
       $_SESSION['loggedIn'] = 0;
       return $this->m->render("loginForm", ['login' => $login, 'error' => $error]);
   }
}



(new CurrentPage())->render();