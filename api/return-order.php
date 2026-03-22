<?php
include "../config/session.php";
include "../config/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    die("Method not allowed");
}

if (!isset($_SESSION["user_id"])) {
    header("Location: ../public/login.php");
    exit;
}

$order_item_id = intval($_POST["order_item_id"]);
$user_id = intval($_SESSION["user_id"]);

if ($order_item_id <= 0) {
    die("Invalid order item ID");
}

// Start transaction
$conn->begin_transaction();

try {
    // Verify order item belongs to user and get current returned status
    $verifyStmt = $conn->prepare("SELECT oi.id, oi.product_id, oi.quantity, oi.returned, o.user_id FROM order_items oi JOIN orders o ON oi.order_id = o.id WHERE oi.id = ?");
    if (!$verifyStmt) {
        throw new Exception("Verify order item prepare failed: " . $conn->error);
    }
    $verifyStmt->bind_param("i", $order_item_id);
    if (!$verifyStmt->execute()) {
        throw new Exception("Verify order item execute failed: " . $verifyStmt->error);
    }
    $verifyResult = $verifyStmt->get_result();
    
    if ($verifyResult->num_rows === 0) {
        throw new Exception("Order item not found");
    }
    
    $item = $verifyResult->fetch_assoc();
    
    if ($item['user_id'] !== $user_id) {
        throw new Exception("You don't have permission to return this item");
    }
    
    // Check if already returned
    if ($item['returned'] == 1) {
        throw new Exception("This item has already been returned");
    }

    // Restore stock for this item
    $restockStmt = $conn->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
    if (!$restockStmt) {
        throw new Exception("Restock prepare failed: " . $conn->error);
    }
    $restockStmt->bind_param("ii", $item['quantity'], $item['product_id']);
    if (!$restockStmt->execute()) {
        throw new Exception("Restock execute failed: " . $restockStmt->error);
    }
    
    // Update order item status to returned
    $updateStmt = $conn->prepare("UPDATE order_items SET returned = 1 WHERE id = ?");
    if (!$updateStmt) {
        throw new Exception("Update order item prepare failed: " . $conn->error);
    }
    $updateStmt->bind_param("i", $order_item_id);
    if (!$updateStmt->execute()) {
        throw new Exception("Update order item execute failed: " . $updateStmt->error);
    }
    
    // Commit transaction
    $conn->commit();
    
    // Redirect with success message
    header("Location: ../public/orders.php?return=success");
    exit;
    
} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    
    // Redirect with error message
    header("Location: ../public/orders.php?return=error&msg=" . urlencode($e->getMessage()));
    exit;
}
?>