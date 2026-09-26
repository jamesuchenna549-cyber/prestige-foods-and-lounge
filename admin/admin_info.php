<?php

session_start();

header("Content-Type: application/json");

if (!isset($_SESSION["admin_id"])) {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized."
    ]);
    exit;
}

echo json_encode([
    "success" => true,
    "email" => $_SESSION["admin_email"]
]);

exit;
?>