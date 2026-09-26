<?php

session_start();

require "db_connection.php";

header("Content-Type: application/json");


// =========================
// CHECK LOGIN
// =========================

if (!isset($_SESSION["user_id"])) {

    echo json_encode([
        "success" => false,
        "message" => "You must be logged in."
    ]);

    exit;
}


// =========================
// GET ORDER ID
// =========================

$orderId = $_POST["order_id"] ?? "";

if (empty($orderId) || !ctype_digit($orderId)) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid order ID."
    ]);

    exit;
}


$userId = $_SESSION["user_id"];


// =========================
// GET ORDER FROM DATABASE
// =========================

$sql = "
    SELECT
        id,
        total_amount,
        payment_status
    FROM orders
    WHERE id = ?
    AND user_id = ?
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    $orderId,
    $userId
]);

$order = $stmt->fetch(PDO::FETCH_ASSOC);


// =========================
// CHECK ORDER
// =========================

if (!$order) {

    echo json_encode([
        "success" => false,
        "message" => "Order not found."
    ]);

    exit;
}


// =========================
// CHECK PAYMENT STATUS
// =========================

if ($order["payment_status"] === "paid") {

    echo json_encode([
        "success" => false,
        "message" => "This order has already been paid for."
    ]);

    exit;
}


// =========================
// PAYSTACK TEST SECRET KEY
// =========================


$secretKey = "require config.php";


// =========================
// PREPARE PAYMENT
// =========================

$amount = $order["total_amount"] * 100;

$email = $_SESSION["user_email"];


// =========================
// PAYMENT DATA
// =========================

$data = [
    "email" => $email,
    "amount" => $amount,
    "currency" => "NGN",
    "callback_url" => "http://0.0.0.0:8080/payment_callback.html"
];


// =========================
// SEND REQUEST TO PAYSTACK
// =========================

$ch = curl_init(
    "https://api.paystack.co/transaction/initialize"
);

curl_setopt($ch, CURLOPT_POST, true);

curl_setopt(
    $ch,
    CURLOPT_POSTFIELDS,
    json_encode($data)
);

curl_setopt($ch, CURLOPT_HTTPHEADER, [
"Authorization: Bearer " . $paystackSecretKey,
    "Content-Type: application/json"
]);

curl_setopt(
    $ch,
    CURLOPT_RETURNTRANSFER,
    true
);


// =========================
// TEMPORARY PHONE DEVELOPMENT
// WORKAROUND
// =========================

// Your phone's PHP environment currently
// cannot find its CA certificate.
// These should NOT be used in production.

curl_setopt(
    $ch,
    CURLOPT_SSL_VERIFYPEER,
    false
);

curl_setopt(
    $ch,
    CURLOPT_SSL_VERIFYHOST,
    false
);


// =========================
// EXECUTE REQUEST
// =========================

$response = curl_exec($ch);


// =========================
// CHECK CURL ERROR
// =========================

if ($response === false) {

    echo json_encode([
        "success" => false,
        "curl_error" => curl_error($ch)
    ]);

    curl_close($ch);

    exit;
}


curl_close($ch);


// =========================
// DECODE PAYSTACK RESPONSE
// =========================

$result = json_decode(
    $response,
    true
);


// =========================
// CHECK PAYSTACK RESPONSE
// =========================

if (
    !$result ||
    !isset($result["status"]) ||
    !$result["status"]
) {

    echo json_encode([
        "success" => false,
        "message" => "Unable to initialize payment.",
        "paystack_response" => $result
    ]);

    exit;
}


// =========================
// SAVE PAYMENT REFERENCE
// =========================
if (
    !isset($result["data"]) ||
    !isset($result["data"]["reference"]) ||
    !isset($result["data"]["authorization_url"])
) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid response from Paystack."
    ]);

    exit;
}

$reference =
    $result["data"]["reference"];


$sql = "
    UPDATE orders
    SET payment_reference = ?
    WHERE id = ?
    AND user_id = ?
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    $reference,
    $orderId,
    $userId
]);


// =========================
// SUCCESS
// =========================

echo json_encode([
    "success" => true,
    "authorization_url" =>
        $result["data"]["authorization_url"],

    "reference" =>
        $reference
]);

exit;
?>
