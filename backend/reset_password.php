<?php
require "error_handler.php";
session_start();

require "db_connection.php";


header("Content-Type: application/json");


// =========================
// CHECK REQUEST METHOD
// =========================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);

    exit;
}


// =========================
// GET TOKEN
// =========================

$token = trim(
    $_POST["token"] ?? ""
);


// =========================
// GET NEW PASSWORD
// =========================

$password =
    $_POST["password"] ?? "";


// =========================
// CHECK TOKEN
// =========================

if (empty($token)) {

    echo json_encode([
        "success" => false,
        "message" => "Reset token is required."
    ]);

    exit;
}


// =========================
// CHECK PASSWORD
// =========================

if (empty($password)) {

    echo json_encode([
        "success" => false,
        "message" => "New password is required."
    ]);

    exit;
}
if (strlen($password) < 8) {
    echo json_encode([
        "success" => false,
        "message" => "Password must be at least 8 characters long."
    ]);
    exit;
}


// =========================
// HASH TOKEN
// =========================

$tokenHash = hash(
    "sha256",
    $token
);


// =========================
// FIND RESET REQUEST
// =========================

$sql = "
    SELECT
        id,
        user_id,
        expires_at
    FROM password_resets
    WHERE token_hash = ?
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    $tokenHash
]);

$reset = $stmt->fetch(PDO::FETCH_ASSOC);


// =========================
// CHECK RESET REQUEST
// =========================

if (!$reset) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid or expired reset link."
    ]);

    exit;
}


// =========================
// CHECK EXPIRY
// =========================

if (
    strtotime($reset["expires_at"])
    < time()
) {

    echo json_encode([
        "success" => false,
        "message" => "This reset link has expired."
    ]);

    exit;
}


// =========================
// HASH NEW PASSWORD
// =========================

$passwordHash = password_hash(
    $password,
    PASSWORD_DEFAULT
);


// =========================
// UPDATE USER PASSWORD
// =========================

$sql = "
    UPDATE users
    SET password = ?
    WHERE id = ?
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    $passwordHash,
    $reset["user_id"]
]);


// =========================
// DELETE RESET TOKEN
// =========================

$sql = "
    DELETE FROM password_resets
    WHERE id = ?
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    $reset["id"]
]);


// =========================
// SUCCESS
// =========================

echo json_encode([
    "success" => true,
    "message" => "Password changed successfully."
]);

exit;

?>
