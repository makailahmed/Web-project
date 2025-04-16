<?php
    session_start();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if ($_POST['username'] == "admin" and $_POST['password'] == "admin"){
            $_SESSION['admin_id'] = 'admin';
            header('Location: dashboard.php');
        }
    }
?>