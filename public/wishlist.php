<?php
include "../config/session.php";
include "../config/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

$query = "
SELECT products.id, products.name, products.image, products.description, products.price FROM wishlist
JOIN products ON wishlist.product_id = products.id
WHERE wishlist.user_id = ?
ORDER BY products.name ASC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist - E-Commerce Store</title>
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
            margin: 40px auto;
            max-width: 1200px;
            flex: 1;
        }
        
        .wishlist-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .wishlist-header h1 {
            color: #333;
            font-size: 32px;
        }
        
        .wishlist-header a {
            background: #667eea;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .wishlist-header a:hover {
            background: #5568d3;
            transform: scale(1.02);
        }
        
        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .success-message button {
            background: none;
            border: none;
            color: #155724;
            cursor: pointer;
            font-size: 18px;
        }
        
        .wishlist-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 25px;
        }
        
        .wishlist-item {
            background: white;
            padding: 0;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .wishlist-item:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            transform: translateY(-5px);
        }
        
        .wishlist-item-image {
            width: 100%;
            height: 200px;
            overflow: hidden;
        }
        
        .wishlist-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        
        .wishlist-item-content {
            padding: 15px;
        }
        
        .wishlist-item-content h3 {
            color: #333;
            font-size: 16px;
            margin-bottom: 8px;
        }
        
        .wishlist-item-content p {
            color: #666;
            font-size: 13px;
            margin-bottom: 10px;
            line-height: 1.4;
        }
        
        .wishlist-item-price {
            color: #667eea;
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 12px;
        }
        
        .wishlist-item-actions {
            display: flex;
            gap: 8px;
            flex-direction: column;
        }
        
        .wishlist-item-actions button {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .btn-add-cart {
            background: linear-gradient(135deg, #FB9B8F 0%, #FFF7CD 100%);
            color: white;
        }
        
        .btn-add-cart:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        .btn-remove {
            background-color: #dc3545;
            color: white;
        }
        
        .btn-remove:hover {
            background-color: #c82333;
        }
        
        .empty-wishlist {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        
        .empty-wishlist i {
            font-size: 64px;
            margin-bottom: 20px;
            color: #ddd;
        }
        
        .empty-wishlist p {
            font-size: 18px;
            margin-bottom: 20px;
        }
        
        .empty-wishlist a {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .empty-wishlist a:hover {
            background: #5568d3;
            transform: scale(1.02);
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
                <a href="orders.php"><i class="fas fa-box"></i> Orders</a>
                <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
            </div>
        </div>
    </div>
</nav>

<div class="container">
    <div class="wishlist-header">
        <h1><i class="fas fa-heart"></i> My Wishlist</h1>
        <a href="index.php"><i class="fas fa-arrow-left"></i> Back to Shopping</a>
    </div>
    
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'removed'): ?>
    <div class="success-message">
        <span><i class="fas fa-check-circle"></i> Product removed from wishlist successfully!</span>
        <button onclick="this.parentElement.style.display='none';">×</button>
    </div>
    <?php endif; ?>
    
    <?php if ($result->num_rows > 0): ?>
        <div class="wishlist-grid">
            <?php while ($row = $result->fetch_assoc()): ?>
            <div class="wishlist-item">
                <div class="wishlist-item-image">
                    <?php if (!empty($row["image"])): ?>
                        <img src="../admin/upload/<?php echo htmlspecialchars($row['image']); ?>" 
                             alt="<?php echo htmlspecialchars($row['name']); ?>"
                             loading="lazy">
                    <?php else: ?>
                        <div style="width: 100%; height: 100%; background-color: #f0f0f0; display: flex; align-items: center; justify-content: center; color: #999;">No Image</div>
                    <?php endif; ?>
                </div>
                
                <div class="wishlist-item-content">
                    <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                    <p><?php echo htmlspecialchars(substr($row['description'], 0, 60)) . '...'; ?></p>
                    <div class="wishlist-item-price">₹ <?php echo number_format($row['price'], 2); ?></div>
                    
                    <div class="wishlist-item-actions">
                        <form method="POST" action="../api/add-to-cart.php" style="margin: 0;">
                            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="btn-add-cart"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
                        </form>
                        
                        <form method="POST" action="../api/remove-from-wishlist.php" style="margin: 0;" onsubmit="return confirm('Remove from wishlist?');">
                            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="btn-remove"><i class="fas fa-trash-alt"></i> Remove</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="empty-wishlist">
            <i class="fas fa-heart-broken"></i>
            <p>Your wishlist is empty</p>
            <a href="index.php"><i class="fas fa-shopping-bag"></i> Continue Shopping</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>