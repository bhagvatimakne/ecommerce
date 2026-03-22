<?php
include "../config/session.php";
include "../config/db.php";

header('Content-Type: application/json');

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Login required']);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$user_id = $_SESSION["user_id"];
$product_id = intval($_POST["product_id"] ?? 0);

if ($product_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid product id']);
    exit;
}

// Check if product exists in wishlist
$stmt = $conn->prepare("SELECT id FROM wishlist WHERE user_id=? AND product_id=?");
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Remove from wishlist
    $delete = $conn->prepare("DELETE FROM wishlist WHERE user_id=? AND product_id=?");
    $delete->bind_param("ii", $user_id, $product_id);
    $delete->execute();
    
    echo json_encode(['success' => true, 'added' => false, 'message' => 'Removed from wishlist']);
} else {
    // Add to wishlist
    $insert = $conn->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
    $insert->bind_param("ii", $user_id, $product_id);
    $insert->execute();
    
    echo json_encode(['success' => true, 'added' => true, 'message' => 'Added to wishlist']);
}
?>
