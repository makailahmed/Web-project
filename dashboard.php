<?php
    session_start();

    if (!isset($_SESSION['admin_id']) ){
        header('Location: admin_login.php');
    }

    require_once 'config.php';
    require "DB.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?php echo $website_title ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        .delete-btn {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 8px 16px;
            cursor: pointer;
            border-radius: 4px;
        }
        .delete-btn:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; 
    if (isset($_SESSION['error'])) {
        echo "<div class='error-message'>" . $_SESSION['error'] . "</div>";
        unset($_SESSION['error']);
    }

    if (isset($_SESSION['success'])) {
        echo "<div class='success-message'>" . $_SESSION['success'] . "</div>";
        unset($_SESSION['success']);
    }
    ?>
    

    <div class="grid-container">
        <?php
        $sql = "SELECT * FROM products";
        $result = $conn->query($sql);

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
                            <form action="delete_auth.php" method="POST" onsubmit="return confirm(\'Are you sure you want to delete this product?\');">
                                <input type="hidden" name="product_id" value="' . $row["id"] . '">
                                <button class="delete-btn" type="submit">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>';
            }
        } else {
            echo "<p>No products found.</p>";
        }
        ?>
        <div class="grid-item">
            <img alt="add new product">
            <div class="content">
                <h3>Add</h3>
                <p>New Poduct</p>
                <h6>$99.99</h6>
                <div class="button-group">
                    <form action="add_item.php">
                        <button class="buy-btn" type="submit">Add New</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
