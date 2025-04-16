<?php
session_start();
require_once 'DB.php';

print_r($_POST);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error'] = 'First Login to Submit a Review.';
        header('Location: login.php');
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

    if ($rating < 1 || $rating > 5 || empty($comment) || $product_id <= 0) {
        $_SESSION['error'] = 'Something Went Wrong While Submitting Review';
        header("Location: product.php?id=" . $product_id);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO reviews (user_id, product_id, rate, comment) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $user_id, $product_id, $rating, $comment);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Successfully Review Submitted';
        header("Location: product.php?id=" . $product_id);
        exit;
    } else {
        $_SESSION['error'] = $stmt->error;
        header("Location: product.php?id=" . $product_id);
        exit;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
