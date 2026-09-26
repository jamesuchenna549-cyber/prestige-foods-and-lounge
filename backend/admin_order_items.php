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

$orderId = $_GET["order_id"] ?? "";

if (empty($orderId) || !is_numeric($orderId)) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid order ID."
    ]);
    exit;
}

$sql = "
    SELECT
        order_items.product_id,
        order_items.quantity,
        order_items.price,
        products.name,
        products.image
    FROM order_items
    INNER JOIN products
        ON order_items.product_id = products.id
    WHERE order_items.order_id = ?
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$orderId]);

$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$items) {
    echo json_encode([
        "success" => false,
        "message" => "Order items not found."
    ]);
    exit;
}

echo json_encode([
    "success" => true,
    "items" => $items
]);

exit;
?>