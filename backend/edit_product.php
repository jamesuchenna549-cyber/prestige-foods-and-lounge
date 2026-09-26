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

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request."
    ]);
    exit;
}

$id = $_POST["id"] ?? "";
$name = trim($_POST["name"] ?? "");
$description = trim($_POST["description"] ?? "");
$price = $_POST["price"] ?? "";
$category = trim($_POST["category"] ?? "");
$image = trim($_POST["image"] ?? "");

if (empty($id) || !is_numeric($id)) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid product ID."
    ]);
    exit;
}

if (
    empty($name) ||
    empty($description) ||
    empty($price) ||
    empty($category) ||
    empty($image)
) {
    echo json_encode([
        "success" => false,
        "message" => "All product fields are required."
    ]);
    exit;
}

if (!is_numeric($price)) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid price."
    ]);
    exit;
}

$sql = "
    UPDATE products
    SET
        name = ?,
        description = ?,
        price = ?,
        category = ?,
        image = ?
    WHERE id = ?
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    $name,
    $description,
    $price,
    $category,
    $image,
    $id
]);

echo json_encode([
    "success" => true,
    "message" => "Product updated successfully."
]);

exit;
?>



