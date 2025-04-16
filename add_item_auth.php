<?php
session_start();
require 'DB.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = $_POST['product_name'];
    $brand = $_POST['brand'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $description = $_POST['description'];


    $image = $_FILES['image']['name'];
    $tmp_name = $_FILES['image']['tmp_name'];
    $target_dir = "uploads/";

    $image_ext = pathinfo($image, PATHINFO_EXTENSION);
    $new_name = uniqid("img_", true) . "." . $image_ext;
    $target_file = $target_dir . $new_name;


    if (move_uploaded_file($tmp_name, $target_file)) {

        $stmt = $conn->prepare("INSERT INTO products (product_name, brand, price, stock, description, image_path) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssddss", $product_name, $brand, $price, $stock, $description, $target_file);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'Your Product was added Successfully';
            header('Location: dashboard.php');
            exit;
        } else {
            $_SESSION['error'] = 'Your Product was not added';
            header('Location: dashboard.php');
        }

        $stmt->close();
    } else {
        echo "Image upload failed.";
    }
}

$conn->close();
?>
