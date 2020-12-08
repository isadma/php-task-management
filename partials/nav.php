<div class="navbar">
    <a href="index.php">
        TASK MANAGEMENT
    </a>
    <?php if ($_SESSION['is_admin']){ ?>
        <a href="users.php" class="<?php echo $_SESSION['nav'] == 'users' ? "active" : ""?>">
            Users
        </a>
    <?php }?>
    <a href="profile.php" class="<?php echo $_SESSION['nav'] == 'profile' ? "active" : ""?>">
        <span class="fa fa-user">
    </a>
</div>