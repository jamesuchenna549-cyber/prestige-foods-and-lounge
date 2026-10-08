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



// GET WINNER

$sql = "
SELECT *
FROM snooker_winners
WHERE id = ?
";


$stmt = $pdo->prepare($sql);

$stmt->execute([$id]);


$winner = $stmt->fetch(PDO::FETCH_ASSOC);



if(!$winner){

    echo "Winner not found.";
    exit;

}





$message = "";



if($_SERVER["REQUEST_METHOD"] === "POST"){


    $playerName = trim($_POST["player_name"]);
    $year = $_POST["year"];



    $sql = "

    UPDATE snooker_winners

    SET 
    player_name = ?,
    year = ?

    WHERE id = ?

    ";



    $stmt = $pdo->prepare($sql);


    $stmt->execute([

        $playerName,
        $year,
        $id

    ]);



    $message = "Champion updated successfully.";


}





?>


<!DOCTYPE html>

<html>

<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Edit Champion</title>

<link rel="stylesheet" href="admin.css">

</head>


<body>


<div class="admin-layout">


<main class="admin-main">


<h1>
Edit Champion
</h1>


<?php if($message): ?>

<p>
<?= $message ?>
</p>

<?php endif; ?>



<form method="POST">


<div class="form-group">

<label>
Player Name
</label>


<input
type="text"
name="player_name"
value="<?= htmlspecialchars($winner["player_name"]) ?>"
required
>

</div>



<div class="form-group">

<label>
Year
</label>


<input
type="number"
name="year"
value="<?= htmlspecialchars($winner["year"]) ?>"
required
>

</div>



<button
type="submit"
class="product-form-button"
>

Update Champion

</button>


</form>


</main>


</div>


</body>

</html>
