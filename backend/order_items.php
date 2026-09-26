<?php

session_start();

require "db_connection.php";

header("Content-Type: application/json");


/*
    Check login
*/

if (!isset($_SESSION["user_id"])) {

    echo json_encode([
        "success" => false,
        "message" => "You must be logged in."
    ]);

    exit;
}


/*
    Check for order ID
*/

if (!isset($_GET["order_id"])) {

    echo json_encode([
        "success" => false,
        "message" => "Order ID is required."
    ]);

    exit;
}


$orderId = $_GET["order_id"];
$userId = $_SESSION["user_id"];


/*
    Get order items
*/

$sql = "
    SELECT
        order_items.product_id,
        order_items.quantity,
        order_items.price,
        products.name,
        products.image,
        orders.total_amount,
        orders.status,
        orders.payment_status

    FROM order_items

    INNER JOIN products
        ON order_items.product_id = products.id

    INNER JOIN orders
        ON order_items.order_id = orders.id

    WHERE order_items.order_id = ?
    AND orders.user_id = ?
";

$statement = $pdo->prepare($sql);

$statement->execute([
    $orderId,
    $userId
]);


$items = $statement->fetchAll(PDO::FETCH_ASSOC);


/*
    Send result
*/




echo json_encode([
    "success" => true,
    "items" => $items,
    "total" => $items[0]["total_amount"],
    "status" => $items[0]["status"],
    "payment_status" => $items[0]["payment_status"]
]);