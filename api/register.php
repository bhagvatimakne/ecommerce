<?php
include "../config/db.php";
include "../config/session.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name  = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $pass  = $_POST["password"];

    if (empty($name) || empty($email) || empty($pass)) {
        die("All fields required");
    }

    // Check if email exists
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        die("Email already registered");
    }

    // Hash password
    $hashed = password_hash($pass, PASSWORD_BCRYPT);

    $stmt = $conn->prepare(
        "INSERT INTO users (name, email, password) VALUES (?, ?, ?)"
    );
    $stmt->bind_param("sss", $name, $email, $hashed);

    if ($stmt->execute()) {
        header("Location: ../public/login.php");
        exit;
    } else {
        echo "Registration failed";
    }
}
