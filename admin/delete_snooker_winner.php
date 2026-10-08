<?php

session_start();

require "../backend/db_connection.php";


if (!isset($_SESSION["admin_id"])) {

    header("Location: index.html");
    exit;

}



$id = $_GET["id"] ?? "";



if(empty($id) || !ctype_digit($id)){

    echo "Invalid winner.";
    exit;

}




$sql = "

DELETE FROM snooker_winners

WHERE id = ?

";



$stmt = $pdo->prepare($sql);


$stmt->execute([$id]);



header("Location: admin_snooker_winners.php");

exit;

?>
