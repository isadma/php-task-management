    <script src='assets/js/scripts.js'></script>
    <?php
        if (!strpos($_SERVER['REQUEST_URI'], 'profile.php') && !strpos($_SERVER['REQUEST_URI'], 'users.php'))
            echo "<script src='assets/js/home.js'></script>";
    ?>
</body>
</html>