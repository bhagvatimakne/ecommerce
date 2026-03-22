<?php
include "../config/session.php";
include "../config/db.php";

// Get product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id <= 0) {
    die("Invalid product ID.");
}

// Fetch product details
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ? AND status = 'active'");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Product not found.");
}

$product = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product["name"]); ?> - E-Commerce Store</title>
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
        }

        /* Navigation Bar */
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
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .nav-links a:hover {
            color: #ffd700;
        }

        .product-container {
            width: 90%;
            margin: 40px auto;
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .breadcrumb {
            margin-bottom: 20px;
            color: #666;
            font-size: 14px;
        }

        .breadcrumb a {
            color: #667eea;
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .product-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        .product-image {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-image img {
            width: 100%;
            max-width: 400px;
            height: auto;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .product-info h1 {
            font-size: 32px;
            color: #333;
            margin-bottom: 20px;
        }

        .product-info .price {
            font-size: 28px;
            color: #667eea;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .product-info .description {
            color: #666;
            line-height: 1.8;
            margin-bottom: 20px;
            font-size: 15px;
        }

        .product-info .stock {
            color: #28a745;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .product-info .stock.low {
            color: #dc3545;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-group input {
            width: 100px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .btn-add-cart {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #FB9B8F 0%, #FFF7CD 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 10px;
        }

        .btn-add-cart:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .btn-buy-now {
            width: 100%;
            padding: 15px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-buy-now:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
        }

        .btn-back {
            display: inline-block;
            padding: 10px 20px;
            background: #f0f0f0;
            color: #333;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background: #e0e0e0;
        }

        @media (max-width: 768px) {
            .product-details {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .product-info h1 {
                font-size: 24px;
            }

            .product-info .price {
                font-size: 22px;
            }
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

        <div class="nav-links">
            <a href="index.php"><i class="fas fa-home"></i> Home</a>
            <a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a>
            <a href="wishlist.php"><i class="fas fa-heart"></i> Wishlist</a>
            <?php if (isset($_SESSION["email"]) && $_SESSION["email"] === "admin@gmail.com"): ?>
                <a href="../admin/add-product.php"><i class="fas fa-plus"></i> Add Product</a>
            <?php endif; ?>

            <?php if (isset($_SESSION["user_id"])): ?>
                <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
            <?php else: ?>
                <a href="login.php"><i class="fas fa-user"></i> Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="product-container">
    <a href="index.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Products</a>

    <div class="product-details">
        <!-- Product Image -->
        <div class="product-image">
            <?php if (!empty($product["image"])): ?>
                <img src="../admin/upload/<?php echo htmlspecialchars($product["image"]); ?>" 
                     alt="<?php echo htmlspecialchars($product["name"]); ?>">
            <?php else: ?>
                <div style="width: 400px; height: 400px; background-color: #f0f0f0; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #999; font-weight: bold;">No Image Available</div>
            <?php endif; ?>
        </div>

        <!-- Product Info -->
        <div class="product-info">
            <h1><?php echo htmlspecialchars($product["name"]); ?></h1>

            <div class="price">
                ₹<?php echo number_format($product["price"], 2); ?>
            </div>

            <div class="description">
                <?php echo nl2br(htmlspecialchars($product["description"])); ?>
            </div>

            <div class="stock <?php echo $product["stock"] < 5 ? 'low' : ''; ?>">
                <i class="fas fa-box"></i>
                <?php 
                if ($product["stock"] > 0) {
                    echo "Stock: " . $product["stock"] . " available";
                } else {
                    echo "Out of Stock";
                }
                ?>
            </div>

            <?php if (isset($_SESSION["user_id"]) && $product["stock"] > 0): ?>
                <form method="POST" action="../api/add-to-cart.php" style="margin-bottom: 10px;">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

                    <div class="form-group">
                        <label><i class="fas fa-box"></i> Quantity:</label>
                        <input 
                            type="number" 
                            name="quantity" 
                            value="1" 
                            min="1" 
                            max="<?php echo $product['stock']; ?>" 
                            required
                        >
                    </div>

                    <button type="submit" class="btn-add-cart">
                        <i class="fas fa-cart-plus"></i> Add to Cart
                    </button>
                </form>

                <form method="POST" action="../api/buy-product.php">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <input type="hidden" name="quantity" value="1" id="buy-quantity">
                    <button type="submit" class="btn-buy-now" style="background: #28a745; color: white; margin-top: 5px;">
                        <i class="fas fa-bolt"></i> Buy Now
                    </button>
                </form>

                <script>
                    const qtyInput = document.querySelector('.form-group input[name="quantity"]');
                    const buyQtyInput = document.getElementById('buy-quantity');

                    if (qtyInput && buyQtyInput) {
                        qtyInput.addEventListener('change', function() {
                            buyQtyInput.value = this.value;
                        });
                    }
                </script>
            <?php elseif (!isset($_SESSION["user_id"])): ?>
                <p style="color: #667eea; font-weight: 600; margin-top: 20px;">
                    <a href="login.php" style="color: #667eea;"><i class="fas fa-sign-in-alt"></i> Login</a> to purchase this product
                </p>
            <?php else: ?>
                <p style="color: #dc3545; font-weight: 600; margin-top: 20px;">
                    <i class="fas fa-exclamation-circle"></i> This product is currently out of stock
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
