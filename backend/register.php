<?php

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

    // Check if email already exists
    $sql = "SELECT id FROM users WHERE email = ?";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        echo "Email already exists.";
        exit;
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert the new user
    $sql = "INSERT INTO users (email, password) VALUES (?, ?)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email, $hashedPassword]);

echo "Registration successful.";



   /* header("Location: ../inde.html?registered=success");
*/
exit;
}
?>














