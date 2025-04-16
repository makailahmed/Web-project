<?php
    session_start();
    include 'config.php';
    require "DB.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?php echo $website_title ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; 
    ?>
    <header class="site-header">
        <div class="search-bar-container">
            <input type="text" id="search-bar" placeholder="🔍 Search for products..." />
        </div>
    </header>

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
                    
                    <form action="cart_auth.php" method="POST" style="display:inline;">
                        <input type="hidden" name="product_id" value="'. $row["id"] .'">';
                    
                
                $stmt = $conn->prepare("SELECT 1 FROM cart WHERE userId = ? AND productID = ?");
                $stmt->bind_param("ii", $_SESSION['user_id'], $row['id']);
                $stmt->execute();
                $stmt->store_result();
    
                if ($stmt->num_rows == 0) {
                    echo '<button class="buy-btn">Add to Cart</button>';
                }else{
                    echo '<button class="buy-btn">Remove from Cart</button>';
                }
                $stmt->close();

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
    </div>


    <script src="script.js"></script>

    <script>
        document.getElementById('search-bar').addEventListener('input', function() {
            const query = this.value;

            fetch('search_products.php?search=' + encodeURIComponent(query))
                .then(response => response.text())
                .then(data => {
                    document.querySelector('.grid-container').innerHTML = data;
                });
        });
    </script>

</body>
</html>
