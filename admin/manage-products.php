<?php
include "../config/session.php";
include "../config/db.php";

// Check if user is admin
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["email"]) || $_SESSION["email"] !== "admin@gmail.com") {
    header("Location: ../public/login.php");
    exit;
}

$admin_id = $_SESSION["user_id"];

// Fetch all products
$query = "SELECT id, name, price, stock, status, image, created_at FROM products ORDER BY created_at DESC";
$result = $conn->query($query);

if (!$result) {
    die("Error fetching products: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Admin Panel</title>
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
            width: 95%;
            max-width: 1400px;
            margin: 30px auto;
            flex: 1;
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .admin-header h1 {
            font-size: 28px;
            color: #333;
        }

        .btn-add {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-add:hover {
            background: #218838;
            transform: translateY(-2px);
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .products-table {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f8f9fa;
            border-bottom: 2px solid #ddd;
        }

        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #333;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            color: #555;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .product-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }

        .price {
            font-weight: 600;
            color: #667eea;
        }

        .stock {
            font-weight: 600;
        }

        .stock.low {
            color: #dc3545;
        }

        .stock.high {
            color: #28a745;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        .btn-edit, .btn-delete {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-edit {
            background: #667eea;
            color: white;
        }

        .btn-edit:hover {
            background: #5568d3;
            transform: translateY(-2px);
        }

        .btn-delete {
            background: #dc3545;
            color: white;
        }

        .btn-delete:hover {
            background: #c82333;
            transform: translateY(-2px);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .empty-state i {
            font-size: 50px;
            margin-bottom: 20px;
            display: block;
        }

        @media (max-width: 768px) {
            .admin-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            table {
                font-size: 13px;
            }

            th, td {
                padding: 10px;
            }

            .actions {
                flex-direction: column;
            }

            .product-img {
                width: 40px;
                height: 40px;
            }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="nav-container">
        <a href="../public/index.php" class="nav-logo">
            <i class="fas fa-shopping-bag"></i>
            WowLady Admin
        </a>

        <div class="nav-right">
            <div class="nav-links">
                <a href="../public/index.php"><i class="fas fa-home"></i> Home</a>
                <a href="manage-products.php"><i class="fas fa-boxes"></i> Manage Products</a>
                <a href="../public/profile.php"><i class="fas fa-user"></i> Profile</a>
            </div>
        </div>
    </div>
</nav>

<div class="container">
    <!-- Admin Header -->
    <div class="admin-header">
        <h1><i class="fas fa-cogs"></i> Manage Products</h1>
        <a href="add-product.php" class="btn-add">
            <i class="fas fa-plus"></i> Add New Product
        </a>
    </div>

    <!-- Success Message -->
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'product_deleted'): ?>
        <div class="success-message">
            <i class="fas fa-check-circle"></i>
            Product deleted successfully!
        </div>
    <?php endif; ?>

    <!-- Products Table -->
    <?php if ($result->num_rows > 0): ?>
        <div class="products-table">
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Added Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($product = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?php if (!empty($product['image'])): ?>
                                    <img src="upload/<?php echo htmlspecialchars($product['image']); ?>" alt="" class="product-img">
                                <?php else: ?>
                                    <div style="width: 50px; height: 50px; background: #f0f0f0; border-radius: 5px; display: flex; align-items: center; justify-content: center; color: #999;">
                                        <i class="fas fa-image"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><strong><?php echo htmlspecialchars($product['name']); ?></strong></td>
                            <td class="price">₹<?php echo number_format($product['price'], 2); ?></td>
                            <td class="stock <?php echo $product['stock'] < 5 ? 'low' : 'high'; ?>">
                                <?php echo $product['stock']; ?> items
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $product['status'] === 'active' ? 'active' : 'inactive'; ?>">
                                    <?php echo ucfirst($product['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($product['created_at'])); ?></td>
                            <td>
                                <div class="actions">
                                    <a href="edit-product.php?id=<?php echo $product['id']; ?>" class="btn-edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form method="POST" action="../api/delete-product.php" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this product? This action cannot be undone.');">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <button type="submit" class="btn-delete">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="products-table">
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h2>No Products Available</h2>
                <p>You haven't added any products yet.</p>
                <a href="add-product.php" class="btn-add" style="margin-top: 20px; display: inline-flex;">
                    <i class="fas fa-plus"></i> Add First Product
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
