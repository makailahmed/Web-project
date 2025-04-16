<?php
session_start();
require "DB.php";

if (!isset($_SESSION["user_id"])){
  header('Location: login.php');
}

$userId = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT phone, address FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($phone, $address);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Confirm Purchase</title>
  <style>
    .total-section {
        text-align: right;
        margin-top: 2rem;
        font-size: 1.2rem;
        color: #2c3e50;
    }

    .container {
      margin: 2rem auto;
      max-width: 800px;
      background: #fff;
      padding: 2rem;
      border-radius: 12px;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }

    h2 {
      text-align: center;
      color: #2c3e50;
    }

    .section {
      margin-top: 2rem;
    }

    .product-list {
      display: grid;
      grid-template-columns: 80px 1fr;
      gap: 1rem;
      margin-bottom: 1.5rem;
      border-bottom: 1px solid #eee;
      padding-bottom: 1rem;
    }

    .product-list img {
      width: 100%;
      border-radius: 8px;
      object-fit: cover;
    }

    .product-info h4 {
      margin: 0;
      color: #34495e;
    }

    .product-info p {
      margin: 0.3rem 0;
      font-size: 0.95rem;
      color: #7f8c8d;
    }

    label {
      font-weight: 600;
      margin-top: 1rem;
      display: block;
      color: #2c3e50;
    }

    input[type="text"],
    textarea {
      width: 100%;
      padding: 0.8rem;
      border-radius: 8px;
      border: 1px solid #ccc;
      margin-top: 0.5rem;
      font-size: 1rem;
    }

    .options label {
      display: flex;
      align-items: center;
      margin-top: 0.5rem;
    }

    input[type="radio"] {
      margin-right: 0.5rem;
    }

    .transaction-id,
    .custom-phone,
    .custom-address {
      display: none;
      margin-top: 1rem;
    }

    button {
      width: 100%;
      margin-top: 2rem;
      padding: 1rem;
      background-color: #27ae60;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 1.2rem;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    button:hover {
      background-color: #219150;
    }

    .note {
      margin-top: 1rem;
      font-size: 0.9rem;
      color: #7f8c8d;
      text-align: center;
    }
  </style>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <?php require "navbar.php" ?>

  <div class="container">
    <h2>Order Confirmation</h2>

    <form action="payment_auth.php" method="POST" onsubmit="return submitOrder();">
      <!-- Product Details -->
      <div class="section">
        <h3>Product Details</h3>
        <?php
        if (isset($_POST['order_data'])) {
          $_POST['order'] = json_decode($_POST["order_data"], true);
        }
        $finalPrice = 0;
        $orderJson = json_encode($_POST['order']); // For sending in a hidden input

        foreach ($_POST['order'] as $order) {
          $sql = "SELECT * FROM products WHERE id = ?";
          $stmt = $conn->prepare($sql);
          $stmt->bind_param("i", $order['product_id']);
          $stmt->execute();
          $result = $stmt->get_result();
          $stmt->close();

          if ($result->num_rows === 0) {
            die("Product not found.");
          }

          $product = $result->fetch_assoc();

          $image = isset($product['image_path']) ? htmlspecialchars($product['image_path']) : 'images/default.png';
          $name = isset($product['product_name']) ? htmlspecialchars($product['product_name']) : 'Unnamed Product';
          $brand = isset($product['brand']) ? htmlspecialchars($product['brand']) : 'No description';
          $price = isset($product['price']) ? htmlspecialchars($product['price']) : '0.00';

          $subtotal = $price * $order['total'];
          $finalPrice += $subtotal;

          echo '
          <div class="product-list">
            <img src="' . $image . '" alt="Product Image">
            <div class="product-info">
              <h4>Product Name: ' . $name . '</h4>
              <p>Brand: ' . $brand . '</p>
              <p>Quantity: ' . $order["total"] . '</p>
              <h5 style="margin:0;">Price: ' . $subtotal . '$</h5>
            </div>
          </div>';
        }
        ?>
        <div class="total-section">
          Total: $<span id="totalPrice"> <?php echo $finalPrice ?></span>
        </div>
      </div>

      <!-- Delivery Info -->
      <div class="section">
        <h3>Delivery Information</h3>

        <label>Phone Number:</label>
        <div class="options">
          <label><input type="radio" name="phoneOption" value="saved" checked onclick="toggleCustomPhone()"> Use Saved Phone (<?php echo $phone ?>)</label>
          <label><input type="radio" name="phoneOption" value="custom" onclick="toggleCustomPhone()"> Use Custom Phone</label>
        </div>
        <div class="custom-phone" id="customPhoneField" style="display: none;">
          <input type="text" id="customPhone" placeholder="+1234567890">
        </div>

        <label>Address:</label>
        <div class="options">
          <label><input type="radio" name="addressOption" value="saved" checked onclick="toggleCustomAddress()"> Use Saved Address (<?php echo $address ?>)</label>
          <label><input type="radio" name="addressOption" value="custom" onclick="toggleCustomAddress()"> Use Custom Address</label>
        </div>
        <div class="custom-address" id="customAddressField" style="display: none;">
          <textarea id="customAddress" rows="3" placeholder="House, Street, City, Postal Code"></textarea>
        </div>
      </div>

      <!-- Payment Info -->
      <div class="section">
        <h3>Payment Method</h3>
        <div class="options">
          <label><input type="radio" name="payment" value="cod" checked onclick="toggleTransactionId()"> Cash on Delivery</label>
          <label><input type="radio" name="payment" value="online" onclick="toggleTransactionId()"> Online Payment</label>
        </div>
        <div class="transaction-id" id="transactionField" style="display: none;">
          <label for="transactionInput">Transaction ID:</label>
          <input type="text" id="transactionInput" name="transaction_id" placeholder="e.g., TXN12345678">
        </div>
      </div>

      <!-- Hidden Inputs to Send -->
      <input type="hidden" name="order_data" id="orderData" value='<?php echo htmlspecialchars($orderJson, ENT_QUOTES); ?>'>
      <input type="hidden" name="phone" id="finalPhone">
      <input type="hidden" name="address" id="finalAddress">
      <input type="hidden" name="total_price" id="finalTotal" value="<?php echo $finalPrice ?>">

      <button type="submit">Place Order</button>
      <p class="note">You'll receive a confirmation message after order is placed.</p>
    </form>
  </div>

    <script>
      function toggleTransactionId() {
        const field = document.getElementById("transactionField");
        const online = document.querySelector('input[value="online"]');
        field.style.display = online.checked ? "block" : "none";
      }

      function toggleCustomPhone() {
        const field = document.getElementById("customPhoneField");
        const custom = document.querySelector('input[name="phoneOption"][value="custom"]');
        field.style.display = custom.checked ? "block" : "none";
      }

      function toggleCustomAddress() {
        const field = document.getElementById("customAddressField");
        const custom = document.querySelector('input[name="addressOption"][value="custom"]');
        field.style.display = custom.checked ? "block" : "none";
      }

      function submitOrder() {
        const savedPhone = "<?php echo $phone ?>";
        const savedAddress = "<?php echo $address ?>";

        const phoneOption = document.querySelector('input[name="phoneOption"]:checked').value;
        const addressOption = document.querySelector('input[name="addressOption"]:checked').value;

        document.getElementById("finalPhone").value =
          phoneOption === "custom" ? document.getElementById("customPhone").value : savedPhone;

        document.getElementById("finalAddress").value =
          addressOption === "custom" ? document.getElementById("customAddress").value : savedAddress;

        return true; // Allow form to submit
      }
    </script>

</body>

</html>