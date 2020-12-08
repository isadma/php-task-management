<?php

    // Initialize the session
    session_start();

    $_SESSION['nav'] = 'profile';

    global $title;
    $title = "My Profile";

    include "includes/checkLoggedIn.php";
    $nameErrorMessage = $emailErrorMessage = "";
    $currentPasswordErrorMessage = $newPasswordErrorMessage = $confirmNewPasswordErrorMessage = "";

    $successMessage = $errorMessage = "";

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $user_id = $_SESSION['id'];
        $now = date('Y-m-d H:i:s');
        if (isset($_POST["updateProfile"])){
            //validation

            // Check if name is empty
            if(empty(trim($_POST["name"]))){
                $nameErrorMessage = "Please enter your name.";
            } else{
                $name = trim($_POST["name"]);
            }

            // Check if email is empty
            if(empty(trim($_POST["email"]))){
                $emailErrorMessage = "Please enter email.";
            } else{
                $email = trim($_POST["email"]);
                $emailUniqueValidation = "SELECT id FROM users WHERE email = '$email' and id != $user_id";

                $emailUniqueValidationResult = $dbConnection->query($emailUniqueValidation);
                if ($emailUniqueValidationResult->num_rows > 0) {
                    $emailErrorMessage = "Email is already taken.";
                }
            }

            if(empty($emailErrorMessage) && empty($nameErrorMessage) && empty($errorMessage)) {
                $queryUpdateProfile = "UPDATE users SET name='$name', email='$email', updated_at='$now' WHERE id = $user_id";

                if ($dbConnection->query($queryUpdateProfile)) {
                    $successMessage = "Profile is successfully updated.";
                } else {
                    $errorMessage = "Error on updating profile: " . $dbConnection->error;
                }
            }
        }
        elseif (isset($_POST["changePassword"])){
            //validation

            // Check if current password is empty
            if(empty(trim($_POST["currentPassword"]))){
                $currentPasswordErrorMessage = "Please enter your current password.";
            } else{
                $currentPassword = trim($_POST["currentPassword"]);

                $emailUniqueValidation = "SELECT password FROM users WHERE id = $user_id";

                $queryCheckPassword = $dbConnection->query($emailUniqueValidation);
                if ($queryCheckPassword->num_rows > 0) {
                    while ($row = $queryCheckPassword->fetch_assoc()) {
                        if (!password_verify($currentPassword, $row["password"])) {
                            $currentPasswordErrorMessage = "You entered wrong current password.";
                        }
                    }
                }
            }

            if (empty($currentPasswordErrorMessage)) {
                // Check if new password is empty
                if (empty(trim($_POST["newPassword"]))) {
                    $newPasswordErrorMessage = "Please enter your new password.";
                } else {
                    $newPassword = trim($_POST["newPassword"]);
                }

                // Check if confirm password is empty
                if (empty(trim($_POST["confirmNewPassword"]))) {
                    $confirmNewPasswordErrorMessage = "Please enter your confirm new password.";
                } else {
                    $newConfirmPassword = trim($_POST["confirmNewPassword"]);
                }

                if ($newPassword !== $newConfirmPassword) {
                    $confirmNewPasswordErrorMessage = $newPasswordErrorMessage = "New password confirmation does not match";
                }

                if (empty($newPasswordErrorMessage) && empty($confirmNewPasswordErrorMessage) && empty($errorMessage)) {
                    $password = password_hash($newPassword, PASSWORD_BCRYPT);
                    $queryChangePassword = "UPDATE users SET password='$password', updated_at='$now' WHERE id = $user_id";

                    if ($dbConnection->query($queryChangePassword)) {
                        $successMessage = "Password is successfully changed.";
                    } else {
                        $errorMessage = "Error on updating profile: " . $dbConnection->error;
                    }
                }
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

        <h2>My profile</h2>

        <form class="w-40" action='<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>' method="POST">
            <div class="form-group <?php echo (!empty($nameErrorMessage)) ? 'has-error' : ''; ?>">
                <label for="name">Name</label>
                <input type="text" class="form-control" name="name" id="name" placeholder="Enter your name" required value="<?php echo $_SESSION['name']; ?>">
                <span class='form-text text-danger'>
                <?php echo $nameErrorMessage; ?>
            </span>
            </div>
            <div class="form-group <?php echo (!empty($emailErrorMessage)) ? 'has-error' : ''; ?>">
                <label for="email">Email</label>
                <input type="email" class="form-control" name="email" id="email" placeholder="Enter your email" required value="<?php echo $_SESSION['email']; ?>">
                <span class='form-text text-danger'>
                <?php echo $emailErrorMessage; ?>
            </span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn" name="updateProfile" value="Update">
            </div>
        </form>

        <hr class="separator w-40">

        <h2>Change password</h2>

        <form class="w-40" action='<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>' method="POST">
            <div class="form-group <?php echo (!empty($currentPasswordErrorMessage)) ? 'has-error' : ''; ?>">
                <label for="currentPassword">Current password</label>
                <input type="password" class="form-control" name="currentPassword" id="currentPassword" placeholder="Current password" required>
                <span class='form-text text-danger'>
                    <?php echo $currentPasswordErrorMessage; ?>
                </span>
            </div>
            <div class="form-group <?php echo (!empty($newPasswordErrorMessage)) ? 'has-error' : ''; ?>">
                <label for="newPassword">New password</label>
                <input type="password" class="form-control" name="newPassword" id="newPassword" placeholder="New password" required>
                <span class='form-text text-danger'>
                    <?php echo $newPasswordErrorMessage; ?>
                </span>
            </div>
            <div class="form-group <?php echo (!empty($confirmNewPasswordErrorMessage)) ? 'has-error' : ''; ?>">
                <label for="confirmNewPassword">Confirm new password</label>
                <input type="password" class="form-control" name="confirmNewPassword" id="confirmNewPassword" placeholder="Confirm new password" required>
                <span class='form-text text-danger'>
                    <?php echo $confirmNewPasswordErrorMessage; ?>
                </span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn" name="changePassword" value="Change password">
            </div>
        </form>

        <hr class="separator w-40">

        <h2>Change password</h2>

        <div class="form-group">
            <a class="btn" href="logout.php">Logout</a>
        </div>

    </div>

<?php include "partials/footer.php"; ?>