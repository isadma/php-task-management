<?php
// Initialize the session
session_start();

global $title;
$title = "Login";

// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedIn"]) && $_SESSION["loggedIn"]){
    header("location: index.php");
    exit;
}

// Include config file
include "includes/config.php";

$emailErrorMessage = $passwordErrorMessage = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    //validation
    // Check if email is empty
    if(empty(trim($_POST["email"]))){
        $emailErrorMessage = "Please enter email.";
    } else{
        $email = trim($_POST["email"]);
    }

    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $passwordErrorMessage = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }

    //if there is no problem on validation
    if(empty($emailErrorMessage) && empty($passwordErrorMessage)) {

        $email = $_POST['email'];
        $password = $_POST['password'];

        $queryLogin = "SELECT id, email, password, name, is_admin FROM users WHERE email = '$email'";

        $loginResult = $dbConnection->query($queryLogin);
        if ($loginResult->num_rows > 0) {
            while ($row = $loginResult->fetch_assoc()) {
                if (password_verify($password, $row["password"])) {

                    // Store data in session variables
                    $_SESSION["loggedIn"] = true;
                    $_SESSION["id"] = $row['id'];
                    $_SESSION["email"] = $row['email'];
                    $_SESSION["name"] = $row['name'];
                    $_SESSION["is_admin"] = $row['is_admin'];

                    header("location: index.php");
                }
            }
            $emailErrorMessage = "These credentials do not match our records.";
        }
        else {
            $emailErrorMessage = "These credentials do not match our records.";
        }
    }
}
?>

<?php include "partials/header.php" ?>
   <div class="login-card">
       <h2>Login</h2>
       <form action=" <?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
           <div class="form-group <?php echo (!empty($emailErrorMessage)) ? 'has-error' : ''; ?>">
               <label for="email">Email</label>
               <input type="email" class="form-control" name="email" id="email" placeholder="Enter your email" required>
               <span class='form-text text-danger'>
                    <?php echo $emailErrorMessage; ?>
                </span>
           </div>
           <div class="form-group <?php echo (!empty($passwordErrorMessage)) ? 'has-error' : ''; ?>">
               <label for="password">Password</label>
               <input type="password" class="form-control" name="password" id="password" placeholder="Enter your password" required>
               <span class='form-text text-danger'>
                    <?php echo $passwordErrorMessage; ?>
                </span>
           </div>
           <div class="form-group">
               <input type="submit" class="btn" value="Go">
           </div>
       </form>
   </div>
<?php include "partials/footer.php" ?>