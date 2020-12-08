<?php

    // Initialize the session
    session_start();

    //active nav menu
    $_SESSION['nav'] = 'home';

    //checking admin or user
    $id = 0;
    if (isset($_GET['id']) && $_SESSION['is_admin']){
        $id = trim($_GET['id']);
    }
    else{
        $id = $_SESSION['id'];
    }

    //title of page
    global $title;
    $title = "Home";

    include "includes/checkLoggedIn.php";
?>

<!--including header-->
<?php include "partials/header.php"; ?>

    <div id="loading" class="loading d-none"></div>

    <div class="content">
        <div id="message" class="alert d-none">
            <p id="messageText" class="text-success">
                message
            </p>
        </div>

        <div class="header">
            <h2>Tasks</h2>
            <button class="btn btn-modal" data-target="createNewTaskModal" style="width: auto;"> Add new task </button>
        </div>

        <div class="content-data" id="tasks">
            <?php

            //getting tasks from db
            $queryTasks = "SELECT * FROM tasks where user_id=$id order by id desc";

            $taskResult = $dbConnection->query($queryTasks);
            if ($taskResult->num_rows > 0) {
                while ($task = $taskResult->fetch_assoc()) {

                    //showing tasks

                    ?>
                    <div class="content-item" id="item<?php echo $task['id']; ?>">
                        <span>
                            Title:
                            <strong> <?php echo urldecode($task['title']); ?> </strong>
                        </span>
                        <span>
                            Body:
                            <strong> <?php echo urldecode($task['body']); ?> </strong>
                        </span>
                        <span>
                            Created at:
                            <strong> <?php echo date('d-m-y H:i', strtotime($task['created_at'])); ?> </strong>
                        </span>
                        <span>
                            Updated at:
                            <strong> <?php echo date('d-m-y H:i', strtotime($task['updated_at'])); ?> </strong>
                        </span>
                        <span>
                            <button class="btn mr-5 btn-modal" data-target="editTask<?php echo $task['id']; ?>">Edit</button>
                            <form class="taskForm">
                                <input type="hidden" name="id" value="<?php echo $task['id']; ?>">
                                <input type="hidden" name="type" value="deleteTask">
                                <button type="submit" class="btn btn-danger mr-5">
                                    Delete
                                </button>
                            </form>
                        </span>
                    </div>

                    <!--modal for editing new task-->
                    <div id="editTask<?php echo $task['id']; ?>" class="modal">
                        <!-- Modal content -->
                        <div class="modal-content">
                            <h4>Update task</h4>
                            <form class="taskForm" method="POST">
                                <input type="hidden" name="id" value="<?php echo $task['id']; ?>">
                                <input type="hidden" name="type" value="updateTask">
                                <div class="form-group">
                                    <label for="title">Title</label>
                                    <input type="text" class="form-control" name="title" id="title" placeholder="Enter title" required value="<?php echo urldecode($task["title"]); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="content">Content</label>
                                    <textarea rows="5" class="form-control" name="content" id="content" placeholder="Enter content" required><?php echo urldecode($task["body"]); ?></textarea>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn">Update</button>
                                </div>
                                <div class="form-group">
                                    <button class="btn cancel" data-target="editTask<?php echo $task['id']; ?>"> Cancel </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <?php
                }
            }
            else{
                echo "<p id='noTask'>No tasks</p>";
            }
            ?>
        </div>
    </div>

    <!--modal for creating new task-->
    <div id="createNewTaskModal" class="modal">
        <!-- Modal content -->
        <div class="modal-content">
            <h4>Create new task</h4>
            <form class="taskForm" method="POST">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <input type="hidden" name="type" value="addTask">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" class="form-control" name="title" id="title" placeholder="Enter title" required value="<?php echo isset($_POST["title"]) ? $_POST["title"] : "" ?>">
                </div>
                <div class="form-group">
                    <label for="content">Content</label>
                    <textarea rows="5" class="form-control" name="content" id="content" placeholder="Enter content" required><?php echo isset($_POST["content"]) ? $_POST["content"] : "" ?></textarea>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn">Add</button>
                </div>
                <div class="form-group">
                    <button class="btn cancel" data-target="createNewTaskModal"> Cancel </button>
                </div>
            </form>
        </div>
    </div>

<!--including footer-->
<?php include "partials/footer.php"; ?>