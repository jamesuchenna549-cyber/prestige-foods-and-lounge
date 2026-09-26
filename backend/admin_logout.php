<?php

session_start();

header("Content-Type: application/json");

unset($_SESSION["admin_id"]);
unset($_SESSION["admin_email"]);

session_destroy();

echo json_encode([
    "success" => true,
    "message" => "Admin logged out successfully."
]);

exit;
?>
