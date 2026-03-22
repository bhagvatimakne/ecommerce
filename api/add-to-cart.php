<?php
include "../config/session.php";
include "../config/db.php";

if (!isset($_SESSION["user_id"])) {
    die("Please login first");
}

$user_id = $_SESSION["user_id"];
$product_id = intval($_POST["product_id"]);
$quantity = isset($_POST["quantity"]) ? intval($_POST["quantity"]) : 1;


if ($quantity <= 0) {
    die("Invalid quantity");
}

// Check product stock
$stockCheck = $conn->prepare("SELECT stock FROM products WHERE id = ?");
$stockCheck->bind_param("i", $product_id);
$stockCheck->execute();
$stockResult = $stockCheck->get_result();

if (!$product = $stockResult->fetch_assoc()) {
    die("Product not found");
}

if ($quantity > $product["stock"]) {
    die("Not enough stock available");
}

// Check if already in cart
$check = $conn->prepare(
    "SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?"
);
$check->bind_param("ii", $user_id, $product_id);
$check->execute();
$result = $check->get_result();

if ($row = $result->fetch_assoc()) {

    $newQty = $row["quantity"] + $quantity;

    if ($newQty > $product["stock"]) {
        die("Stock limit exceeded");
    }

    $update = $conn->prepare(
        "UPDATE cart SET quantity = ? WHERE id = ?"
    );
    $update->bind_param("ii", $newQty, $row["id"]);
    $update->execute();

} else {

    $insert = $conn->prepare(
        "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)"
    );
    $insert->bind_param("iii", $user_id, $product_id, $quantity);
    $insert->execute();
}

header("Location: ../public/cart.php");
exit;
