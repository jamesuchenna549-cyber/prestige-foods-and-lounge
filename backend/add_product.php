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

$name = trim($_POST["name"] ?? "");
$description = trim($_POST["description"] ?? "");
$price = $_POST["price"] ?? "";
$category = trim($_POST["category"] ?? "");


if (
    empty($name) ||
    empty($description) ||
    empty($price) ||
    empty($category)
) {
    echo json_encode([
        "success" => false,
        "message" => "All fields are required."
    ]);
    exit;
}


if (!is_numeric($price) || $price <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid price."
    ]);
    exit;
}


// Check that an image was uploaded.
if (
    !isset($_FILES["image"]) ||
    $_FILES["image"]["error"] !== UPLOAD_ERR_OK
) {
    echo json_encode([
        "success" => false,
        "message" => "Please select a product image."
    ]);
    exit;
}


$image = $_FILES["image"];


// Allowed image types.
$allowedTypes = [
    "image/jpeg",
    "image/png",
    "image/webp"
];


// Check the actual MIME type of the uploaded file.
$finfo = finfo_open(FILEINFO_MIME_TYPE);

$mimeType = finfo_file(
    $finfo,
    $image["tmp_name"]
);

finfo_close($finfo);


if (!in_array($mimeType, $allowedTypes, true)) {
    echo json_encode([
        "success" => false,
        "message" => "Only JPG, PNG, and WEBP images are allowed."
    ]);
    exit;
}


// Limit image size to 2 MB.
if ($image["size"] > 2 * 1024 * 1024) {
    echo json_encode([
        "success" => false,
        "message" => "Image must not be larger than 2 MB."
    ]);
    exit;
}


// Create the product image folder if it doesn't exist.
$uploadFolder = "../uploads/products/";

if (!is_dir($uploadFolder)) {
    mkdir($uploadFolder, 0777, true);
}


// Give the image a unique filename.
$extension = pathinfo(
    $image["name"],
    PATHINFO_EXTENSION
);

$fileName = uniqid("product_", true) . "." . strtolower($extension);

$uploadPath = $uploadFolder . $fileName;


// Move the uploaded image into the products folder.
if (!move_uploaded_file(
    $image["tmp_name"],
    $uploadPath
)) {
    echo json_encode([
        "success" => false,
        "message" => "Unable to upload product image."
    ]);
    exit;
}


// Save the path in the database.
$imagePath = "uploads/products/" . $fileName;


$sql = "
    INSERT INTO products
    (
        name,
        description,
        price,
        category,
        image
    )
    VALUES (?, ?, ?, ?, ?)
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    $name,
    $description,
    $price,
    $category,
    $imagePath
]);


echo json_encode([
    "success" => true,
    "message" => "Product added successfully."
]);

exit;

?>