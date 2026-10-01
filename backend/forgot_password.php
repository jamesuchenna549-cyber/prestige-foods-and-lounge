<?php




require "../PHPMailer/src/Exception.php";
require "../PHPMailer/src/PHPMailer.php";
require "../PHPMailer/src/SMTP.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
require "config.php";

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
// GET EMAIL
// =========================

$email = trim(
    $_POST["email"] ?? ""
);


// =========================
// VALIDATE EMAIL
// =========================

if (empty($email)) {

    echo json_encode([
        "success" => false,
        "message" => "Email is required."
    ]);

    exit;
}


if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid email."
    ]);

    exit;
}


// =========================
// FIND USER
// =========================

$sql = "
    SELECT id, email
    FROM users
    WHERE email = ?
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    $email
]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);




// =========================
// CHECK USER
// =========================

if (!$user) {

    echo json_encode([
        "success" => true,
        "message" => "If an account exists with that email, a password reset link has been sent."
    ]);

    exit;
}



$sql = "
    SELECT created_at
    FROM password_resets
    WHERE user_id = ?
    ORDER BY created_at DESC
    LIMIT 1
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    $user["id"]
]);

$lastReset = $stmt->fetch(PDO::FETCH_ASSOC);

if ($lastReset) {

    $lastRequestTime =
        strtotime($lastReset["created_at"]);

    $timePassed =
        time() - $lastRequestTime;

    if ($timePassed < 300) {

        echo json_encode([
            "success" => false,
            "message" => "If an account exists with that email, a password reset link has been sent."
        ]);

        exit;
    }
}



// =========================
// GENERATE TOKEN
// =========================

$token = bin2hex(
    random_bytes(32)
);


// =========================
// HASH TOKEN
// =========================

$tokenHash = hash(
    "sha256",
    $token
);


// =========================
// SET EXPIRY
// =========================

$expiresAt = date(
    "Y-m-d H:i:s",
    time() + 3600
);


$sql = "
    DELETE FROM password_resets
    WHERE user_id = ?
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    $user["id"]
]);

// =========================
// STORE RESET REQUEST
// =========================

$sql = "
    INSERT INTO password_resets
    (
        user_id,
        token_hash,
        expires_at
    )
    VALUES (?, ?, ?)
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    $user["id"],
    $tokenHash,
    $expiresAt
]);


// =========================
// CREATE RESET LINK
// =========================

$resetLink =
    "http://0.0.0.0:8080/reset_password.html?token="
    . urlencode($token);

// =========================
// SEND RESET EMAIL
// =========================


$mail = new PHPMailer(true);




try {

   $mail->isSMTP();

$mail->SMTPDebug = 0;

$mail->Host =
    "smtp.gmail.com";

$mail->SMTPAuth =
    true;

$mail->Username = $smtpUsername;
$mail->Password = $smtpPassword;

$mail->SMTPSecure =
    PHPMailer::ENCRYPTION_STARTTLS;

$mail->Port =
    587;

$mail->SMTPOptions = [
    "ssl" => [
        "verify_peer" => false,
        "verify_peer_name" => false,
        "allow_self_signed" => true
    ]
];

    // =========================
    // EMAIL DETAILS
    // =========================

    $mail->setFrom(
        "jamesuchenna549@gmail.com",
 "Prestige Foods & Lounge"
    );

    $mail->addAddress(
        $user["email"]
    );

    $mail->Subject =
        "Password Reset - Prestige Foods & Lounge";

    $mail->Body =
        "Hello,\n\n"
        . "We received a request to reset your password.\n\n"
        . "Click the link below to reset your password:\n\n"
        . $resetLink
        . "\n\n"
        . "This link will expire in 1 hour.\n\n"
        . "If you did not request a password reset, you can ignore this email.";

  $mail->send();

} catch (Exception $e) {

    $sql = "
        DELETE FROM password_resets
        WHERE token_hash = ?
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $tokenHash
    ]);

    echo json_encode([
        "success" => false,
        "message" => "Unable to send password reset email."
    ]);

    exit;
}


echo json_encode([
    "success" => true,
    "message" => "If an account exists with that email, a password reset link has been sent."
]);

exit;

?>
