<?php

    require_once "includes\config.php";

    // Check if the user is logged in, if not then redirect him to login page
    if(!isset($_SESSION["loggedIn"]) || $_SESSION["loggedIn"] !== true){
        header("location: login.php");
        exit;
    }