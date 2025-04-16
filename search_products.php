<?php
session_start();
require 'DB.php'; // Make sure this connects $conn

$search = isset($_GET['search']) ? "%" . $conn->real_escape_string($_GET['search']) . "%" : '%';

$sql = "SELECT * FROM products WHERE product_name LIKE ? OR description LIKE ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $search, $search);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $image = isset($row['image_path']) ? htmlspecialchars($row['image_path']) : 'images/default.png';
        $name = isset($row['product_name']) ? htmlspecialchars($row['product_name']) : 'Unnamed Product';
        $desc = isset($row['description']) ? htmlspecialchars($row['description']) : 'No description';
        $price = isset($row['price']) ? htmlspecialchars($row['price']) : '0.00';

        echo '
        <div class="grid-item">
            <img src="' . $image . '" alt="' . $name . '">
            <div class="content">
                <h3>' . $name . '</h3>
                <p>' . $desc . '</p>
                <h6>$' . $price . '</h6>
                <div class="button-group">
                    <form action="cart_auth.php" method="POST" style="display:inline;">
                        <input type="hidden" name="product_id" value="'. $row["id"] .'">';
        
        $stmt2 = $conn->prepare("SELECT 1 FROM cart WHERE userId = ? AND productID = ?");
        $stmt2->bind_param("ii", $_SESSION['user_id'], $row['id']);
        $stmt2->execute();
        $stmt2->store_result();

        if ($stmt2->num_rows == 0) {
            echo '<button class="buy-btn">Add to Cart</button>';
        } else {
            echo '<button class="buy-btn">Remove from Cart</button>';
        }
        $stmt2->close();

        echo '
                    </form>
                    <a href="product.php?id=' . $row['id'] . '"><button style="height:100%" class="details-btn">Details</button></a>
                </div>
            </div>
        </div>';
    }
} else {
    echo "<p>No products found.</p>";
}
?>
