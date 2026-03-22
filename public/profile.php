<?php
include "../config/session.php";
include "../config/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

// Fetch user information
$stmt = $conn->prepare("SELECT id, name, email, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Fetch user orders
$ordersStmt = $conn->prepare("SELECT COUNT(*) as total_orders FROM orders WHERE user_id = ?");
$ordersStmt->bind_param("i", $user_id);
$ordersStmt->execute();
$ordersResult = $ordersStmt->get_result();
$orderStats = $ordersResult->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - E-Commerce Store</title>
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

        /* Main Content */
        .container {
            width: 90%;
            max-width: 800px;
            margin: 40px auto;
            flex: 1;
        }

        .profile-header {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 40px;
            margin-bottom: 30px;
            text-align: center;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #FB9B8F 0%, #FFF7CD 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 50px;
            margin: 0 auto 20px;
            color: white;
        }

        .profile-header h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }

        .profile-header p {
            color: #666;
            margin: 5px 0;
        }

        .profile-info {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 30px;
        }

        .info-group {
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-group:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .info-label {
            font-size: 12px;
            color: #999;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 16px;
            color: #333;
            font-weight: 500;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
        }

        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
        }

        .stat-label {
            color: #666;
            font-size: 14px;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #FB9B8F 0%, #FFF7CD 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        /* Footer Styling */
        footer {
            background: linear-gradient(135deg, #F57799 0%, #FB9B8F 100%);
            color: white;
            padding: 30px 0 20px 0;
            margin-top: 50px;
        }

        .footer-bottom {
            text-align: center;
            padding: 20px;
            color: rgba(255, 255, 255, 0.7);
        }

        @media (max-width: 768px) {
            .container {
                width: 95%;
                margin: 20px auto;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }

            .stats-container {
                grid-template-columns: 1fr;
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

        <div class="nav-right">
            <div class="nav-links">
                <a href="index.php"><i class="fas fa-home"></i> Home</a>
                <a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a>
                <a href="wishlist.php"><i class="fas fa-heart"></i> Wishlist</a>
                <a href="orders.php"><i class="fas fa-box"></i> Orders</a>
            </div>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container">
    <!-- Profile Header -->
    <div class="profile-header">
        <div class="profile-avatar">
            <i class="fas fa-user"></i>
        </div>
        <h1><?php echo htmlspecialchars($user['name']); ?></h1>
        <p><?php echo htmlspecialchars($user['email']); ?></p>
    </div>

    <!-- Stats Cards -->
    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-number"><?php echo $orderStats['total_orders']; ?></div>
            <div class="stat-label">Total Orders</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" id="wishlistCount">0</div>
            <div class="stat-label">Wishlist Items</div>
        </div>
    </div>

    <!-- User Information -->
    <div class="profile-info">
        <h2 style="margin-bottom: 25px; color: #333;"><i class="fas fa-user-circle"></i> Account Information</h2>
        
        <div class="info-group">
            <div class="info-label"><i class="fas fa-user"></i> Full Name</div>
            <div class="info-value"><?php echo htmlspecialchars($user['name']); ?></div>
        </div>

        <div class="info-group">
            <div class="info-label"><i class="fas fa-envelope"></i> Email Address</div>
            <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>
        </div>

        <div class="info-group">
            <div class="info-label"><i class="fas fa-calendar"></i> Member Since</div>
            <div class="info-value"><?php echo date('F d, Y', strtotime($user['created_at'])); ?></div>
        </div>

        <div class="info-group">
            <div class="info-label"><i class="fas fa-shopping-bag"></i> Account Status</div>
            <div class="info-value" style="color: #28a745;">
                <i class="fas fa-check-circle"></i> Active
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="action-buttons">
        <a href="index.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Home
        </a>
        <a href="../api/logout.php" class="btn btn-danger" onclick="return confirm('Are you sure you want to logout?');">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</div>

<!-- Footer -->
<footer>
    <div class="footer-bottom">
        <p>&copy; 2026 WowLady. All rights reserved.</p>
    </div>
</footer>

<script>
    // Count wishlist items
    fetch('../api/get-wishlist-count.php')
        .then(response => response.json())
        .then(data => {
            document.getElementById('wishlistCount').textContent = data.count;
        })
        .catch(error => console.log('Could not load wishlist count'));
</script>

</body>
</html>
