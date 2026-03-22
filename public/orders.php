<?php
include "../config/session.php";
include "../config/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$ordersQuery = "SELECT o.id AS order_id, o.total_price, o.status, o.created_at, oi.id AS order_item_id, oi.product_id, oi.quantity, oi.price AS unit_price, oi.returned, p.name, p.image
FROM orders o
JOIN order_items oi ON oi.order_id = o.id
JOIN products p ON oi.product_id = p.id
WHERE o.user_id = ?
ORDER BY o.created_at DESC";

$stmt = $conn->prepare($ordersQuery);
if (!$stmt) {
    die("SQL prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if (!$result) {
    die("SQL execute failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - E-Commerce Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: #f8f9fa;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
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
            width: 90%; 
            max-width: 1200px; 
            margin: 40px auto; 
            flex: 1;
        }

        .orders-header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 25px; 
        }

        .orders-header h1 { 
            font-size: 32px; 
            color: #333; 
        }

        .order-card { 
            background: white; 
            border-radius: 10px; 
            box-shadow: 0 2px 8px rgba(0,0,0,0.08); 
            padding: 20px; 
            margin-bottom: 15px; 
            display: grid; 
            grid-template-columns: 120px 1fr auto; 
            gap: 15px; 
            align-items: center; 
        }

        .order-card img { 
            width: 100%; 
            height: 100px; 
            object-fit: cover; 
            border-radius: 8px; 
        }

        .order-info h3 { 
            margin:0; 
            font-size: 18px; 
        }

        .order-info p { 
            margin: 3px 0; 
            color: #555; 
        }

        .order-status { 
            text-transform: capitalize; 
            font-weight: bold; 
        }

        .order-actions button { 
            padding: 10px 15px; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-return { 
            background: #dc3545; 
            color: white; 
        }

        .btn-return:hover { 
            background: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }

        .returned-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 15px;
            background: #d4edda;
            color: #155724;
            border-radius: 5px;
            font-weight: 600;
        }

        .order-actions form {
            margin: 0;
        }

        .empty {
            text-align:center; 
            padding: 50px 0; 
            color: #999; 
            background: #fff; 
            border-radius: 10px; 
        }

        .back { 
            text-decoration:none; 
            color: #667eea;
            background: #667eea;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .back:hover {
            background: #5568d3;
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
                <a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a>
                <a href="wishlist.php"><i class="fas fa-heart"></i> Wishlist</a>
                <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
            </div>
        </div>
    </div>
</nav>

<div class="container">
    <div class="orders-header">
        <h1><i class="fas fa-box-open"></i> My Orders</h1>
        <a class="back" href="index.php"><i class="fas fa-arrow-left"></i> Continue Shopping</a>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'order_placed'): ?>
        <div style="background:#d4edda; color:#155724; padding:15px; border:1px solid #c3e6cb; border-radius:5px; margin-bottom:20px;">
            <i class="fas fa-check-circle"></i> Order placed successfully!
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['return']) && $_GET['return'] === 'success'): ?>
        <div style="background:#d4edda; color:#155724; padding:15px; border:1px solid #c3e6cb; border-radius:5px; margin-bottom:20px;">
            <i class="fas fa-check-circle"></i> Product returned successfully! Stock has been restored.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['return']) && $_GET['return'] === 'error'): ?>
        <div style="background:#f8d7da; color:#721c24; padding:15px; border:1px solid #f5c6cb; border-radius:5px; margin-bottom:20px;">
            <i class="fas fa-exclamation-circle"></i> Return failed: <?php echo isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : 'An error occurred'; ?>
        </div>
    <?php endif; ?>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="order-card">
                <div><img src="../admin/upload/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>"></div>
                <div class="order-info">
                    <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                    <p>Qty: <?php echo $row['quantity']; ?></p>
                    <p>Total: ₹<?php echo number_format($row['total_price'], 2); ?></p>
                    <p>Ordered: <?php echo htmlspecialchars($row['created_at']); ?></p>
                    <p class="order-status">Status: <?php echo htmlspecialchars($row['status']); ?></p>
                </div>
                <div class="order-actions">
                    <?php if ($row['returned'] == 0): ?>
                        <form method="POST" action="../api/return-order.php" onsubmit="return confirm('Are you sure you want to return this product? Stock will be restored to your account.');">
                            <input type="hidden" name="order_item_id" value="<?php echo $row['order_item_id']; ?>">
                            <button type="submit" class="btn-return"><i class="fas fa-undo"></i> Return Product</button>
                        </form>
                    <?php else: ?>
                        <span class="returned-status"><i class="fas fa-check-circle"></i> Returned</span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="empty">
            <i class="fas fa-box-open" style="font-size: 40px; margin-bottom: 12px; display:block;"></i>
            <p>No orders yet. Go add your first order.</p>
        </div>
    <?php endif; ?>
</div>
</body>
</html>