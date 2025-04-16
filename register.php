<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php
    session_start(); 
    include 'navbar.php'; 
    ?>

    <div class='signle-page-form-container'>
        <div class="form-container">
            <h2>Create an Account</h2>
            <form action="registration.php" method="POST">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" required />
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required />
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" required />
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required />
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required />
                </div>

                <button type="submit" class="form-btn">Register</button>

                <div class="form-footer">
                    Already have an account? <a href="login.php">Login</a>
                </div>
            </form>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>
