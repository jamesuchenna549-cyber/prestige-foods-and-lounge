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

$orderId = $_POST["order_id"] ?? "";
$status = trim($_POST["status"] ?? "");

if (empty($orderId) || !is_numeric($orderId)) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid order ID."
    ]);
    exit;
}

$allowedStatuses = ["pending", "processing", "completed", "cancelled"];

if (!in_array($status, $allowedStatuses)) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid order status."
    ]);
    exit;
}

$sql = "UPDATE orders SET status = ? WHERE id = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$status, $orderId]);

echo json_encode([
    "success" => true,
    "message" => "Order status updated successfully."
]);

exit;
?>