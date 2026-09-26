<?php

$host = "MY_DATABASE_HOST";
$dbname = "MY_DATABASE_NAME";
$username = "MY_DATABASE_USERNAME";
$password = "MY_DATABASE_PASSWORD";

try {

    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname",
        $username,
        $password
    );

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

} catch (PDOException $e) {

    echo "Connection failed.";

}

?>