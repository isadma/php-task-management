<?php

// Initialize the session
session_start();
include "includes/checkLoggedIn.php";

function getLastUpdatedTask($id = false){
    include "includes/config.php";
    if ($id){
        $task = mysqli_fetch_assoc(mysqli_query($dbConnection, "SELECT * FROM tasks where id=$id"));
    }
    else{
        $task = mysqli_fetch_assoc(mysqli_query($dbConnection, "SELECT * FROM tasks order by updated_at desc limit 1"));
    }
    return [
        "id" => $task['id'],
        "title" => $task['title'],
        "body" => $task['body'],
        "createdAt" => date('d-m-Y H:i', strtotime($task['created_at'])),
        "updatedAt" => date('d-m-Y H:i', strtotime($task['updated_at'])),
    ];
}

if($_SERVER["REQUEST_METHOD"] == "POST"){

    if ($_POST['type'] == 'addTask'){

        //validation
        // Check if id is empty
        if(empty(trim($_POST["id"]))){
            echo json_encode([
                "status" => false,
                "message" => "Oops, something went wrong."
            ]);
            exit;
        } else{
            $id = trim($_POST["id"]);
        }
        // Check if title is empty
        if(empty(trim($_POST["title"]))){
            echo json_encode([
                "status" => false,
                "message" => "Please enter title."
            ]);
            exit;
        } else{
            $title = trim($_POST["title"]);
        }

        // Check if title is content
        if(empty(trim($_POST["content"]))){
            echo json_encode([
                "status" => false,
                "message" => "Please enter content."
            ]);
            exit;
        } else{
            $content = trim($_POST["content"]);
        }
        $queryCreateNewTask = "INSERT INTO tasks (user_id, title, body) VALUES ($id, '$title', '$content')";

        if ($dbConnection->query($queryCreateNewTask)) {
            echo json_encode([
                "status" => false,
                "message" => "New task is successfully created.",
                "task" => getLastUpdatedTask($dbConnection->insert_id)
            ]);
        } else {
            echo json_encode([
                "status" => false,
                "message" => "Error on creating task: " . $dbConnection->error
            ]);
        }
        exit;
    }
    elseif ($_POST['type'] == 'deleteTask'){
        //validation
        // Check if id is empty
        if(empty(trim($_POST["id"]))){
            echo json_encode([
                "status" => false,
                "message" => "Oops, something went wrong."
            ]);
            exit;
        } else{
            $id = trim($_POST["id"]);
        }
        $queryDeleteTask = "DELETE from tasks where id = $id";

        if ($dbConnection->query($queryDeleteTask)) {
            echo json_encode([
                "status" => false,
                "message" => "Task is successfully deleted."
            ]);
        } else {
            echo json_encode([
                "status" => false,
                "message" => "Error on deleting task: " . $dbConnection->error
            ]);
        }
        exit;
    }
    elseif($_POST['type'] == 'updateTask'){
        //validation
        // Check if id is empty
        if(empty(trim($_POST["id"]))){
            echo json_encode([
                "status" => false,
                "message" => "Oops, something went wrong."
            ]);
            exit;
        } else{
            $id = trim($_POST["id"]);
        }
        // Check if title is empty
        if(empty(trim($_POST["title"]))){
            echo json_encode([
                "status" => false,
                "message" => "Please enter title."
            ]);
            exit;
        } else{
            $title = trim($_POST["title"]);
        }

        // Check if title is content
        if(empty(trim($_POST["content"]))){
            echo json_encode([
                "status" => false,
                "message" => "Please enter content."
            ]);
            exit;
        } else{
            $content = trim($_POST["content"]);
        }

        $now = date('Y-m-d H:i:s');
        $queryUpdateTask = "UPDATE tasks SET title='$title', body='$content', updated_at='$now' WHERE id = $id";
        if ($dbConnection->query($queryUpdateTask)) {
            echo json_encode([
                "status" => true,
                "message" => "Task is successfully updated.",
                "task" => getLastUpdatedTask($id)
            ]);
        } else {
            echo json_encode([
                "status" => true,
                "message" => "Error on updating task: " . $dbConnection->error
            ]);
        }
        exit;
    }
    header("location: index.php");
}
else{
    header("location: index.php");
    exit;
}