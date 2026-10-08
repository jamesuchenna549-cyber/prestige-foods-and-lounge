<?php

session_start();

require "../backend/db_connection.php";
require "../backend/config.php";

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


$userId = $_SESSION["user_id"];


// =========================
// GET REFERENCE
// =========================

$reference = trim($_POST["reference"] ?? "");


if (empty($reference)) {

    echo json_encode([
        "success" => false,
        "message" => "Payment reference is required."
    ]);

    exit;
}


// =========================
// FIND REGISTRATION
// =========================

$sql = "

SELECT

snooker_registrations.id,
snooker_registrations.payment_status,
snooker_events.entry_fee


FROM snooker_registrations


JOIN snooker_events

ON snooker_registrations.event_id = snooker_events.id


WHERE snooker_registrations.user_id = ?

AND snooker_registrations.payment_reference = ?

";


$stmt = $pdo->prepare($sql);


$stmt->execute([

$userId,

$reference

]);


$registration = $stmt->fetch(PDO::FETCH_ASSOC);



if (!$registration) {

    echo json_encode([
        "success" => false,
        "message" => "Registration not found."
    ]);

    exit;
}



// Already paid

if ($registration["payment_status"] === "paid") {

    echo json_encode([
        "success" => true,
        "message" => "Payment already verified."
    ]);

    exit;
}



// =========================
// VERIFY WITH PAYSTACK
// =========================

$ch = curl_init(

"https://api.paystack.co/transaction/verify/"
. urlencode($reference)

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


// Temporary for phone development

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



$response = curl_exec($ch);



if ($response === false) {

    echo json_encode([
        "success" => false,
        "message" => curl_error($ch)
    ]);

    curl_close($ch);

    exit;
}


curl_close($ch);



$result = json_decode(
$response,
true
);



// Check Paystack response

if (
!isset($result["status"]) ||
$result["status"] !== true
) {

    echo json_encode([
        "success" => false,
        "message" => "Unable to verify payment."
    ]);

    exit;

}



// =========================
// CHECK PAYMENT STATUS
// =========================

$transaction = $result["data"];


if ($transaction["status"] !== "success") {

    echo json_encode([
        "success" => false,
        "message" => "Payment was not successful."
    ]);

    exit;

}



// =========================
// CHECK AMOUNT
// =========================

$expectedAmount =
(float)$registration["entry_fee"] * 100;


if ((float)$transaction["amount"] !== $expectedAmount) {

    echo json_encode([
        "success" => false,
        "message" => "Payment amount mismatch."
    ]);

    exit;

}



// =========================
// UPDATE REGISTRATION
// =========================

$sql = "

UPDATE snooker_registrations

SET

payment_status = ?

WHERE id = ?

AND user_id = ?

";


$stmt = $pdo->prepare($sql);


$stmt->execute([

"paid",

$registration["id"],

$userId

]);




// SUCCESS

echo json_encode([

"success" => true,

"message" => "Snooker payment verified successfully."

]);


exit;

?>