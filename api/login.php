<?php
include "../config/db.php";
include "../config/session.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $pass  = $_POST["password"];

    $stmt = $conn->prepare(
        "SELECT id, name, password, role FROM users WHERE email = ?"
    );
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {

        if (password_verify($pass, $user["password"])) {

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["name"]    = $user["name"];
            $_SESSION["email"]   = $email;
            $_SESSION["role"]    = $user["role"];

            header("Location: ../public/index.php");
            exit;
        }
    }

    echo "Invalid login credentials";
}
