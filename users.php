<?php

    // Initialize the session
    session_start();

    if (!$_SESSION['is_admin']){
        header("location: index.php");
    }

    //active nav menu
    $_SESSION['nav'] = 'users';

    //title of page
    global $title;
    $title = "Users";

    include "includes/checkLoggedIn.php";

    //defining error messages
    $nameErrorMessage = $emailErrorMessage = $passwordErrorMessage = $confirmPasswordErrorMessage = "";

    $errorMessage = $successMessage = "";

    //if request is post method
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $now = date('Y-m-d H:i:s');

        if ($_POST['type'] == "addUser"){
            // Check if name is empty
            if(empty(trim($_POST["name"]))){
                $nameErrorMessage = "Please enter name.";
            } else{
                $name = trim($_POST["name"]);
            }

            // Check if email is empty
            if(empty(trim($_POST["email"]))){
                $emailErrorMessage = "Please enter email.";
            } else{
                $email = trim($_POST["email"]);
                $emailUniqueValidation = "SELECT id FROM users WHERE email = '$email'";

                $emailUniqueValidationResult = $dbConnection->query($emailUniqueValidation);
                if ($emailUniqueValidationResult->num_rows > 0) {
                    $emailErrorMessage = "Email is already taken.";
                }
            }

            if (empty(trim($_POST["password"]))) {
                $passwordErrorMessage = "Please enter password.";
            } else {
                $password = trim($_POST["password"]);
            }

            // Check if confirm password is empty
            if (empty(trim($_POST["confirmPassword"]))) {
                $confirmPasswordErrorMessage = "Please enter confirm password.";
            } else {
                $confirmPassword = trim($_POST["confirmPassword"]);
            }

            if ($password !== $confirmPassword) {
                $passwordErrorMessage = $confirmPasswordErrorMessage = "Password confirmation does not match";
            }

            if (empty($emailErrorMessage) && empty($passwordErrorMessage) && empty($confirmPasswordErrorMessage) && empty($passwordErrorMessage)){
                $password = password_hash($password, PASSWORD_BCRYPT);
                $queryCreateNewUser = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";

                if ($dbConnection->query($queryCreateNewUser)) {
                    $successMessage = "New user is successfully created.";
                } else {
                    $errorMessage = "Something went wrong: " . "<br>" . $dbConnection->error;
                }
            }
            else{
                $errorMessage = "New user could not added. Please, open modal and look for errors.";
            }
        }
        elseif ($_POST['type'] == "deleteUser"){
            // Check if id is empty
            if(empty(trim($_POST["id"]))){
                $errorMessage = "Something went wrong.";
            } else{
                $id = trim($_POST["id"]);
            }
            if (empty($errorMessage)){
                $queryDeleteUser = "DELETE FROM users WHERE id=$id";

                if ($dbConnection->query($queryDeleteUser)) {
                    $successMessage = "User is successfully deleted.";
                } else {
                    $errorMessage = "Something went wrong: " . "<br>" . $dbConnection->error;
                }
            }
        }
        elseif ($_POST['type'] == "updateUser"){
            // Check if id is empty
            if(empty(trim($_POST["id"]))){
                $errorMessage = "Something went wrong.";
            } else{
                $id = trim($_POST["id"]);
            }
            // Check if name is empty
            if(empty(trim($_POST["name"]))){
                $nameErrorMessage = "Please enter name.";
            } else{
                $name = trim($_POST["name"]);
            }

            // Check if email is empty
            if(empty(trim($_POST["email"]))){
                $emailErrorMessage = "Please enter email.";
            } else{
                $email = trim($_POST["email"]);
                $emailUniqueValidation = "SELECT id FROM users WHERE email = '$email' and id !=$id";

                $emailUniqueValidationResult = $dbConnection->query($emailUniqueValidation);
                if ($emailUniqueValidationResult->num_rows > 0) {
                    $emailErrorMessage = "Email is already taken.";
                }
            }

            $password = "";
            if (!empty(trim($_POST["password"])) && !empty(trim($_POST["confirmPassword"]))) {
                $password = trim($_POST["password"]);
                $confirmPassword = trim($_POST["confirmPassword"]);
                if ($password !== $confirmPassword) {
                    $passwordErrorMessage = $confirmPasswordErrorMessage = "Password confirmation does not match";
                }
                $password = password_hash($password, PASSWORD_BCRYPT);
            }

            if (empty($emailErrorMessage) && empty($passwordErrorMessage) && empty($confirmPasswordErrorMessage) && empty($passwordErrorMessage)){
                if (empty($password)){
                    $queryUpdateUser = "UPDATE users set name='$name', email='$email', updated_at = '$now' where id=$id";
                }
                else{
                    $queryUpdateUser = "UPDATE users set name='$name', email='$email', password='$password', updated_at = '$now' where id=$id";
                }

                if ($dbConnection->query($queryUpdateUser)) {
                    $successMessage = "User is successfully updated.";
                } else {
                    $errorMessage = "Something went wrong: " . "<br>" . $dbConnection->error;
                }
            }
            else{
                $errorMessage = "User could not updated. Please, open modal and look for errors.";
            }
        }
    }
?>

