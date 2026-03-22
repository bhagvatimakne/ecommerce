<?php
include "../config/db.php";
include "../config/session.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo "Method not allowed";
    exit;
}

if (!isset($_SESSION["user_id"])) {
    header("Location: ../public/login.php");
    exit;
}

$user_id = intval($_SESSION["user_id"]);
$product_id = intval($_POST["product_id"]);
$quantity = intval($_POST["quantity"]);

if ($product_id <= 0 || $quantity <= 0) {
    die("Invalid product or quantity.");
}

// Get product and check stock/status
$stmt = $conn->prepare("SELECT id, price, stock, status FROM products WHERE id = ? AND status = 'active'");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$product = $result->fetch_assoc()) {
    die("Product not found or inactive.");
}

if ($product["stock"] < $quantity) {
    die("Ordered quantity exceeds stock.");
}

$total_price = $product["price"] * $quantity;

// Transactional operations
$conn->begin_transaction();

try {
    // Insert order without product_id and quantity
    $orderStmt = $conn->prepare("INSERT INTO orders (user_id, total_price, status, created_at) VALUES (?, ?, 'confirmed', NOW())");
    if (!$orderStmt) {
        throw new Exception("Order insert prepare failed: " . $conn->error);
    }
    $orderStmt->bind_param("id", $user_id, $total_price);

    if (!$orderStmt->execute()) {
        throw new Exception("Unable to create order: " . $orderStmt->error);
    }

    // Get the inserted order ID
    $order_id = $conn->insert_id;

    $updateStock = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");
    if (!$updateStock) {
        throw new Exception("Stock update prepare failed: " . $conn->error);
    }
    $updateStock->bind_param("iii", $quantity, $product_id, $quantity);

    if (!$updateStock->execute() || $conn->affected_rows === 0) {
        throw new Exception("Unable to update stock or stock insufficient.");
    }

    // add order item
    $itemStmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, price, quantity) VALUES (?, ?, ?, ?)");
    if (!$itemStmt) {
        throw new Exception("Order item prepare failed: " . $conn->error);
    }
    $itemStmt->bind_param("iidi", $order_id, $product_id, $product["price"], $quantity);

    if (!$itemStmt->execute()) {
        throw new Exception("Unable to insert order item: " . $itemStmt->error);
    }

    $conn->commit();
    header("Location: ../public/orders.php?msg=order_placed");
    exit;
} catch (Exception $e) {
    $conn->rollback();
    die("Buy now failed: " . $e->getMessage());
}
