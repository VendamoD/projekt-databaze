<?php

if(!$_SESSION['loggedIn']) {
    //var_dump($_SESSION);
    header('Location: ./index.php');
    exit;
}