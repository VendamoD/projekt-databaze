<?php
session_start();
if(!$_SESSION["start"]){
header("Location: http://localhost/Project/rooms/login.php");
}