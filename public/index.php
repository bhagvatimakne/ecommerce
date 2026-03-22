<?php
include "../config/session.php";
include "../config/db.php";
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Commerce Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Navbar Styling */
        .navbar {
            background: linear-gradient(135deg, #FB9B8F 0%, #FB9B8F 100%);
            color: white;
            padding: 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 90%;
            margin: 0 auto;
            padding: 15px 0;
        }

        .nav-logo {
            font-size: 24px;
            font-weight: bold;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-logo i {
            font-size: 28px;
        }

        .nav-center {
            flex: 1;
            margin: 0 30px;
        }

        .search-bar {
            display: flex;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 50px;
            padding: 8px 12px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border: 2px solid transparent;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .search-bar:hover {
            border-color: rgba(255, 255, 255, 0.3);
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.15);
        }

        .search-bar:focus-within {
            border-color: #FB9B8F;
            box-shadow: 0 6px 25px rgba(251, 155, 143, 0.2);
        }

        .search-bar input {
            border: none;
            background: none;
            flex: 1;
            outline: none;
            padding: 10px 15px;
            font-size: 16px;
            color: #333;
            font-weight: 400;
        }

        .search-bar input::placeholder {
            color: #999;
            font-weight: 300;
        }

        .search-bar button {
            background: linear-gradient(135deg, #FB9B8F 0%, #FB9B8F 100%);
            border: none;
            color: white;
            cursor: pointer;
            font-size: 16px;
            padding: 10px 20px;
            border-radius: 25px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 50px;
            box-shadow: 0 2px 10px rgba(251, 155, 143, 0.3);
        }

        .search-bar button:hover {
            background: linear-gradient(135deg, #e8877a 0%, #e8877a 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(251, 155, 143, 0.4);
        }

        .search-bar button:active {
            transform: translateY(0);
        }

        .search-bar button i {
            font-size: 14px;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .nav-links {
            display: flex;
            gap: 25px;
            align-items: center;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 10px;
            border-radius: 20px;
        }

        .nav-links a.wishlist-link {
            margin-left: 10px;
            background-color: rgba(255, 255, 255, 0.15);
        }

        .nav-links a:hover {
            color: #ffd700;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -5px;
            left: 0;
            background-color: #ffd700;
            transition: width 0.3s ease;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .cart-icon {
            position: relative;
            font-size: 20px;
        }

        .user-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-welcome {
            font-size: 14px;
            display: none;
        }

        @media (max-width: 768px) {
            .nav-center {
                display: none;
            }

            .nav-container {
                flex-wrap: wrap;
            }

            .nav-links {
                gap: 15px;
                font-size: 14px;
            }

            .user-welcome {
                display: block;
                width: 100%;
                font-size: 12px;
                margin-top: 10px;
            }
        }

        .container {
            width: 90%;
            margin: auto;
            margin-top: 40px;
            margin-bottom: 40px;
            flex: 1;
        }

        .container h1 {
            color: #333;
            margin-bottom: 30px;
            font-size: 32px;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 25px;
        }

        .product {
            position: relative;
            border: none;
            padding: 0;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .wishlist-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.95);
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
            z-index: 5;
            cursor: pointer;
            font-size: 18px;
            transition: all 0.3s ease;
            padding: 0;
            color: #d1d5db;
        }

        .wishlist-btn:hover {
            background: #fff;
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .wishlist-btn.in-wishlist {
            color: #ef4444;
        }

        .wishlist-btn.in-wishlist:hover {
            color: #dc2626;
        }

        .product-image {
            width: 100%;
            height: 250px;
            overflow: hidden;
            border-radius: 10px 10px 0 0;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            margin-top: 12px;
        }

        .action-buttons button,
        .action-buttons a {
            flex: 1;
            padding: 10px 12px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            text-align: center;
            transition: all 0.2s ease;
        }

        .action-buttons .btn-add-cart {
            background: linear-gradient(135deg, #667eea 0%, #5a6bf3 100%);
            color: #fff;
        }

        .action-buttons .btn-add-cart:hover {
            background: linear-gradient(135deg, #5465d3 0%, #4a56ca 100%);
        }

        .action-buttons .btn-buy-now {
            background: linear-gradient(135deg, #ff6b6b 0%, #ff4757 100%);
            color: #fff;
        }

        .action-buttons .btn-buy-now:hover {
            background: linear-gradient(135deg, #ee5a5a 0%, #e04147 100%);
        }

        .product-content {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .product:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .product h3 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 18px;
        }

        .product a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .product a:hover {
            color: #E06B80;
        }

        .product p {
            color: #666;
            margin: 10px 0;
            font-size: 14px;
            line-height: 1.5;
            flex-grow: 1;
        }

        .price {
            font-weight: bold;
            color: #667eea;
            font-size: 20px;
            margin: 15px 0 10px 0;
        }

        .product form button {
            padding: 10px 20px;
            background: linear-gradient(135deg, #FB9B8F 0%, #FFF7CD 100%);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-buy-now {
            background: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            padding: 10px 20px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
        }

        .btn-buy-now:hover {
            background: #218838;
        }

        .delete-button {
            background-color: #dc3545;
            margin-top: 8px;
        }

        .delete-button:hover {
            background-color: #c82333;
        }

        button:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        /* Footer Styling */
        footer {
            background: linear-gradient(135deg, #F57799 0%, #FB9B8F 100%);
            color: white;
            padding: 50px 0 20px 0;
            margin-top: 50px;
        }

        .footer-container {
            width: 90%;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 40px;
            margin-bottom: 30px;
        }

        .footer-section h3 {
            font-size: 18px;
            margin-bottom: 15px;
            color: #ffd700;
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section ul li {
            margin-bottom: 10px;
        }

        .footer-section a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .footer-section a:hover {
            color: #ffd700;
            margin-left: 5px;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            padding-top: 20px;
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
        }

        .social-icons {
            display: flex;
            gap: 15px;
            margin-top: 15px;
        }

        .social-icons a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            color: white;
            transition: all 0.3s ease;
        }

        .social-icons a:hover {
            background: #ffd700;
            color: #667eea;
            transform: scale(1.1);
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar">
    <div class="nav-container">
        <a href="index.php" class="nav-logo">
            <i class="fas fa-shopping-bag"></i>
            WowLady
        </a>

        <div class="nav-center">
            <form class="search-bar" method="GET" action="">
                <input
                    type="text"
                    name="search"
                    placeholder="Search for products..."
                    value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                >
                <button type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>

        <div class="nav-right">
            <div class="nav-links">
                <a href="index.php"><i class="fas fa-home"></i> Home</a>
                <a href="cart.php" class="cart-icon"><i class="fas fa-shopping-cart"></i> Cart</a>
                <a href="wishlist.php" class="wishlist-link"><i class="fas fa-heart"></i> Wishlist</a>
                <?php if (isset($_SESSION["email"]) && $_SESSION["email"] === "admin@gmail.com"): ?>
                    <a href="../admin/manage-products.php"><i class="fas fa-cogs"></i> Manage Products</a>
                <?php endif; ?>
                      
                <?php if (isset($_SESSION["user_id"])): ?>
                    <div class="user-section">
                        <span class="user-welcome">Welcome, <?php echo htmlspecialchars($_SESSION["name"]); ?></span>
                        <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                    </div>
                <?php else: ?>
                    <a href="login.php"><i class="fas fa-user"></i> Login</a>
                    <a href="register.php"><i class="fas fa-user-plus"></i> Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<div class="container">
    <h1><i class="fas fa-box"></i> Our Products</h1>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'product_deleted'): ?>
        <div style="background:#d4edda; color:#155724; padding:15px; border:1px solid #c3e6cb; border-radius:5px; margin-bottom:20px;">
            <i class="fas fa-check-circle"></i> Product deleted successfully!
        </div>
    <?php endif; ?>
            
    <div class="products-grid">
        <?php

        $search = "";

        if (isset($_GET['search']) && $_GET['search'] !== "") {
            $search = trim($_GET['search']);

            $stmt = $conn->prepare(
                "SELECT * FROM products 
                 WHERE status = 'active' 
                 AND (name LIKE ? OR description LIKE ?)"
            );

            $like = "%" . $search . "%";
            $stmt->bind_param("ss", $like, $like);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $conn->query("SELECT * FROM products WHERE status='active'");
        }

        if ($result && $result->num_rows > 0):
            while ($row = $result->fetch_assoc()):
        ?>

            <div class="product">
                <div class="product-image">
                    <?php if (!empty($row["image"])): ?>
                        <img src="../admin/upload/<?php echo htmlspecialchars($row["image"]); ?>" 
                             alt="<?php echo htmlspecialchars($row["name"]); ?>"
                             loading="lazy">
                    <?php else: ?>
                        <div style="width: 100%; height: 100%; background-color: #f0f0f0; display: flex; align-items: center; justify-content: center; color: #999; font-weight: bold;">No Image</div>
                    <?php endif; ?>
                </div>

                <div class="product-content">
                    <h3>
                        <a href="product.php?id=<?php echo $row['id']; ?>">
                            <?php echo htmlspecialchars($row["name"]); ?>
                        </a>
                    </h3>

                    <p><?php echo htmlspecialchars(substr($row["description"], 0, 80)) . '...'; ?></p>

                    <p class="price">₹ <?php echo number_format($row["price"], 2); ?></p>

                    <?php if (isset($_SESSION["user_id"])): ?>
                        <button class="wishlist-btn" data-product-id="<?php echo $row['id']; ?>" title="Add to Wishlist" onclick="toggleWishlist(event, this)">
                            <i class="fas fa-heart"></i>
                        </button>

                        <div class="action-buttons">
                            <form method="POST" action="../api/add-to-cart.php">
                                <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn-add-cart"><i class="fas fa-cart-plus"></i> Add to Cart</button>
                            </form>

                            <form method="POST" action="../api/buy-product.php">
                                <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn-buy-now"><i class="fas fa-bolt"></i> Buy Now</button>
                            </form>
                        </div>

                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <form method="POST" action="../api/delete-product.php" onsubmit="return confirm('Are you sure you want to delete this product?');">
                            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="delete-button"><i class="fas fa-trash-alt"></i> Remove Product</button>
                        </form>
                        <?php endif; ?>
                    <?php else: ?>
                        <p><a href="login.php" style="color: #667eea; text-decoration: none; font-weight: 600;"><i class="fas fa-sign-in-alt"></i> Login to add to cart</a></p>
                    <?php endif; ?>
                </div>
            </div>

        <?php
            endwhile;
        else:
            echo "<p style='text-align: center; color: #999; margin-top: 50px;'><i class='fas fa-inbox'></i> No products available.</p>";
        endif;
        ?>
    </div>
</div>

<!-- Footer -->
<footer>
    <div class="footer-container">
        <!-- About Section -->
        <div class="footer-section">
            <h3><i class="fas fa-store"></i> About WowLady</h3>
            <p style="color: rgba(255, 255, 255, 0.8); line-height: 1.6;">
                WowLady is your one-stop destination for quality products at affordable prices. We're committed to providing excellent customer service and fast delivery worldwide.
            </p>
            <div class="social-icons">
                <a href="https://facebook.com" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="https://twitter.com" title="Twitter"><i class="fab fa-twitter"></i></a>
                <a href="https://instagram.com" title="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="https://linkedin.com" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="footer-section">
            <h3><i class="fas fa-link"></i> Quick Links</h3>
            <ul>
                <li><a href="index.php"><i class="fas fa-chevron-right"></i> Home</a></li>
                <li><a href="cart.php"><i class="fas fa-chevron-right"></i> Shopping Cart</a></li>
                <li><a href="wishlist.php"><i class="fas fa-chevron-right"></i> Wishlist</a></li>
                
            </ul>
        </div>

        <!-- Customer Service -->
        <div class="footer-section">
            <h3><i class="fas fa-headset"></i> Customer Service</h3>
            <ul>
                <li><a href="mailto:support@wowlady.com"><i class="fas fa-chevron-right"></i> Contact Us</a></li>
                <li><a href="orders.php"><i class="fas fa-chevron-right"></i> My Orders</a></li>
                <li><a href="profile.php"><i class="fas fa-chevron-right"></i> Profile</a></li>
            </ul>
        </div>
    </div>

    <!-- Footer Bottom -->
    <div class="footer-bottom">
        <p>&copy; 2026 WowLady. All rights reserved. | Designed with <i class="fas fa-heart" style="color: #ffd700;"></i> by Team</p>
        <p style="margin-top: 10px; font-size: 12px;">
            Payment Methods: 
            <i class="fab fa-cc-visa" style="margin: 0 5px;"></i>
            <i class="fab fa-cc-mastercard" style="margin: 0 5px;"></i>
            <i class="fab fa-cc-paypal" style="margin: 0 5px;"></i>
        </p>
    </div>
</footer>

<script>
    // Load wishlist status when page loads
    document.addEventListener('DOMContentLoaded', function() {
        loadWishlistStatus();
    });

    // Fetch all wishlist product IDs and update button colors
    function loadWishlistStatus() {
        fetch('../api/get-wishlist-products.php')
            .then(response => response.json())
            .then(wishlistIds => {
                document.querySelectorAll('.wishlist-btn').forEach(btn => {
                    const productId = parseInt(btn.dataset.productId);
                    if (wishlistIds.includes(productId)) {
                        btn.classList.add('in-wishlist');
                    }
                });
            })
            .catch(error => console.error('Error loading wishlist:', error));
    }

    // Toggle wishlist on button click
    function toggleWishlist(event, button) {
        event.preventDefault();
        event.stopPropagation();

        const productId = button.dataset.productId;

        // Create FormData for the POST request
        const formData = new FormData();
        formData.append('product_id', productId);

        fetch('../api/toggle-wishlist.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Toggle the button appearance
                if (data.added) {
                    button.classList.add('in-wishlist');
                } else {
                    button.classList.remove('in-wishlist');
                }
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }
</script>

</body>
</html>
