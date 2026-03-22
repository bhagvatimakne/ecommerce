<?php
include "../config/session.php";
include "../config/db.php";

header('Content-Type: application/json');

if (!isset($_SESSION["user_id"])) {
    echo json_encode([]);
    exit;
}

$user_id = $_SESSION["user_id"];

// Get all wishlist product IDs for this user
$stmt = $conn->prepare("SELECT product_id FROM wishlist WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$wishlist_ids = [];
while ($row = $result->fetch_assoc()) {
    $wishlist_ids[] = $row['product_id'];
}

echo json_encode($wishlist_ids);
?>
