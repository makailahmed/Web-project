<?php
session_start();
require_once 'DB.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare('SELECT id, name, password FROM users WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($userId, $userName, $hashedPassword);

    
    if ($stmt->fetch()) {
        if (password_verify($password, $hashedPassword)) {
            $_SESSION['user_id'] = $userId;
            header('Location: index.php');
            exit;
        } else {
            $_SESSION['error'] = 'Invalid password!';
            header('Location: login.php');
            exit;
        }
    } else {
        $_SESSION['error'] = 'No user found with this email!';
        header('Location: login.php');
        exit;
    }
}
?>
