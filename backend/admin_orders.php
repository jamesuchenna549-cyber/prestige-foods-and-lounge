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
$sql = "
    SELECT
        orders.id,
        orders.user_id,
        orders.total_amount,
        orders.status,
        orders.payment_status,
        users.email
    FROM orders
    INNER JOIN users
        ON orders.user_id = users.id
    ORDER BY orders.id DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute();

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "success" => true,
    "orders" => $orders
]);

exit;
?>
