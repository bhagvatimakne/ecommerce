<?php
include "../config/session.php";
include "../config/db.php";

// Check if user is admin
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["email"]) || $_SESSION["email"] !== "admin@gmail.com") {
    header("Location: ../public/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $category_id = intval($_POST['category_id']);

    if (empty($name) || empty($description) || $price <= 0 || $category_id <= 0) {
        header("Location: add-product.php?error=invalid_input");
        exit;
    }

    // Image upload
    $imageName = '';

    if (!empty($_FILES['image']['name'])) {
        $targetDir = "upload/";
        
        // Create directory if it doesn't exist
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $imageName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $imageName;
        
        // Validate image file
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowed = array("jpg", "jpeg", "png", "gif", "webp", "avif");
        
        if (!in_array($imageFileType, $allowed)) {
            header("Location: add-product.php?error=invalid_image");
            exit;
        }

        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            header("Location: add-product.php?error=upload_failed");
            exit;
        }
    } else {
        header("Location: add-product.php?error=no_image");
        exit;
    }

    // Insert into DB
    $stmt = $conn->prepare(
        "INSERT INTO products (name, description, price, image, category_id, stock, status, created_at)
         VALUES (?, ?, ?, ?, ?, ?, 'active', NOW())"
    );

    if (!$stmt) {
        die("Insert prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ssdssi", $name, $description, $price, $imageName, $category_id, $stock);

    if ($stmt->execute()) {
        header("Location: manage-products.php?success=1");
        exit;
    } else {
        header("Location: add-product.php?error=db_error");
        exit;
    }
}
?>
