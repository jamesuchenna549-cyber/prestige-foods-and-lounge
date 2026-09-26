<?php
session_start();

require "db_connection.php";

header("Content-Type: application/json");

if (!isset($_SESSION["admin_id"])) {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized."
    ]);
    exit;
}

$sql = "
    SELECT
        users.id,
        users.email,
        users.created_at,
        user_details.full_name,
        user_details.phone,
        user_details.address,
        user_details.city,
        user_details.state,
        user_details.zip_code,
        user_details.delivery_note
    FROM users
    LEFT JOIN user_details
        ON users.id = user_details.user_id
    WHERE users.role = 'customer'
    ORDER BY users.id DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute();

$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "success" => true,
    "customers" => $customers
]);

exit;
?>
