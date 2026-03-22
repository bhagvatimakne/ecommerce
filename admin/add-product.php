<?php 
include "../config/session.php";
include "../config/db.php"; 

// Check if user is admin
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["email"]) || $_SESSION["email"] !== "admin@gmail.com") {
    header("Location: ../public/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Admin Panel</title>
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

        .nav-links {
            display: flex;
            gap: 20px;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s ease;
        }

        .nav-links a:hover {
            color: #ffd700;
        }

        .container {
            width: 90%;
            max-width: 600px;
            margin: 40px auto;
            flex: 1;
        }

        .form-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        .form-card h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
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

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }

        .form-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit {
            background: #28a745;
            color: white;
        }

        .btn-submit:hover {
            background: #218838;
            transform: translateY(-2px);
        }

        .btn-back {
            background: #6c757d;
            color: white;
        }

        .btn-back:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .container {
                width: 95%;
            }

            .form-card {
                padding: 20px;
            }

            .form-buttons {
                flex-direction: column;
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
        <div class="nav-links">
            <a href="manage-products.php"><i class="fas fa-arrow-left"></i> Back to Products</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="form-card">
        <h1><i class="fas fa-plus-circle"></i> Add New Product</h1>

        <?php if (isset($_GET['success'])): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i> Product added successfully!
            </div>
        <?php endif; ?>

        <form action="save-product.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label><i class="fas fa-heading"></i> Product Name</label>
                <input type="text" name="name" required>
            </div>

            <div class="form-group">
                <label><i class="fas fa-align-left"></i> Description</label>
                <textarea name="description" required></textarea>
            </div>

            <div class="form-group">
                <label><i class="fas fa-tag"></i> Category</label>
                <select name="category_id" required>
                    <option value="">Select Category</option>
                    <?php
                    $catQuery = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
                    while ($cat = $catQuery->fetch_assoc()) {
                        echo "<option value='{$cat['id']}'>{$cat['name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label><i class="fas fa-dollar-sign"></i> Price</label>
                <input type="number" step="0.01" name="price" min="0" required>
            </div>

            <div class="form-group">
                <label><i class="fas fa-boxes"></i> Stock Quantity</label>
                <input type="number" name="stock" min="0" required>
            </div>

            <div class="form-group">
                <label><i class="fas fa-image"></i> Product Image</label>
                <input type="file" name="image" accept="image/*" required>
            </div>

            <div class="form-buttons">
                <button type="submit" class="btn btn-submit">
                    <i class="fas fa-save"></i> Add Product
                </button>
                <a href="manage-products.php" class="btn btn-back" style="text-decoration: none;">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
