<?php

require "error_handler.php";

session_start();

require "db_connection.php";

header("Content-Type: application/json");


/* =========================
   CHECK ADMIN LOGIN
========================= */

if (!isset($_SESSION["admin_id"])) {

    echo json_encode([
        "success" => false,
        "message" => "Unauthorized."
    ]);

    exit;
}


/* =========================
   CHECK REQUEST METHOD
========================= */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        "success" => false,
        "message" => "Invalid request."
    ]);

    exit;
}


/* =========================
   GET PRODUCT ID
========================= */

$id = $_POST["id"] ?? "";

if (empty($id) || !is_numeric($id)) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid product ID."
    ]);

    exit;
}


/* =========================
   GET PRODUCT IMAGE
========================= */

$sql = "
    SELECT image
    FROM products
    WHERE id = ?
    LIMIT 1
";

$stmt = $pdo->prepare($sql);

$stmt->execute([$id]);

$product = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$product) {

    echo json_encode([
        "success" => false,
        "message" => "Product not found."
    ]);

    exit;
}


/* =========================
   CHECK PRODUCT IN ORDERS
========================= */

$sql = "
    SELECT id
    FROM order_items
    WHERE product_id = ?
    LIMIT 1
";

$stmt = $pdo->prepare($sql);

$stmt->execute([$id]);


if ($stmt->fetch()) {

    echo json_encode([
        "success" => false,
        "message" => "This product cannot be deleted because it is already part of an order."
    ]);

    exit;
}


/* =========================
   DELETE PRODUCT
========================= */

$sql = "
    DELETE FROM products
    WHERE id = ?
";

$stmt = $pdo->prepare($sql);

$stmt->execute([$id]);


/* =========================
   DELETE IMAGE FILE
========================= */

$imagePath = $product["image"];

if (!empty($imagePath)) {

    $fullImagePath = "../" . $imagePath;

    if (file_exists($fullImagePath)) {
        unlink($fullImagePath);
    }
}


/* =========================
   SUCCESS RESPONSE
========================= */

echo json_encode([
    "success" => true,
    "message" => "Product deleted successfully."
]);

exit;

?>