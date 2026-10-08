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
// GET REGISTRATION ID
// =========================

$registrationId = $_POST["registration_id"] ?? "";


if (empty($registrationId) || !ctype_digit($registrationId)) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid registration ID."
    ]);

    exit;
}



// =========================
// GET REGISTRATION DETAILS
// =========================

$sql = "

SELECT

snooker_registrations.id,
snooker_registrations.payment_status,

snooker_events.title,
snooker_events.entry_fee

FROM snooker_registrations


JOIN snooker_events

ON snooker_registrations.event_id = snooker_events.id


WHERE snooker_registrations.id = ?

AND snooker_registrations.user_id = ?

";


$stmt = $pdo->prepare($sql);


$stmt->execute([

    $registrationId,

    $userId

]);


$registration = $stmt->fetch(PDO::FETCH_ASSOC);



if (!$registration) {

    echo json_encode([
        "success" => false,
        "message" => "Registration not found."
    ]);

    exit;
}



// =========================
// CHECK PAYMENT STATUS
// =========================

if ($registration["payment_status"] === "paid") {

    echo json_encode([
        "success" => false,
        "message" => "This registration has already been paid."
    ]);

    exit;
}



// =========================
// PAYMENT DATA
// =========================

$amount = $registration["entry_fee"] * 100;


$email = $_SESSION["user_email"];



// =========================
// SEND TO PAYSTACK
// =========================

$data = [

    "email" => $email,

    "amount" => $amount,

    "currency" => "NGN",

    "callback_url" =>
    "http://0.0.0.0:8080/snooker/snooker_payment_callback.html"

];



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



// temporary for my phone environment

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
        "message" => "Unable to initialize payment."
    ]);

    exit;
}



// =========================
// SAVE PAYMENT REFERENCE
// =========================

$reference =
$result["data"]["reference"];



$sql = "

UPDATE snooker_registrations

SET payment_reference = ?

WHERE id = ?

AND user_id = ?

";



$stmt = $pdo->prepare($sql);


$stmt->execute([

    $reference,

    $registrationId,

    $userId

]);



// =========================
// SEND PAYMENT URL
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
