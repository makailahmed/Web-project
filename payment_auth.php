<?php
include("DB.php");
session_start();

if (
    isset($_POST['order_data']) &&
    isset($_POST['phone']) &&
    isset($_POST['address']) &&
    isset($_POST['payment']) &&
    isset($_POST['total_price'])
) {
    $orderData = json_decode($_POST['order_data'], true);
    $phone = htmlspecialchars($_POST['phone']);
    $address = htmlspecialchars($_POST['address']);
    $payment = htmlspecialchars($_POST['payment']);
    $transaction_id = isset($_POST['transaction_id']) ? htmlspecialchars($_POST['transaction_id']) : null;
    $total_price = floatval($_POST['total_price']);

    $product_ids = [];
    $quantities = [];

    foreach ($orderData as $item) {
        $product_ids[] = intval($item['product_id']);
        $quantities[] = intval($item['total']);
    }

    $product_ids_str = implode(',', $product_ids);
    $quantities_str = implode(',', $quantities);

    $sql = "INSERT INTO orders (user_id, product_ids, quantities, phone, address, payment_method, transaction_id, total_price, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssd", $_SESSION['user_id'], $product_ids_str, $quantities_str, $phone, $address, $payment, $transaction_id, $total_price);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Your order Payment was Successful';
        header('Location: index.php');
        exit;
    } else {        
        $_SESSION['error'] = 'Invalid Order!!';
        header('Location: index.php');
        exit;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<h2>Error</h2>";
    echo "<p>Missing order information.</p>";
}
?>
