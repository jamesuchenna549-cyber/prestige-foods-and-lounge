<?php
require "error_handler.php";
session_start();
require "db_connection.php";


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Check that fields are not empty
    if (empty($email) || empty($password)) {
        echo "Email and password are required.";
        exit;
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email.";
        exit;
    }

    // Find the user by email
    $sql = "SELECT * FROM users WHERE email = ?";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if user exists
    if (!$user) {
        echo "Invalid email or password.";
        exit;
    }

    // Verify password
    if (!password_verify($password, $user["password"])) {
        echo "Invalid email or password.";
        exit;
    }

    // Login successful
$_SESSION["user_id"] = $user["id"];
$_SESSION["user_email"] = $user["email"];

header("Location: ../05-homePage.php");
exit;
    
}

?>
