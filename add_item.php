<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Item</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
        session_start();
        if (!isset($_SESSION['admin_id']) ){
            header('Location: admin_login.php');
        }

        include 'navbar.php';
    ?>
    <div class='signle-page-form-container'>
        <div class="form-container">
            <h2>ADD a new Item</h2>
            <form action="add_item_auth.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="product_name">Product Name</label>
                    <input type="text" id="product_name" name="product_name" required />
                </div>

                <div class="form-group">
                    <label for="brand">Brand</label>
                    <input type="text" id="brand" name="brand" required />
                </div>

                <div class="form-group">
                    <label for="price">Price ($)</label>
                    <input type="number" step="0.01" id="price" name="price" required />
                </div>

                <div class="form-group">
                    <label for="stock">Stock Quantity</label>
                    <input type="number" id="stock" name="stock" required />
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3" required></textarea>
                </div>

                <div class="form-group">
                    <label for="image">Product Image</label>
                    <input type="file" id="image" name="image" accept="image/*" required />
                </div>

                <button type="submit" class="form-btn">Add Product</button>
            </form>
        </div>
    </div>
    

</body>
</html>