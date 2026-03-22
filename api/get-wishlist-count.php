<?php
include "../config/session.php";
include "../config/db.php";

header('Content-Type: application/json');

if (!isset($_SESSION["user_id"])) {
    echo json_encode(['count' => 0]);
    exit;
}

$user_id = $_SESSION["user_id"];
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM wishlist WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

echo json_encode(['count' => $data['count']]);
?>
