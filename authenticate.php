<?php
    session_start();
    if(!isset($_SESSION["username"]) && !isset($_SESSION["token"])){
        header("Location: login.php");
        exit(); 
    }
?>