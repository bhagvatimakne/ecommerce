<?php
include "../config/session.php";
include "../config/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = intval($_SESSION["user_id"]);

// Ensure address fields exist on users table (migrations)
$requiredColumns = ['address','city','state','zip','country'];
foreach ($requiredColumns as $col) {
    $checkCol = $conn->query("SHOW COLUMNS FROM users LIKE '$col'");
    if ($checkCol && $checkCol->num_rows === 0) {
        $conn->query("ALTER TABLE users ADD COLUMN $col VARCHAR(150) NULL");
    }
}

// Load saved address from users table
$userStmt = $conn->prepare("SELECT name, email, address, city, state, zip, country FROM users WHERE id = ?");
if (!$userStmt) {
    die("SQL prepare failed: " . $conn->error);
}
$userStmt->bind_param("i", $user_id);
$userStmt->execute();
$userData = $userStmt->get_result()->fetch_assoc();

$mode = isset($_GET['from']) && $_GET['from'] === 'cart' ? 'cart' : 'product';
$product_id = intval($_GET['product_id'] ?? 0);

$selectedProduct = null;
$cartItems = [];
$grandTotal = 0.0;

if ($mode === 'product') {
    if ($product_id <= 0) {
        die("Invalid product for checkout.");
    }

    $pStmt = $conn->prepare("SELECT id, name, price, stock FROM products WHERE id = ? AND status='active'");
    $pStmt->bind_param("i", $product_id);
    $pStmt->execute();
    $selectedProduct = $pStmt->get_result()->fetch_assoc();

    if (!$selectedProduct) {
        die("Product not found or not active.");
    }

    $selectedProduct['quantity'] = 1;
    $selectedProduct['subtotal'] = $selectedProduct['price'];
    $grandTotal = $selectedProduct['subtotal'];
} else {
    $cStmt = $conn->prepare("SELECT c.id AS cart_id, c.product_id, c.quantity, p.name, p.price, p.stock FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ? AND p.status='active'");
    $cStmt->bind_param("i", $user_id);
    $cStmt->execute();
    $cartItems = $cStmt->get_result()->fetch_all(MYSQLI_ASSOC);

    if (!$cartItems || count($cartItems) === 0) {
        die("Your cart is empty or has no available items.");
    }

    foreach ($cartItems as $item) {
        $subtotal = $item['price'] * $item['quantity'];
        $grandTotal += $subtotal;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $state = trim($_POST['state'] ?? '');
    $zip = trim($_POST['zip'] ?? '');
    $country = trim($_POST['country'] ?? '');

    if (!$address || !$city || !$state || !$zip || !$country) {
        $error = 'Please fill all address fields.';
    } else {
        // update saved address
        $uStmt = $conn->prepare("UPDATE users SET address=?, city=?, state=?, zip=?, country=? WHERE id=?");
        $uStmt->bind_param("sssssi", $address, $city, $state, $zip, $country, $user_id);
        $uStmt->execute();

        $conn->begin_transaction();

        try {
            // create order
            $orderInsert = $conn->prepare("INSERT INTO orders (user_id, total_price, status, created_at) VALUES (?, ?, 'confirmed', NOW())");
            $orderInsert->bind_param("id", $user_id, $grandTotal);
            $orderInsert->execute();
            $order_id = $conn->insert_id;

            if ($mode === 'product') {
                $items = [
                    [
                        'product_id' => $selectedProduct['id'],
                        'price' => $selectedProduct['price'],
                        'quantity' => 1,
                        'cart_id' => null,
                    ]
                ];
            } else {
                $items = [];
                foreach ($cartItems as $ci) {
                    $items[] = [
                        'product_id' => $ci['product_id'],
                        'price' => $ci['price'],
                        'quantity' => $ci['quantity'],
                        'cart_id' => $ci['cart_id']
                    ];
                }
            }

            $itemStmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, price, quantity) VALUES (?, ?, ?, ?)");
            $stockStmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");
            $deleteCartStmt = $conn->prepare("DELETE FROM cart WHERE id = ?");

            foreach ($items as $item) {
                if ($item['quantity'] <= 0) {
                    throw new Exception("Invalid quantity");
                }
                $itemStmt->bind_param("iidi", $order_id, $item['product_id'], $item['price'], $item['quantity']);
                if (!$itemStmt->execute()) {
                    throw new Exception("Failed insert order item: " . $itemStmt->error);
                }

                $stockStmt->bind_param("iii", $item['quantity'], $item['product_id'], $item['quantity']);
                $stockStmt->execute();
                if ($conn->affected_rows === 0) {
                    throw new Exception("Insufficient stock for product " . $item['product_id']);
                }

                if ($mode === 'cart') {
                    $deleteCartStmt->bind_param("i", $item['cart_id']);
                    $deleteCartStmt->execute();
                }
            }

            $conn->commit();
            header("Location: orders.php?msg=order_placed");
            exit;
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Checkout failed: " . $e->getMessage();
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
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
            max-width: 800px; 
            margin: 40px auto; 
            background:white; 
            padding:30px; 
            border-radius:10px; 
            box-shadow:0 2px 10px rgba(0,0,0,0.1);
            flex: 1;
        }

        .heading { 
            margin-bottom:20px;
            font-size: 28px;
            color: #333;
        }

        .form-group { 
            margin-bottom:12px; 
        }

        .form-group label { 
            font-weight:600; 
            display:block; 
            margin-bottom:5px;
            color: #333;
        }

        .form-group input { 
            width:100%; 
            padding:10px; 
            border:1px solid #ccc; 
            border-radius:5px;
            font-size: 14px;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
        }

        .btn { 
            background: #28a745; 
            color:white; 
            border:none; 
            padding:12px 18px; 
            border-radius:5px; 
            font-weight:600; 
            cursor:pointer;
            transition: all 0.3s ease;
        }

        .btn:hover { 
            background:#218838;
            transform: translateY(-2px);
        }

        .order-summary { 
            margin-top:20px; 
            border-top:1px solid #eee; 
            padding-top:15px;
        }

        .error { 
            color:#dc3545; 
            margin-bottom:10px;
            background: #f8d7da;
            padding: 12px;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
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
                <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
            </div>
        </div>
    </div>
</nav>

<div class="container">
    <h2 class="heading">Checkout</h2>

    <?php if (!empty($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Address</label>
            <input type="text" name="address" value="<?php echo htmlspecialchars($userData['address'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label>City</label>
            <input type="text" name="city" value="<?php echo htmlspecialchars($userData['city'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label>State</label>
            <input type="text" name="state" value="<?php echo htmlspecialchars($userData['state'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label>Zip</label>
            <input type="text" name="zip" value="<?php echo htmlspecialchars($userData['zip'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label>Country</label>
            <input type="text" name="country" value="<?php echo htmlspecialchars($userData['country'] ?? ''); ?>" required>
        </div>

        <div class="order-summary">
            <h3>Order Summary</h3>
            <?php if ($mode === 'product'): ?>
                <p><strong><?php echo htmlspecialchars($selectedProduct['name']); ?></strong> × 1</p>
                <p>Price: ₹<?php echo number_format($selectedProduct['price'], 2); ?></p>
            <?php else: ?>
                <?php foreach ($cartItems as $item): ?>
                    <p><?php echo htmlspecialchars($item['name']); ?> × <?php echo intval($item['quantity']); ?> = ₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                <?php endforeach; ?>
            <?php endif; ?>
            <p><strong>Total: ₹<?php echo number_format($grandTotal, 2); ?></strong></p>
        </div>

        <button type="submit" class="btn">Place Order</button>
    </form>
</div>
</body>
</html>
