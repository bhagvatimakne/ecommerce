<?php
include "../config/session.php";
include "../config/db.php";

if (!isset($_SESSION["user_id"])) {
    die("Login required");
}

$user_id = $_SESSION["user_id"];
$cart_id = intval($_POST["cart_id"]);
$action  = $_POST["action"];

// Get current cart row
$stmt = $conn->prepare(
    "SELECT cart.quantity, products.stock
     FROM cart
     JOIN products ON cart.product_id = products.id
     WHERE cart.id = ? AND cart.user_id = ?"
);
$stmt->bind_param("ii", $cart_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$row = $result->fetch_assoc()) {
    die("Cart item not found");
}

$currentQty = $row["quantity"];
$stock      = $row["stock"];

// Decide new quantity
if ($action === "increase") {
    $newQty = $currentQty + 1;

    if ($newQty > $stock) {
        $newQty = $stock; // clamp to stock
    }

} elseif ($action === "decrease") {
    $newQty = $currentQty - 1;
} else {
    die("Invalid action");
}

// If quantity becomes 0 → remove item
if ($newQty <= 0) {

    $del = $conn->prepare(
        "DELETE FROM cart WHERE id = ? AND user_id = ?"
    );
    $del->bind_param("ii", $cart_id, $user_id);
    $del->execute();

} else {

    $update = $conn->prepare(
        "UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?"
    );
    $update->bind_param("iii", $newQty, $cart_id, $user_id);
    $update->execute();
}

header("Location: ../public/cart.php");
exit;
