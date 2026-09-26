<?php

require "db_connection.php";

$sql = "SELECT * FROM products";

$statement = $pdo->query($sql);

$products = $statement->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($products);
