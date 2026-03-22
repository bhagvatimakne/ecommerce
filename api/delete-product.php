<?php
include "../config/db.php";
include "../config/session.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    die("Method not allowed");
}

// Check if user is logged in and is admin
if (!isset($_SESSION["user_id"])) {
    header("Location: ../public/login.php");
    exit;
}

// Check if user has admin role
if (!isset($_SESSION["email"]) || $_SESSION["email"] !== "admin@gmail.com") {
    http_response_code(403);
    die("Access denied. Only admin users can delete products.");
}

if (empty($_POST["product_id"])) {
    die("Invalid product id.");
}

$product_id = intval($_POST["product_id"]);
if ($product_id <= 0) {
    die("Invalid product id.");
}

// Start transaction
$conn->begin_transaction();

try {
    // Fetch product details
    $stmt = $conn->prepare("SELECT id, image FROM products WHERE id = ?");
    if (!$stmt) {
        throw new Exception("Fetch product prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $product_id);
    if (!$stmt->execute()) {
        throw new Exception("Fetch product execute failed: " . $stmt->error);
    }
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Product not found.");
    }
    
    $product = $result->fetch_assoc();

    // Delete product image file if present
    if (!empty($product['image'])) {
        $imagePath = __DIR__ . "/../admin/upload/" . basename($product['image']);
        if (file_exists($imagePath)) {
            if (!@unlink($imagePath)) {
                throw new Exception("Failed to delete product image file");
            }
        }
    }

    // Delete order items associated with this product
    $deleteItemsStmt = $conn->prepare("DELETE FROM order_items WHERE product_id = ?");
    if (!$deleteItemsStmt) {
        throw new Exception("Delete order items prepare failed: " . $conn->error);
    }
    $deleteItemsStmt->bind_param("i", $product_id);
    if (!$deleteItemsStmt->execute()) {
        throw new Exception("Delete order items execute failed: " . $deleteItemsStmt->error);
    }

    // Delete wishlist entries for this product
    $deleteWishlistStmt = $conn->prepare("DELETE FROM wishlist WHERE product_id = ?");
    if (!$deleteWishlistStmt) {
        throw new Exception("Delete wishlist prepare failed: " . $conn->error);
    }
    $deleteWishlistStmt->bind_param("i", $product_id);
    if (!$deleteWishlistStmt->execute()) {
        throw new Exception("Delete wishlist execute failed: " . $deleteWishlistStmt->error);
    }

    // Delete cart entries for this product
    $deleteCartStmt = $conn->prepare("DELETE FROM cart WHERE product_id = ?");
    if (!$deleteCartStmt) {
        throw new Exception("Delete cart prepare failed: " . $conn->error);
    }
    $deleteCartStmt->bind_param("i", $product_id);
    if (!$deleteCartStmt->execute()) {
        throw new Exception("Delete cart execute failed: " . $deleteCartStmt->error);
    }

    // Delete the product itself
    $delStmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    if (!$delStmt) {
        throw new Exception("Delete product prepare failed: " . $conn->error);
    }
    $delStmt->bind_param("i", $product_id);
    if (!$delStmt->execute()) {
        throw new Exception("Delete product execute failed: " . $delStmt->error);
    }

    // Commit transaction
    $conn->commit();
    
    header("Location: ../public/index.php?msg=product_deleted");
    exit;
    
} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    die("Delete failed: " . $e->getMessage());
}
?>
}