<?php include "partials/header.php"; ?>

    <div class="content">
        <?php if (!empty($errorMessage) || !empty($successMessage)){ ?>
            <div class="alert">
                <p class="text-<?php echo empty($errorMessage) ? "success" : "danger"; ?>">
                    <?php echo empty($errorMessage) ? $successMessage : $errorMessage; ?>
                </p>
            </div>
        <?php } ?>

        <div class="header">
            <h2>Users</h2>
            <button class="btn btn-modal" data-target="createNewItemModal" style="width: auto;"> Add new user </button>
        </div>

        <div class="content-data">
            <?php
            $queryUsers = "SELECT * FROM users WHERE is_admin = 0 order by id desc";

            $userResult = $dbConnection->query($queryUsers);
            if ($userResult->num_rows > 0) {
                while ($user = $userResult->fetch_assoc()) {
                    ?>
                    <div class="content-item">
                        <span>
                            ID:
                            <strong> <?php echo $user['id']; ?> </strong>
                        </span>
                        <span>
                            Name:
                            <strong> <?php echo $user['name']; ?> </strong>
                        </span>
                        <span>
                            Email:
                            <strong> <?php echo $user['email']; ?> </strong>
                        </span>
                        <span>
                            Created at:
                            <strong> <?php echo date('d-m-y H:i', strtotime($user['created_at'])); ?> </strong>
                        </span>
                        <span>
                            Updated at:
                            <strong> <?php echo date('d-m-y H:i', strtotime($user['updated_at'])); ?> </strong>
                        </span>
                        <span>
                            <a href="index.php?id=<?php echo $user['id']; ?>" class="btn btn-success mr-5">Tasks</a>
                            <button class="btn mr-5 btn-modal" data-target="editUser<?php echo $user['id']; ?>">Edit</button>
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                                <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                <input type="hidden" name="type" value="deleteUser">
                                <a class="btn btn-danger mr-5"
                                    onclick="if (confirm('Do you want to delete this user?')) {this.parentElement.submit();}"
                                >
                                    Delete
                                </a>
                            </form>
                        </span>
                    </div>

                    <div id="editUser<?php echo $user['id']; ?>" class="modal">
                        <div class="modal-content">
                        <h4>Update user</h4>
                        <form action='<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>' method="POST">
                            <input type="hidden" name="type" value="updateUser">
                            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                            <div class="form-group <?php echo (!empty($nameErrorMessage)) ? 'has-error' : ''; ?>">
                                <label>Name</label>
                                <input type="text" class="form-control" name="name" placeholder="Enter your name" required value="<?php echo $user["name"]; ?>">
                                <span class='form-text text-danger'>
                                    <?php echo $nameErrorMessage; ?>
                                </span>
                            </div>
                            <div class="form-group <?php echo (!empty($emailErrorMessage)) ? 'has-error' : ''; ?>">
                                <label>Email</label>
                                <input type="email" class="form-control" name="email" placeholder="Enter your email" required value="<?php echo $user["email"]; ?>">
                                <span class='form-text text-danger'>
                                    <?php echo $emailErrorMessage; ?>
                                </span>
                            </div>
                            <div class="form-group <?php echo (!empty($passwordErrorMessage)) ? 'has-error' : ''; ?>">
                                <label>Password</label>
                                <input type="password" class="form-control" name="password" placeholder="Password">
                                <span class='form-text text-danger'>
                                    <?php echo $passwordErrorMessage; ?>
                                </span>
                            </div>
                            <div class="form-group <?php echo (!empty($confirmPasswordErrorMessage)) ? 'has-error' : ''; ?>">
                                <label>Confirm password</label>
                                <input type="password" class="form-control" name="confirmPassword" placeholder="Confirm password">
                                <span class='form-text text-danger'>
                                    <?php echo $confirmPasswordErrorMessage; ?>
                                </span>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn">Update</button>
                            </div>
                            <div class="form-group">
                                <button class="btn cancel" data-target="editUser<?php echo $user['id']; ?>"> Cancel </button>
                            </div>
                        </form>
                    </div>
                    </div>
                    <?php
                }
            }
            else{
                echo "<p>No users</p>";
            }
            ?>
        </div>

    </div>

    <div id="createNewItemModal" class="modal">
        <!-- Modal content -->
        <div class="modal-content">
            <h4>Create new user</h4>
            <form action='<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>' method="POST">
                <input type="hidden" name="type" value="addUser">
                <div class="form-group <?php echo (!empty($nameErrorMessage)) ? 'has-error' : ''; ?>">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" name="name" id="name" placeholder="Enter your name" required value="<?php echo isset($_POST["name"]) ? $_POST["name"] : "" ?>">
                    <span class='form-text text-danger'>
                        <?php echo $nameErrorMessage; ?>
                    </span>
                </div>
                <div class="form-group <?php echo (!empty($emailErrorMessage)) ? 'has-error' : ''; ?>">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="Enter your email" required value="<?php echo isset($_POST["name"]) ? $_POST["email"] : "" ?>">
                    <span class='form-text text-danger'>
                        <?php echo $emailErrorMessage; ?>
                    </span>
                </div>
                <div class="form-group <?php echo (!empty($passwordErrorMessage)) ? 'has-error' : ''; ?>">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                    <span class='form-text text-danger'>
                        <?php echo $passwordErrorMessage; ?>
                    </span>
                </div>
                <div class="form-group <?php echo (!empty($confirmPasswordErrorMessage)) ? 'has-error' : ''; ?>">
                    <label for="confirmPassword">Confirm password</label>
                    <input type="password" class="form-control" name="confirmPassword" id="confirmPassword" placeholder="Confirm password" required>
                    <span class='form-text text-danger'>
                    <?php echo $confirmPasswordErrorMessage; ?>
                </span>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn">Add</button>
                </div>
                <div class="form-group">
                    <button class="btn cancel" data-target="createNewItemModal"> Cancel </button>
                </div>
            </form>
        </div>
    </div>

<?php include "partials/footer.php"; ?>