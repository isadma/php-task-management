<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title><?php echo $title; ?> | Task Management</title>

    <!--fonts-->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">

    <!--fontawesome-->
    <script src="https://kit.fontawesome.com/df71a0556b.js" crossorigin="anonymous"></script>

    <!--css-->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php
    if (!strpos($_SERVER['REQUEST_URI'], 'login.php'))
        include "partials/nav.php";
?>