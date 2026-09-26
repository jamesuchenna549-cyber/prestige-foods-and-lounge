<?php

session_start();

require "db_connection.php";

header("Content-Type: application/json");

if (!isset($_SESSION["admin_id"])) {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized."
    ]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request."
    ]);
    exit;
}

$name = trim($_POST["name"] ?? "");
$description = trim($_POST["description"] ?? "");
$price = $_POST["price"] ?? "";
$category = trim($_POST["category"] ?? "");
$image = trim($_POST["image"] ?? "");

if (empty($name) || empty($description) || empty($price) || empty($category) || empty($image)) {
    echo json_encode([
        "success" => false,
        "message" => "All fields are required."
    ]);
    exit;
}

if (!is_numeric($price) || $price <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid price."
    ]);
    exit;
}

$sql = "INSERT INTO products (name, description, price, category, image) VALUES (?, ?, ?, ?, ?)";

$stmt = $pdo->prepare($sql);
$stmt->execute([$name, $description, $price, $category, $image]);

echo json_encode([
    "success" => true,
    "message" => "Product added successfully."
]);

exit;

?>
