<?php
include "../config/session.php";
include "../config/db.php";

if (!isset($_SESSION["user_id"])) {
    die("Please login first");
}

$user_id = $_SESSION["user_id"];

// Fetch cart items
$query = "
SELECT 
    cart.id AS cart_id,
    products.name,
    products.price,
    products.stock,
    cart.quantity
FROM cart
JOIN products ON cart.product_id = products.id
WHERE cart.user_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total = 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Cart</title>
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

        /* Navbar */
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
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .nav-links a:hover {
            color: #ffd700;
        }

        .container {
            width: 80%;
            margin: auto;
            margin-top: 30px;
            margin-bottom: 30px;
        }

        .cart-item {
            border: 1px solid #ccc;
            padding: 15px;
            margin: 15px 0;
            background: white;
            border-radius: 5px;
        }

        .qty-box {
            margin: 10px 0;
        }

        button {
            padding: 6px 10px;
            cursor: pointer;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 3px;
            transition: all 0.3s ease;
        }

        button:hover {
            background: #5568d3;
        }

        .total-box {
            font-size: 20px;
            font-weight: bold;
            margin-top: 20px;
            padding: 20px;
            background: white;
            border-radius: 5px;
        }

        .empty {
            color: red;
            font-weight: bold;
        }

        .top-links {
            margin-bottom: 20px;
        }

        .top-links a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .top-links a:hover {
            text-decoration: underline;
        }

        .checkout-btn {
            display:inline-block;
            margin-top:15px;
            background:#28a745;
            color:#fff;
            padding: 10px 18px;
            border-radius: 5px;
            text-decoration:none;
            transition: all 0.3s ease;
        }

        .checkout-btn:hover {
            background: #218838;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="nav-container">
        <a href="index.php" class="nav-logo">
            <i class="fas fa-shopping-bag"></i>
            WowLady
        </a>

        <div class="nav-right">
            <div class="nav-links">
                <a href="index.php"><i class="fas fa-home"></i> Home</a>
                <a href="wishlist.php"><i class="fas fa-heart"></i> Wishlist</a>
                <a href="orders.php"><i class="fas fa-box"></i> Orders</a>
                <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
            </div>
        </div>
    </div>
</nav>

<div class="container">
    <div class="top-links">
        <a href="index.php">← Continue Shopping</a>
    </div>

    <h1>Your Cart</h1>

<?php if ($result->num_rows > 0): ?>

    <?php while ($row = $result->fetch_assoc()): 
        $subtotal = $row["price"] * $row["quantity"];
        $total += $subtotal;
    ?>

        <div class="cart-item">

            <h3><?php echo htmlspecialchars($row["name"]); ?></h3>

            <p>Price: ₹<?php echo number_format($row["price"], 2); ?></p>

            <!-- Quantity Controls -->
            <div class="qty-box">

                <!-- Decrease -->
                <form method="POST" action="../api/update-cart.php" style="display:inline;">
                    <input type="hidden" name="cart_id" value="<?php echo $row['cart_id']; ?>">
                    <input type="hidden" name="action" value="decrease">
                    <button type="submit">−</button>
                </form>

                <strong style="margin: 0 12px;">
                    <?php echo $row["quantity"]; ?>
                </strong>

                <!-- Increase -->
                <form method="POST" action="../api/update-cart.php" style="display:inline;">
                    <input type="hidden" name="cart_id" value="<?php echo $row['cart_id']; ?>">
                    <input type="hidden" name="action" value="increase">
                    <button type="submit">+</button>
                </form>

            </div>

            <p>Stock Available: <?php echo $row["stock"]; ?></p>

            <p><strong>Subtotal: $<?php echo number_format($subtotal, 2); ?></strong></p>

        </div>

    <?php endwhile; ?>

    <div class="total-box">
        Grand Total: $<?php echo number_format($total, 2); ?>
    </div>

    <a href="checkout.php?from=cart" class="checkout-btn"><i class="fas fa-credit-card"></i> Checkout Now</a>

<?php else: ?>

    <p class="empty">Your cart is empty.</p>

<?php endif; ?>

</div>

</body>
</html>
