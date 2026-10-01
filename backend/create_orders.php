<?php

session_start();

require "db_connection.php";
require "error_handler.php";

header("Content-Type: application/json");


// Check if user is logged in

if (!isset($_SESSION["user_id"])) {

    echo json_encode([
        "success" => false,
        "message" => "You must be logged in."
    ]);

    exit;
}


// Receive JSON

$json = file_get_contents("php://input");

$data = json_decode($json, true);


// Check JSON

if (!is_array($data)) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid order data."
    ]);

    exit;
}


// Separate data

$items =
    $data["items"] ?? [];

$userDetails =
    $data["userDetails"] ?? [];


// Check cart

if (empty($items)) {

    echo json_encode([
        "success" => false,
        "message" => "Your cart is empty."
    ]);

    exit;
}


// Validate delivery details

if (
    empty($userDetails["fullName"]) ||
    empty($userDetails["phoneNumber"]) ||
    empty($userDetails["address"]) ||
    empty($userDetails["city"]) ||
    empty($userDetails["state"]) ||
    empty($userDetails["zipCode"])
) {

    echo json_encode([
        "success" => false,
        "message" => "Complete your delivery details."
    ]);

    exit;
}


// Calculate subtotal

$subtotal = 0;


foreach ($items as $item) {

    // Validate ID and quantity

    if (
        !isset($item["id"]) ||
        !isset($item["quantity"]) ||
        !is_numeric($item["id"]) ||
        !is_numeric($item["quantity"]) ||
        $item["quantity"] <= 0
    ) {

        echo json_encode([
            "success" => false,
            "message" => "Invalid cart item."
        ]);

        exit;
    }


    // Get product price from database

    $sql =
        "SELECT price FROM products WHERE id = ?";

    $stmt =
        $pdo->prepare($sql);

    $stmt->execute([
        $item["id"]
    ]);

    $product =
        $stmt->fetch(PDO::FETCH_ASSOC);


    // Check product

    if (!$product) {

        echo json_encode([
            "success" => false,
            "message" => "Product not found."
        ]);

        exit;
    }


    // Calculate item total

    $itemTotal =
        $product["price"] * $item["quantity"];

    $subtotal += $itemTotal;
}


// Delivery and discount

$deliveryFee = 1500;

$discount = 1000;


// Final total

$total =
    ($subtotal + $deliveryFee) - $discount;


// Start transaction

$pdo->beginTransaction();


try {

    // Save/update user details

    $sql = "
        INSERT INTO user_details
        (
            user_id,
            full_name,
            phone,
            address,
            city,
            state,
            zip_code,
            delivery_note
        )

        VALUES (?, ?, ?, ?, ?, ?, ?, ?)

        ON DUPLICATE KEY UPDATE

            full_name = VALUES(full_name),
            phone = VALUES(phone),
            address = VALUES(address),
            city = VALUES(city),
            state = VALUES(state),
            zip_code = VALUES(zip_code),
            delivery_note = VALUES(delivery_note)
    ";


    $stmt =
        $pdo->prepare($sql);


    $stmt->execute([

        $_SESSION["user_id"],

        $userDetails["fullName"],

        $userDetails["phoneNumber"],

        $userDetails["address"],

        $userDetails["city"],

        $userDetails["state"],

        $userDetails["zipCode"],

        $userDetails["deliveryNote"] ?? ""

    ]);


    // Create order

    $sql = "
    INSERT INTO orders
    (
        user_id,
        total_amount,
        status,
        payment_status,
        payment_reference
    )

    VALUES (?, ?, ?, ?, ?)
";

$stmt =
    $pdo->prepare($sql);

$stmt->execute([

    $_SESSION["user_id"],

    $total,

    "pending",

    "unpaid",

    null

]);

    // Get order ID

    $orderId =
        $pdo->lastInsertId();


    // Save order items

    foreach ($items as $item) {

        $sql = "
            SELECT price
            FROM products
            WHERE id = ?
        ";


        $stmt =
            $pdo->prepare($sql);


        $stmt->execute([
            $item["id"]
        ]);


        $product =
            $stmt->fetch(PDO::FETCH_ASSOC);


        $sql = "
            INSERT INTO order_items
            (
                order_id,
                product_id,
                quantity,
                price
            )

            VALUES (?, ?, ?, ?)
        ";


        $stmt =
            $pdo->prepare($sql);


        $stmt->execute([

            $orderId,

            $item["id"],

            $item["quantity"],

            $product["price"]

        ]);
    }


    // Everything worked

    $pdo->commit();


    // Send success response

    echo json_encode([

        "success" => true,

        "message" =>
            "Order created successfully.",

        "order_id" =>
            $orderId,

        "total" =>
            $total

    ]);

    exit;


} catch (Exception $e) {

    // Roll back if something failed

    if ($pdo->inTransaction()) {

        $pdo->rollBack();

    }


    // Show actual error while debugging

    echo json_encode([

        "success" => false,

        "message" =>
            "Failed to create order. " .
            $e->getMessage()

    ]);

    exit;
}
