<?php

session_start();

require "../backend/db_connection.php";

header("Content-Type: application/json");


// Check login

if (!isset($_SESSION["user_id"])) {

    echo json_encode([
        "success" => false,
        "message" => "Please login first."
    ]);

    exit;

}


$userId = $_SESSION["user_id"];


// Get form data

$eventId = $_POST["event_id"] ?? "";

$fullName = trim($_POST["full_name"] ?? "");

$phone = trim($_POST["phone"] ?? "");




// Validate

if (
    empty($eventId) ||
    empty($fullName) ||
    empty($phone)
) {

    echo json_encode([
        "success" => false,
        "message" => "All fields are required."
    ]);

    exit;

}




// Check if event exists

$sql = "
SELECT id
FROM snooker_events
WHERE id = ?
";


$stmt = $pdo->prepare($sql);

$stmt->execute([$eventId]);


if (!$stmt->fetch()) {


    echo json_encode([
        "success" => false,
        "message" => "Competition not found."
    ]);

    exit;

}




// Check duplicate registration

$sql = "
SELECT id
FROM snooker_registrations
WHERE user_id = ?
AND event_id = ?
";


$stmt = $pdo->prepare($sql);

$stmt->execute([
    $userId,
    $eventId
]);



if ($stmt->fetch()) {


    echo json_encode([
        "success" => false,
        "message" => "You already registered for this competition."
    ]);

    exit;

}





// Save user profile

$sql = "
INSERT INTO snooker_profiles
(
 user_id,
 full_name,
 phone
)

VALUES (?, ?, ?)

ON DUPLICATE KEY UPDATE

full_name = VALUES(full_name),
phone = VALUES(phone)
";


$stmt = $pdo->prepare($sql);


$stmt->execute([
    $userId,
    $fullName,
    $phone
]);





// Register for competition

$sql = "
INSERT INTO snooker_registrations
(
 user_id,
 event_id,
 payment_status
)

VALUES (?, ?, ?)

";



$stmt = $pdo->prepare($sql);


$stmt->execute([
    $userId,
    $eventId,
    "unpaid"
]);





header("Location: my_registrations.php");
exit;


?>
