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

$id = $_POST["id"] ?? "";

if (empty($id) || !is_numeric($id)) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid product ID."
    ]);
    exit;
}

/* Check if product is used in an order */

$sql = "SELECT id FROM order_items WHERE product_id = ? LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);

if ($stmt->fetch()) {
    echo json_encode([
        "success" => false,
        "message" => "This product cannot be deleted because it is already part of an order."
    ]);
    exit;
}

/* Delete product */

$sql = "DELETE FROM products WHERE id = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);

echo json_encode([
    "success" => true,
    "message" => "Product deleted successfully."
]);

exit;
?>