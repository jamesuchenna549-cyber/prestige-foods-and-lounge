<?php

session_start();

require "db_connection.php";
require "config.php";

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
// GET PAYMENT REFERENCE
// =========================

$reference = trim(
    $_POST["reference"] ?? ""
);

if (empty($reference)) {

    echo json_encode([
        "success" => false,
        "message" => "Payment reference is required."
    ]);

    exit;
}


$userId = $_SESSION["user_id"];


// =========================
// GET ORDER
// =========================

$sql = "
    SELECT
        id,
        total_amount,
        payment_status,
        payment_reference
    FROM orders
    WHERE user_id = ?
    AND payment_reference = ?
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    $userId,
    $reference
]);

$order = $stmt->fetch(PDO::FETCH_ASSOC);


// =========================
// ORDER NOT FOUND
// =========================

if (!$order) {

    echo json_encode([
        "success" => false,
        "message" => "Order for this payment was not found."
    ]);

    exit;
}


// =========================
// ALREADY PAID
// =========================

if ($order["payment_status"] === "paid") {

    echo json_encode([
        "success" => true,
        "message" => "Payment has already been verified."
    ]);

    exit;
}





// =========================
// VERIFY WITH PAYSTACK
// =========================

$ch = curl_init(
    "https://api.paystack.co/transaction/verify/" .
    urlencode($reference)
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


// TEMPORARY PHONE DEVELOPMENT WORKAROUND

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
// DECODE RESPONSE
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
        "message" => "Unable to verify payment.",
        "paystack_response" => $result
    ]);

    exit;
}


// =========================
// GET TRANSACTION DATA
// =========================

$transaction =
    $result["data"];


// =========================
// CHECK TRANSACTION STATUS
// =========================

if ($transaction["status"] !== "success") {

    echo json_encode([
        "success" => false,
        "message" =>
            "Payment was not successful.",
        "payment_status" =>
            $transaction["status"]
    ]);

    exit;
}


// =========================
// CHECK PAYMENT AMOUNT
// =========================

$expectedAmount =
    (float) $order["total_amount"] * 100;

$paidAmount =
    (float) $transaction["amount"];

if ($paidAmount !== $expectedAmount) {

    echo json_encode([
        "success" => false,
        "message" =>
            "Payment amount does not match the order amount."
    ]);

    exit;
}


// =========================
// UPDATE ORDER
// =========================

$sql = "
    UPDATE orders

    SET
        payment_status = ?,
        payment_reference = ?

    WHERE id = ?
    AND user_id = ?
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    "paid",
    $reference,
    $order["id"],
    $userId
]);


// =========================





// SUCCESS
// =========================

echo json_encode([
    "success" => true,
    "message" => "Payment verified successfully.",
    "order_id" => $order["id"],
    "payment_status" => "paid",
    "payment_reference" => $reference
]);

exit;

?>
















































