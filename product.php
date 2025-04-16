<?php
session_start();
require_once "DB.php";

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 1;

$sql = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Product not found.");
}

$product = $result->fetch_assoc();

$image = isset($product['image_path']) ? htmlspecialchars($product['image_path']) : 'images/default.png';
$name = isset($product['product_name']) ? htmlspecialchars($product['product_name']) : 'Unnamed Product';
$desc = isset($product['description']) ? htmlspecialchars($product['description']) : 'No description';
$brand = isset($product['brand']) ? htmlspecialchars($product['brand']) : 'No description';
$price = isset($product['price']) ? htmlspecialchars($product['price']) : '0.00';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Product Detail</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            width: 85%;
            margin: 3rem auto;
            padding: 2rem;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        h1,
        h2 {
            font-family: 'Montserrat', sans-serif;
            color: #333;
        }

        /* Product Header */
        .product-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
        }

        /* Image Carousel */
        .carousel {
            position: relative;
            width: 350px;
            height: 350px;
            overflow: hidden;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
            cursor: pointer;
        }

        .carousel img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease-in-out;
        }

        .carousel-buttons {
            position: absolute;
            top: 50%;
            width: 100%;
            display: flex;
            justify-content: space-between;
            transform: translateY(-50%);
        }

        .carousel-button {
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
            border-radius: 50%;
        }

        .product-header .details {
            flex: 1;
            padding-left: 2rem;
        }

        .product-header h1 {
            font-size: 2.5rem;
            margin: 0;
            font-weight: bold;
        }

        .product-header p {
            font-size: 1.1rem;
            color: #777;
            margin: 1rem 0;
        }

        .product-header .price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #e74c3c;
            margin: 1rem 0;
        }

        /* Overall Rating */
        .overall-rating {
            display: flex;
            align-items: center;
            font-size: 1.3rem;
            color: #f39c12;
            margin-top: 1rem;
        }

        .overall-rating .stars {
            margin-right: 10px;
        }

        .overall-rating .stars span {
            color: #f39c12;
            font-size: 1.5rem;
        }

        /* Product Info Section */
        .product-info {
            display: flex;
            justify-content: space-between;
            gap: 2rem;
            margin-top: 2rem;
        }

        .product-info .description {
            flex: 2;
        }

        .product-info .specifications {
            flex: 1;
            background-color: #fafafa;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .description p {
            line-height: 1.8;
            color: #555;
            font-size: 1rem;
        }

        .specifications ul {
            list-style: none;
            padding: 0;
        }

        .specifications li {
            background-color: #fff;
            padding: 0.8rem;
            margin: 0.5rem 0;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .specifications li strong {
            color: #333;
            font-weight: bold;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 3rem;
        }

        .action-buttons button {
            padding: 1rem 2rem;
            font-size: 1.2rem;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            flex: 1;
        }

        .action-buttons form {
            flex: 1;
        }

        .buy-btn {
            width: 100%;
            height: 100%;
            margin-top: 0rem;
            background-color: #3498db;
            color: white;
        }

        .buy-btn:hover {
            background-color: #2980b9;
            transform: scale(1.05);
        }

        .wishlist-btn {
            width: 100%;
            height: 100%;
            margin-top: 0rem;
            background-color: #f39c12;
            color: white;
        }

        .wishlist-btn:hover {
            background-color: #e67e22;
            transform: scale(1.05);
        }

        /* Reviews Section */
        .reviews {
            margin-top: 4rem;
            padding-top: 2rem;
            border-top: 2px solid #ddd;
        }

        .reviews h3 {
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 1rem;
            font-weight: bold;
        }

        .review-item {
            display: flex;
            justify-content: space-between;
            padding: 1rem 0;
            border-bottom: 1px solid #eee;
        }

        .review-item p {
            font-size: 1rem;
            color: #777;
            max-width: 80%;
        }

        .review-item .review-author {
            font-weight: bold;
            color: #3498db;
        }

        .review-item .review-date {
            color: #aaa;
            font-size: 0.9rem;
        }

        .review-item .rating {
            color: #f39c12;
        }

        /* Modal for Image Full Preview */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            justify-content: center;
            align-items: center;
            transition: opacity 0.3s ease;
        }

        .modal-content {
            position: relative;
            max-width: 90%;
            max-height: 80%;
            background-color: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }

        .modal-content img {
            width: 100%;
            height: auto;
            max-height: 80vh;
            object-fit: contain;
            border-radius: 8px;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 20px;
            color: white;
            font-size: 2rem;
            cursor: pointer;
            background-color: transparent;
            border: none;
            z-index: 10;
        }

        /* Rating System */
        .rating-form {
            background-color: #fafafa;
            padding: 2rem;
            margin-top: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .rating-form h3 {
            font-size: 1.6rem;
            color: #333;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .stars {
            display: flex;
            gap: 0.5rem;
            font-size: 2rem;
            color: #ccc;
            cursor: pointer;
        }

        .stars .star:hover,
        .stars .star.selected {
            color: #f39c12;
        }

        .stars .star {
            transition: color 0.3s ease;
        }

        textarea {
            width: 100%;
            padding: 1rem;
            margin-top: 1rem;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 1rem;
        }

        button {
            margin-top: 1rem;
            padding: 1rem 2rem;
            background-color: #3498db;
            color: white;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-size: 1.2rem;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #2980b9;
        }


        .quantity-selector {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin: 1rem 0;
        }

        .quantity-selector button {
            padding: 6px 12px;
            font-size: 1.2rem;
            background-color: #3498db;
            border: none;
            border-radius: 6px;
            color: white;
            cursor: pointer;
            transition: background-color 0.2s;
            margin-top: 0rem;
        }

        .quantity-selector button:hover {
            background-color: #2980b9;
        }

        .quantity-selector span {
            font-size: 1.2rem;
            min-width: 30px;
            text-align: center;
            display: inline-block;
        }
    </style>
</head>

<body>
    <?php require "navbar.php"; ?>
    
    <div class="container">
        <div class="product-header">
            <div class="carousel">
                <img id="productImage" src="<?php echo $image ?>" alt="Product Image" onclick="openModal()" />
            </div>
            <div class="details">
                <h1><?php echo $name ?></h1>
                <div class="overall-rating">
                    <?php
                        $product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

                        if ($product_id > 0) {
                            // Get average rating and total number of reviews
                            $stmt = $conn->prepare("SELECT AVG(rate) AS avg_rating, COUNT(*) AS total_reviews FROM reviews WHERE product_id = ?");
                            $stmt->bind_param("i", $product_id);
                            $stmt->execute();
                            $stmt->bind_result($avg_rating, $total_reviews);
                            $stmt->fetch();
                            $stmt->close();

                            if ($total_reviews > 0) {
                                $avg_rating = round($avg_rating, 1);
                                $full_stars = floor($avg_rating);
                                $has_half_star = ($avg_rating - $full_stars) >= 0.5;
                                $empty_stars = 5 - $full_stars - ($has_half_star ? 1 : 0);

                                $star_display = str_repeat("★", $full_stars);
                                if ($has_half_star) $star_display .= "☆";
                                $star_display .= str_repeat("☆", $empty_stars);
                                ?>

                                <div class="stars">
                                    <span><?php echo $star_display; ?></span>
                                </div>
                                <span>(<?php echo $avg_rating; ?>/5) Based on <?php echo $total_reviews; ?> reviews</span>

                                <?php
                            } else {
                                echo "<p>No ratings yet.</p>";
                            }
                        }
                    ?>


                </div>
                <p><?php echo $brand ?></p>
                <p class="price"><?php echo $price ?></p>
            </div>
        </div>

        <div class="product-info">
            <div class="description">
                <h2>Description</h2>
                <p><?php echo $desc ?></p>
            </div>
        </div>

        <div class="action-buttons">
            <div class="quantity-selector">
                <button onclick="changeQuantity(-1)">−</button>
                <label id="quantity">1</label>
                <button onclick="changeQuantity(1)">+</button>
            </div>

            <form action="payment.php" method="POST" onsubmit="fillInfo()">
                <input type="hidden" id="total" name="order[0][total]" value="1">
                <input type="hidden" name="order[0][product_id]" value="<?php echo $_GET['id'] ?>">
                <button class="buy-btn">Buy Now</button>
            </form>

            <form action="cart_auth.php" method="POST">
                <input type="hidden" name="product_id" value="<?php echo $_GET['id'] ?>">
                

                <?php
                    $stmt = $conn->prepare("SELECT 1 FROM cart WHERE userId = ? AND productID = ?");
                    $stmt->bind_param("ii", $_SESSION['user_id'], $_GET['id']);
                    $stmt->execute();
                    $stmt->store_result();

                    if ($stmt->num_rows == 0) {
                        echo '<button class="wishlist-btn">Add to Cart</button>';
                    }else{
                        echo '<button class="wishlist-btn">Remove from Cart</button>';
                    }
                    $stmt->close();
                ?>
            </form>
        </div>

        <?php
            $user_id = $_SESSION['user_id'] ?? null;
            $product_id = $_GET['id'] ?? null;

            $stmt = $conn->prepare("SELECT r.*, u.name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.user_id = ? AND r.product_id = ?");
            $stmt->bind_param("ii", $user_id, $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $existingReview = $result->fetch_assoc();
            $stmt->close();

            if ($existingReview):
                $author = htmlspecialchars($existingReview['name']);
                $date = date("F j, Y", strtotime($existingReview['created_at']));
                $comment = nl2br(htmlspecialchars($existingReview['comment']));
                $rating = (int)$existingReview['rate'];
                $stars = str_repeat("★", $rating) . str_repeat("☆", 5 - $rating);
            ?>

                <div class="reviews">
                    <h3>Your Review</h3>
                    <div class="review-item">
                        <div>
                            <p class="review-author"><?php echo $author; ?></p>
                            <p class="review-date"><?php echo $date; ?></p>
                        </div>
                        <p><?php echo $comment; ?></p>
                        <div class="rating"><?php echo $stars; ?></div>
                    </div>
                </div>

            <?php else: ?>

                <div class="rating-form">
                    <h3>Rate the Product</h3>

                    <form id="reviewForm" method="POST" action="submit_review.php">
                        <div class="stars" id="stars">
                            <span class="star" data-value="1">★</span>
                            <span class="star" data-value="2">★</span>
                            <span class="star" data-value="3">★</span>
                            <span class="star" data-value="4">★</span>
                            <span class="star" data-value="5">★</span>
                        </div>

                        <input type="hidden" id="ratingValue" name="rating" value="5" />
                        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product_id); ?>" />

                        <textarea id="commentBox" name="comment" placeholder="Write your comment here..." rows="4" required></textarea>

                        <button type="submit">Submit Review</button>
                    </form>
                </div>
        <?php endif; ?>



        <?php

            $product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

            if ($product_id > 0) {
                $stmt = $conn->prepare("
                    SELECT r.rate, r.comment, r.created_at, u.name 
                    FROM reviews r
                    JOIN users u ON r.user_id = u.id
                    WHERE r.product_id = ?
                    ORDER BY r.created_at DESC
                ");
                $stmt->bind_param("i", $product_id);
                $stmt->execute();
                $result = $stmt->get_result();

                echo '<div class="reviews">';
                echo '<h3>Customer Reviews</h3>';

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $author = htmlspecialchars($row['name']);
                        $date = date("F j, Y", strtotime($row['created_at']));
                        $comment = nl2br(htmlspecialchars($row['comment']));
                        $rating = (int)$row['rate'];

                        // Generate star string like ★★★★☆
                        $stars = str_repeat("★", $rating) . str_repeat("☆", 5 - $rating);

                        echo '<div class="review-item">';
                        echo '    <div>';
                        echo '        <p class="review-author">' . $author . '</p>';
                        echo '        <p class="review-date">' . $date . '</p>';
                        echo '    </div>';
                        echo '    <p>' . $comment . '</p>';
                        echo '    <div class="rating">' . $stars . '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No reviews yet. Be the first to leave one!</p>';
                }

                echo '</div>';

                $stmt->close();
            } else {
                echo "<p>Invalid product.</p>";
            }

        ?>
    </div>

    <div id="imageModal" class="modal">
        <div class="modal-content">
            <img id="modalImage" src="<?php echo $image ?>" alt="Full Product Image" />
            <button class="close-btn" onclick="closeModal()">×</button>
        </div>
    </div>

    <script>
        function openModal() {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            modal.style.display = 'flex';
            modalImage.src = document.getElementById('productImage').src;
        }

        function closeModal() {
            const modal = document.getElementById('imageModal');
            modal.style.display = 'none';
        }


        const stars = document.querySelectorAll(".star");
        const ratingValueInput = document.getElementById("ratingValue");
        const commentBox = document.getElementById("commentBox");

        stars.forEach(star => {
            star.addEventListener("click", function() {
                const rating = this.getAttribute("data-value");
                ratingValueInput.value = rating;
                updateStars(rating);
            });

            star.addEventListener("mouseover", function() {
                const rating = this.getAttribute("data-value");
                updateStars(rating);
            });

            star.addEventListener("mouseout", function() {
                const rating = ratingValueInput.value;
                updateStars(rating);
            });
        });

        updateStars(5);
        function updateStars(rating) {
            stars.forEach(star => {
                const starValue = star.getAttribute("data-value");
                if (starValue <= rating) {
                    star.classList.add("selected");
                } else {
                    star.classList.remove("selected");
                }
            });
        }

        let quantity = 1;

        function changeQuantity(delta) {
            quantity = Math.max(1, quantity + delta); // minimum quantity is 1
            document.getElementById('quantity').textContent = quantity;
            document.getElementById('total').value = quantity;
        }
    </script>

</body>

</html>