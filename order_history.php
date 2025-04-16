<?php
session_start();
include("DB.php");
$sql = "SELECT * FROM orders ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Order Management</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #f4f6f9;
      margin: 0;
      padding: 0;
    }

    .container {
      max-width: 1200px;
      margin: 40px auto;
      padding: 30px;
      background-color: #fff;
      box-shadow: 0 8px 20px rgba(0,0,0,0.08);
      border-radius: 12px;
    }

    h2 {
      font-size: 28px;
      color: #333;
      margin-bottom: 20px;
      font-weight: 600;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th, td {
      padding: 14px 16px;
      text-align: left;
      border-bottom: 1px solid #e2e8f0;
    }

    th {
      background-color: #f9fafb;
      font-weight: 600;
      color: #444;
    }

    tr:hover {
      background-color: #f1f5f9;
    }

    td {
      color: #555;
      vertical-align: top;
    }

    .badge {
      display: inline-block;
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 12px;
      background-color: #e0f2f1;
      color: #00695c;
    }

    .payment-method-cod {
      background-color: #fff3cd;
      color: #856404;
    }

    .payment-method-online {
      background-color: #d1ecf1;
      color: #0c5460;
    }

    .footer-note {
      margin-top: 30px;
      font-size: 14px;
      color: #999;
      text-align: center;
    }
  </style>
  <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
        include ("navbar.php");
    ?>
  <div class="container">
    <h2>🧾 All Customer Orders</h2>

    <?php if ($result->num_rows > 0): ?>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>User ID</th>
          <th>Product IDs</th>
          <th>Quantities</th>
          <th>Phone</th>
          <th>Address</th>
          <th>Payment</th>
          <th>Transaction ID</th>
          <th>Total ($)</th>
          <th>Ordered At</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['id']) ?></td>
          <td><?= htmlspecialchars($row['user_id']) ?></td>
          <td><?= htmlspecialchars($row['product_ids']) ?></td>
          <td><?= htmlspecialchars($row['quantities']) ?></td>
          <td><?= htmlspecialchars($row['phone']) ?></td>
          <td><?= nl2br(htmlspecialchars($row['address'])) ?></td>
          <td>
            <span class="badge <?= $row['payment_method'] == 'online' ? 'payment-method-online' : 'payment-method-cod' ?>">
              <?= ucfirst($row['payment_method']) ?>
            </span>
          </td>
          <td><?= $row['transaction_id'] ? htmlspecialchars($row['transaction_id']) : '—' ?></td>
          <td><strong>$<?= number_format($row['total_price'], 2) ?></strong></td>
          <td><?= htmlspecialchars($row['created_at']) ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
    <?php else: ?>
      <p>No orders found.</p>
    <?php endif; ?>

    <div class="footer-note">
      &copy; <?= date("Y") ?> Your Store Admin. All rights reserved.
    </div>
  </div>
</body>
</html>

<?php $conn->close(); ?>
