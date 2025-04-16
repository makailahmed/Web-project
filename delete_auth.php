<?php
session_start();
include 'DB.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);

    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Your Product was Deleted Successfully';
            header('Location: dashboard.php');
            exit;
        } else {
            $_SESSION['error'] = 'Your Product was not Deleted';
            header('Location: dashboard.php');
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
