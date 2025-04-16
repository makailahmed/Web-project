<?php
    session_start();

    if (isset($_SESSION['admin_id']) ){
        header('Location: dashboard.php');
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" href="style.css">
    <title>Admin Login</title>
</head>

<body>
    <?php
    if (isset($_SESSION['error'])) {
        echo "<div class='error-message'>" . $_SESSION['error'] . "</div>";
        unset($_SESSION['error']);
    }

    if (isset($_SESSION['success'])) {
        echo "<div class='success-message'>" . $_SESSION['success'] . "</div>";
        unset($_SESSION['success']);
    }
    ?>

    <div class='single-page-form-content'>
        <div class="form-container">
            <h2>Login to Your Admin Account</h2>

            <form action="admin_auth.php" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required />
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required />
                </div>
                <button type="submit" class="form-btn">Login</button>
            </form>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>
