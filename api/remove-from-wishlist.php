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

if (empty($_POST["product_id"])) {
    die("Invalid product id.");
}

$user_id = intval($_SESSION["user_id"]);
$product_id = intval($_POST["product_id"]);

if ($product_id <= 0) {
    die("Invalid product id.");
}

// Delete from wishlist - only if it belongs to the user
$stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
$stmt->bind_param("ii", $user_id, $product_id);

if ($stmt->execute()) {
    header("Location: ../public/wishlist.php?msg=removed");
    exit;
} else {
    die("Failed to remove from wishlist.");
}
