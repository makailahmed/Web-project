<?php
session_start();
require_once "DB.php";

if (!isset($_SESSION["user_id"])){
    header('Location: login.php');
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'];

$stmt = $conn->prepare("SELECT 1 FROM cart WHERE userId = ? AND productID = ?");
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) {
    $insert = $conn->prepare("INSERT INTO cart (userId, productID) VALUES (?, ?)");
    $insert->bind_param("ii", $user_id, $product_id);
    $insert->execute();
}
else{
    $delete = $conn->prepare("DELETE FROM cart WHERE userId = ? AND productID = ?");
    $delete->bind_param("ii", $user_id, $product_id);
    $delete->execute();
    $delete->close();
}
$stmt->close();

if (!empty($_SERVER['HTTP_REFERER'])) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
} else {
    header("Location: index.php");
    exit;
}

?>