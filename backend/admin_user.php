<?php

session_start();

require "db_connection.php";

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request."
    ]);
    exit;
}

$email = trim($_POST["email"] ?? "");
$password = trim($_POST["password"] ?? "");

if (empty($email) || empty($password)) {
    echo json_encode([
        "success" => false,
        "message" => "Email and password are required."
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid email."
    ]);
    exit;
}

$sql = "SELECT * FROM users WHERE email = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$email]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid email or password."
    ]);
    exit;
}

if (!password_verify($password, $user["password"])) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid email or password."
    ]);
    exit;
}

if ($user["role"] !== "admin") {
    echo json_encode([
        "success" => false,
        "message" => "You are not authorized to access the admin panel."
    ]);
    exit;
}

$_SESSION["admin_id"] = $user["id"];
$_SESSION["admin_email"] = $user["email"];

echo json_encode([
    "success" => true,
    "message" => "Admin login successful."
]);

exit;
?>
