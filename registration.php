<?php
session_start();
require 'DB.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $phone = htmlspecialchars($_POST['phone']);
    $address = htmlspecialchars($_POST['address']);

    if ($password !== $confirmPassword) {
        $_SESSION['error'] = 'Passwords do not match!';
        header('Location: register.php');
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare('INSERT INTO users (name, email, password, phone, address) VALUES (?, ?, ?, ?, ?)');
    $stmt->bind_param('sssss', $name, $email, $hashedPassword, $phone, $address);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Registration successful! Please login.';
        header('Location: login.php');
    } else {
        $_SESSION['error'] = 'Registration failed! Try again.';
        header('Location: registration.php');
    }

    $stmt->close();
    $db->close();
}
?>
