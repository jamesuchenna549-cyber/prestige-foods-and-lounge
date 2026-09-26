<?php

session_start();

require "db_connection.php";

header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {
    echo json_encode([
        "success" => false,
        "message" => "You must be logged in."
    ]);
    exit;
}

$userId = $_SESSION["user_id"];

$sql = "
    SELECT
        id,
        total_amount,
        status
    FROM orders
    WHERE user_id = ?
    ORDER BY id DESC
";

$stmt = $pdo->prepare($sql);

$stmt->execute([$userId]);

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "success" => true,
    "orders" => $orders
]);

exit;
?>







