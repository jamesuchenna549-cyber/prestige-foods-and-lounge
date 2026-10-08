<?php

session_start();

require "../backend/db_connection.php";


// =========================
// ADMIN CHECK
// =========================

if (!isset($_SESSION["admin_id"])) {

    header("Location: index.html");
    exit;

}


// =========================
// MESSAGE
// =========================

$message = "";
$messageType = "";




// =========================
// ADD WINNER
// =========================

if ($_SERVER["REQUEST_METHOD"] === "POST") {


    $eventId = $_POST["event_id"] ?? "";
    $playerName = trim($_POST["player_name"] ?? "");
    $year = $_POST["year"] ?? "";


    if (
        empty($eventId)
        || !ctype_digit($eventId)
        || empty($playerName)
        || empty($year)
    ) {


        $message = "Please fill all required fields.";
        $messageType = "error";


    } else {


        $imageName = null;



        // IMAGE UPLOAD

        if (
            isset($_FILES["image"])
            &&
            $_FILES["image"]["error"] === 0
        ) {


          $uploadFolder = "../uploads/";


if (!is_dir($uploadFolder)) {

    mkdir($uploadFolder, 0777, true);

}


$imageName = time() . "_" . basename($_FILES["image"]["name"]);


$uploadPath = $uploadFolder . $imageName;


move_uploaded_file(
    $_FILES["image"]["tmp_name"],
    $uploadPath
);
        }



        $sql = "
            INSERT INTO snooker_winners
            (
                event_id,
                player_name,
                year,
                image
            )

            VALUES (?, ?, ?, ?)
        ";


        $stmt = $pdo->prepare($sql);


        $stmt->execute([

            $eventId,

            $playerName,

            $year,

            $imageName

        ]);



        $message = "Winner added successfully.";
        $messageType = "success";


    }


}



// =========================
// GET COMPETITIONS
// =========================

$sql = "
SELECT *
FROM snooker_events
ORDER BY event_date DESC
";


$stmt = $pdo->prepare($sql);
$stmt->execute();


$events = $stmt->fetchAll(PDO::FETCH_ASSOC);





// =========================
// GET WINNERS
// =========================


$sql = "

SELECT

snooker_winners.*,

snooker_events.title


FROM snooker_winners


JOIN snooker_events

ON snooker_winners.event_id = snooker_events.id


ORDER BY snooker_winners.year DESC

";


$stmt = $pdo->prepare($sql);
$stmt->execute();


$winners = $stmt->fetchAll(PDO::FETCH_ASSOC);



?>



<!DOCTYPE html>

<html lang="en">

<head>


<meta charset="UTF-8">


<meta name="viewport" content="width=device-width, initial-scale=1.0">


<title>
Winners & Champions
</title>


<link rel="stylesheet" href="admin.css">

<link rel="stylesheet" href="css/admin_snooker_winner.css">


</head>



<body>



<div class="admin-layout">



<!-- SIDEBAR -->


<aside class="admin-sidebar">


<div class="admin-brand">

<h2>
Prestige
</h2>

<p>
Restaurant & Lounge
</p>

</div>



<nav class="admin-nav">


<a href="dashboard.html">
Dashboard
</a>


<a href="products.html">
Products
</a>


<a href="orders.html">
Orders
</a>


<a href="customers.html">
Customers
</a>


<a href="snooker.html" class="active">
Snooker
</a>


</nav>


</aside>





<!-- MAIN -->


<main class="admin-main">



<?php if(!empty($message)): ?>


<p class="admin-message <?= $messageType ?>">

<?= htmlspecialchars($message) ?>

</p>


<?php endif; ?>





<header class="admin-header">


<h1>
Winners & Champions
</h1>


<p>
Manage snooker championship winners.
</p>


</header>






<!-- ADD WINNER -->


<section class="products-card">


<h2>
Add Champion
</h2>



<form
method="POST"
enctype="multipart/form-data"
>




<div class="form-group">


<label>
Competition
</label>


<select name="event_id" required>


<option value="">
Select Competition
</option>


<?php foreach($events as $event): ?>


<option value="<?= $event["id"] ?>">

<?= htmlspecialchars($event["title"]) ?>

</option>


<?php endforeach; ?>


</select>


</div>





<div class="form-group">


<label>
Player Name
</label>


<input
type="text"
name="player_name"
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
min="2000"
max="2100"
required
>


</div>






<div class="form-group">


<label>
Winner Image
</label>


<input
type="file"
name="image"
accept="image/*"
>


</div>






<button
type="submit"
class="product-form-button"
>

Save Champion

</button>



</form>



</section>








<!-- WINNER LIST -->


<section class="products-card">


<h2>
Championship History
</h2>




<div class="products-container">



<?php if(empty($winners)): ?>


<p>
No champions added yet.
</p>



<?php else: ?>



<?php foreach($winners as $winner): ?>

<div class="admin-product">



<?php if(!empty($winner["image"])): ?>


<img
src="../uploads/<?= htmlspecialchars($winner["image"]) ?>"
width="120"
class="winner-image"
>


<?php endif; ?>



<h3>
<?= htmlspecialchars($winner["player_name"]) ?>
</h3>



<p>
<strong>
Competition:
</strong>

<?= htmlspecialchars($winner["title"]) ?>

</p>



<p>
<strong>
Year:
</strong>

<?= htmlspecialchars($winner["year"]) ?>

</p>



<div class="competition-actions">


<a
href="edit_snooker_winner.php?id=<?= $winner['id'] ?>"
>
Edit
</a>



<a
href="delete_snooker_winner.php?id=<?= $winner['id'] ?>"
onclick="return confirm('Delete this champion?')"
>
Delete
</a>



</div>

</div>




<?php endforeach; ?>



<?php endif; ?>



</div>



</section>



</main>


</div>


</body>


</html>
