<?php
    $dbHost = 'localhost';
    $dbUser = 'root';
    $dbPassword = '';
    $dbName = 'taskManagement';

    $dbConnection = new mysqli($dbHost, $dbUser, $dbPassword);
    if (mysqli_connect_errno()) {
        printf("Could not connect to MySQL database: %s\n", mysqli_connect_error());
        exit();
    }

    $db_selected = mysqli_select_db($dbConnection, $dbName);

    if (!$db_selected) {
        $sql = 'CREATE DATABASE '. $dbName . " character set utf8 collate utf8_bin";
        if (mysqli_query($dbConnection, $sql)) {
            mysqli_select_db($dbConnection, $dbName);
        }
        else{
            echo "Error creating database: (" . $dbConnection->errno . ") " . $dbConnection->error;
        }
    }

    $queryCreateUsersTable = "CREATE TABLE IF NOT EXISTS `users` (
        `id` int(11) unsigned NOT NULL auto_increment,
        `name` varchar(255) NOT NULL default '',
        `email` varchar(255) UNIQUE NOT NULL default '',
        `password` varchar(255) NOT NULL default '',
        `is_admin` tinyint(1) unsigned NOT NULL default '0',
        `created_at` timestamp NOT NULL default CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL default CURRENT_TIMESTAMP,
        PRIMARY KEY  (`id`)
    )";

    if(!$dbConnection->query($queryCreateUsersTable)){
        echo "Users table creation failed: (" . $dbConnection->errno . ") " . $dbConnection->error;
    }

    $queryCheckSuperAdmin = "SELECT id FROM users where is_admin = 1";
    $resultOfSuperAdminQuery = $dbConnection->query($queryCheckSuperAdmin)->num_rows;

    if ($resultOfSuperAdminQuery == 0){
        $passwordOfAdmin = password_hash("password", PASSWORD_BCRYPT);
        $insertSuperAdminToUsersTable = "INSERT INTO users (name, email, password, is_admin) VALUES ('Admin', 'admin@tasks.test', '$passwordOfAdmin', 1)";
        if (!$dbConnection->query($insertSuperAdminToUsersTable)) {
            echo "Admin insertion failed: (" . $dbConnection->errno . ") " . $dbConnection->error;
        }
    }

    $queryCreateTasksTable = "CREATE TABLE IF NOT EXISTS `tasks` (
        `id` int(11) unsigned NOT NULL auto_increment,
        `user_id` int(11) unsigned NOT NULL,
        `title` varchar(255) NOT NULL default '',
        `body` text,
        `created_at` timestamp NOT NULL default CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL default CURRENT_TIMESTAMP,
        PRIMARY KEY  (`id`),
        FOREIGN KEY (`user_id`) REFERENCES users(id)
    )";

    if(!$dbConnection->query($queryCreateTasksTable)){
        echo "Users table creation failed: (" . $dbConnection->errno . ") " . $dbConnection->error;
    }