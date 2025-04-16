<?php
    include 'config.php';
?>

<nav class="navbar">
    <div class="navbar-container">
        <a href="index.php" class="logo"><?php echo $website_title ?></a>

        
        <ul class="nav-links" id="navLinks">
            <?php
                if (isset($_SESSION['admin_id']) ){
                    echo "<li><a href='dashboard.php'>Dashboard</a></li>";

                    echo "<li><a href='order_history.php'>Order History</a></li>";
                }
            ?>
            <li><a href="index.php">Home</a></li>
            <li><a href="cart.php">Cart</a></li>
            <?php
                if ( isset($_SESSION['user_id']) || isset($_SESSION['admin_id']) ){
                    echo "<li><a href='logout.php'>Log Out</a></li>";
                }
                else{
                    echo "<li><a href='login.php'>Sign Up</a></li>";
                }
            ?>
            
        </ul>

        <div class="menu-toggle" id="menuToggle">&#9776;</div>
    </div>
</nav>


<?php
    if (isset($_SESSION['error'])) {
        echo "<div class='error-message'>" . $_SESSION['error'] . "</div>";
        unset($_SESSION['error']);
    }

    if (isset($_SESSION['success'])) {
        echo "<div class='success-message'>" . $_SESSION['success'] . "</div>";
        unset($_SESSION['success']);
    }
?>