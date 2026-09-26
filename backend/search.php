<?php

require "db_connection.php";

$search = $_GET["search"];

$sql = "SELECT * FROM products WHERE name LIKE ?";

$stmt = $pdo->prepare($sql);

$stmt->execute(["%$search%"]);

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($result);