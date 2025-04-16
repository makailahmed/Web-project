<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" href="style.css">
    <title>Login</title>
</head>

<body>
    <?php
        session_start();
        include 'navbar.php'; 
    ?>

    <div class='single-page-form-content'>
        <div class="form-container">
            <h2>Login to Your Account</h2>

            <form action="signin.php" method="POST">
                <div class="form-group">
                    <label for="email">Email or Username</label>
                    <input type="text" id="email" name="email" required />
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required />
                </div>

                <button type="submit" class="form-btn">Login</button>

                <div class="form-footer">
                    Don't have an account? <a href="register.php">Register</a>
                </div>
            </form>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>
