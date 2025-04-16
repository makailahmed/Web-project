<?php
session_start();
require "DB.php";

if (!isset($_SESSION["user_id"])) {
    header('Location: login.php');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Your Cart</title>
    <link rel="stylesheet" href="style.css">

    <style>
        .cart-container {
            max-width: 900px;
            margin: 2rem auto;
            background: #fff;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        }

        h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #2c3e50;
        }

        .cart-item {
            display: grid;
            grid-template-columns: 80px 1fr 120px 140px 40px;
            gap: 1rem;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #eee;
        }

        .cart-item img {
            width: 100%;
            border-radius: 8px;
            object-fit: cover;
        }

        .cart-item h4 {
            margin: 0;
            font-size: 1.1rem;
            color: #34495e;
        }

        .cart-item p {
            margin: 0.3rem 0;
            font-size: 0.9rem;
            color: #7f8c8d;
        }

        .quantity {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .quantity button {
            background-color: #3498db;
            border: none;
            color: white;
            padding: 6px 10px;
            font-size: 1rem;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .quantity button:hover {
            background-color: #2980b9;
        }

        .quantity span {
            min-width: 30px;
            text-align: center;
            font-weight: bold;
        }

        .remove-btn {
            background: none;
            border: none;
            font-size: 1.2rem;
            color: #e74c3c;
            cursor: pointer;
        }

        .total-section {
            text-align: right;
            margin-top: 2rem;
            font-size: 1.2rem;
            color: #2c3e50;
        }

        .checkout-btn {
            display: block;
            width: 100%;
            margin-top: 1.5rem;
            padding: 1rem;
            background: #27ae60;
            color: white;
            font-size: 1.2rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .checkout-btn:hover {
            background: #219150;
        }

        @media(max-width: 600px) {
            .cart-item {
                grid-template-columns: 60px 1fr;
                grid-template-rows: auto auto auto;
                gap: 0.5rem;
            }

            .cart-item div:nth-child(3),
            .cart-item .quantity,
            .cart-item .remove-btn {
                grid-column: span 2;
                text-align: right;
            }
        }
    </style>
</head>

<body>
    <?php require("navbar.php"); ?>

    <div class="cart-container">
    <h2>🛒 Your Cart</h2>

    <div id="cartItems">
        <?php
        $stmt = $conn->prepare("SELECT productID FROM cart WHERE userId = ?");
        $stmt->bind_param("i", $_SESSION["user_id"]);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 0) {
            echo "<p>No Wishlisted Product Available</p>";
        } else {
            $stmt->bind_result($product_id);
            $index = 0;

            while ($stmt->fetch()) {
                $sql = "SELECT * FROM products WHERE id = ?";
                $prod = $conn->prepare($sql);
                $prod->bind_param("i", $product_id);
                $prod->execute();
                $result = $prod->get_result();

                if ($row = $result->fetch_assoc()) {
                    $item = [
                        "image" => $row["image_path"],
                        "name" => $row["product_name"],
                        "description" => $row["description"],
                        "price" => $row["price"],
                        "quantity" => 1
                    ];

                    echo '<div class="cart-item">';
                    echo    '<img src="' . htmlspecialchars($item["image"]) . '" alt="' . htmlspecialchars($item["name"]) . '">';
                    echo    '<div>';
                    echo        '<h4>' . htmlspecialchars($item["name"]) . '</h4>';
                    echo        '<p>' . htmlspecialchars($item["description"]) . '</p>';
                    echo    '</div>';
                    echo    '<p id="price_' . $index . '">$' . number_format($item["price"], 2) . '</p>';
                    echo    '<div class="quantity">';
                    echo        '<button type="button" onclick="changeQuantity(' . $index . ', -1)">−</button>';
                    echo        '<input type="hidden" name="product_id" value="' . $product_id . '">';
                    echo        '<input type="hidden" name="quantities[]" value="' . $item["quantity"] . '" id="quantityInput_' . $index . '">';
                    echo        '<span id="quantityDisplay_' . $index . '">' . $item["quantity"] . '</span>';
                    echo        '<button type="button" onclick="changeQuantity(' . $index . ', 1)">+</button>';
                    echo    '</div>';

                    echo    '<form action="cart_auth.php" method="POST">';
                    echo        '<input type="hidden" name="product_id" value="'. $product_id .'">';
                    echo        '<button class="remove-btn" type="submit">&times;</button>';
                    echo    '</form>';

                    echo '</div>';
                }

                $prod->close();
                $index++;
            }
        }

        $stmt->close();
        ?>
    </div>

    <div class="total-section">
        Total: $<span id="totalPrice">0.00</span>
    </div>

    <form id="checkoutForm" action="payment.php" method="POST">
        <input type="hidden" name="order_data" id="orderData">
    </form>

    <button class="checkout-btn" onclick="checkout()">Proceed to Checkout</button>
       
    </div>

    <script>
        const cartContainer = document.getElementById("cartItems");
        const totalPriceEl = document.getElementById("totalPrice");

        function updateTotalPrice() {
            const cartItems = document.querySelectorAll('.cart-item');
            let total = 0;

            cartItems.forEach((item, index) => {
                const price = document.getElementById('price_' + index).innerText;
                const quantity = parseInt(document.getElementById('quantityInput_' + index).value);
                
                let p = parseFloat(price.replace('$', ''));
                total += p * quantity;
            });

            document.getElementById('totalPrice').textContent = total.toFixed(2);
        }


        function changeQuantity(index, delta) {
            const quantitySpan = document.querySelectorAll('.cart-item')[index].querySelector('.quantity span');
            const input = document.getElementById('quantityInput_' + index);

            let quantity = parseInt(quantitySpan.textContent);

            quantity += delta;
            if (quantity < 1) quantity = 1;

            quantitySpan.textContent = quantity;
            input.value = quantity;
            
            updateTotalPrice();
        }

        function checkout() {
            console.log("checkout function triggered");

            const cartItems = document.querySelectorAll('.cart-item');
            const order = [];

            cartItems.forEach((item, index) => {
                const productID = item.querySelector('input[name="product_id"]').value;
                const quantity = item.querySelector('input[name="quantities[]"]').value;

                order.push({
                    product_id: parseInt(productID),
                    total: parseInt(quantity)
                });
            });

            console.log("Order to send:", order);

            document.getElementById('orderData').value = JSON.stringify(order);
            document.getElementById('checkoutForm').submit();
        }

        updateTotalPrice();
    </script>

</body>

</html>