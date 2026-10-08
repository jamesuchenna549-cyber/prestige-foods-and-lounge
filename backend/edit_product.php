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


// Receive the product details.
$id = $_POST["id"] ?? "";
$name = trim($_POST["name"] ?? "");
$description = trim($_POST["description"] ?? "");
$price = $_POST["price"] ?? "";
$category = trim($_POST["category"] ?? "");


// Validate the product ID.
if (
    !is_string($id) ||
    !ctype_digit($id) ||
    (int) $id < 1
) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid product ID."
    ]);
    exit;
}


// Validate the required product details.
if (
    $name === "" ||
    $description === "" ||
    $price === "" ||
    $category === ""
) {
    echo json_encode([
        "success" => false,
        "message" => "All product fields are required."
    ]);
    exit;
}


// Validate the price.
if (!is_numeric($price) || (float) $price <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid price."
    ]);
    exit;
}


// Check whether the product exists and retrieve its current image.
$sql = "SELECT image FROM products WHERE id = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([(int) $id]);

$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo json_encode([
        "success" => false,
        "message" => "Product not found."
    ]);
    exit;
}


// Keep the existing image unless a new image is uploaded.
$imagePath = $product["image"];


// Process a new image if one was selected.
if (
    isset($_FILES["image"]) &&
    $_FILES["image"]["error"] !== UPLOAD_ERR_NO_FILE
) {

    $image = $_FILES["image"];


    // Check for upload errors.
    if ($image["error"] !== UPLOAD_ERR_OK) {
        echo json_encode([
            "success" => false,
            "message" => "Image upload failed."
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


    // Check the actual image MIME type.
    $finfo = finfo_open(FILEINFO_MIME_TYPE);

    $mimeType = finfo_file(
        $finfo,
        $image["tmp_name"]
    );

    finfo_close($finfo);


    $allowedTypes = [
        "image/jpeg" => "jpg",
        "image/png" => "png",
        "image/webp" => "webp"
    ];


    if (!isset($allowedTypes[$mimeType])) {
        echo json_encode([
            "success" => false,
            "message" => "Only JPG, PNG, and WEBP images are allowed."
        ]);
        exit;
    }


    // Create the upload folder if necessary.
    $uploadFolder = "../uploads/products/";

    if (
        !is_dir($uploadFolder) &&
        !mkdir($uploadFolder, 0755, true) &&
        !is_dir($uploadFolder)
    ) {
        echo json_encode([
            "success" => false,
            "message" => "Unable to create the image folder."
        ]);
        exit;
    }


    // Generate a unique filename using the verified image type.
    $fileName = bin2hex(random_bytes(16))
        . "."
        . $allowedTypes[$mimeType];

    $uploadPath = $uploadFolder . $fileName;


    // Save the uploaded image.
    if (!move_uploaded_file(
        $image["tmp_name"],
        $uploadPath
    )) {
        echo json_encode([
            "success" => false,
            "message" => "Unable to save the new image."
        ]);
        exit;
    }


    // Save the relative image path in the database.
    $imagePath = "uploads/products/" . $fileName;
}


// Update the product details and image path.
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
    $imagePath,
    (int) $id
]);


echo json_encode([
    "success" => true,
    "message" => "Product updated successfully."
]);

exit;

?>