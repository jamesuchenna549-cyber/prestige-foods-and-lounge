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
// GET COMPETITION ID
// =========================

$eventId = $_GET["id"] ?? "";


if (
    empty($eventId)
    || !ctype_digit($eventId)
) {

    echo "Invalid competition.";
    exit;

}



// =========================
// UPDATE COMPETITION
// =========================

$message = "";


if ($_SERVER["REQUEST_METHOD"] === "POST") {


    $title = trim($_POST["title"] ?? "");

    $description = trim($_POST["description"] ?? "");

    $eventDate = trim($_POST["event_date"] ?? "");

    $eventTime = trim($_POST["event_time"] ?? "");

    $location = trim($_POST["location"] ?? "");

    $entryFee = trim($_POST["entry_fee"] ?? "");

    $maxPlayers = trim($_POST["max_players"] ?? "");



    if ($maxPlayers === "") {

        $maxPlayers = null;

    }



    $sql = "
        UPDATE snooker_events

        SET

        title = ?,
        description = ?,
        event_date = ?,
        event_time = ?,
        location = ?,
        entry_fee = ?,
        max_players = ?

        WHERE id = ?

    ";



    $stmt = $pdo->prepare($sql);



    $stmt->execute([

        $title,

        $description,

        $eventDate,

        $eventTime,

        $location,

        $entryFee,

        $maxPlayers,

        $eventId

    ]);



    $message = "Competition updated successfully.";

}



// =========================
// GET COMPETITION DETAILS
// =========================


$sql = "

SELECT *

FROM snooker_events

WHERE id = ?

";


$stmt = $pdo->prepare($sql);

$stmt->execute([$eventId]);


$event = $stmt->fetch(PDO::FETCH_ASSOC);



if (!$event) {

    echo "Competition not found.";

    exit;

}



?>



<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">


<title>Edit Competition</title>


<link rel="stylesheet" href="admin.css">
<link rel="stylesheet" href="css/edit_competitions.css">


</head>


<body>


<div class="admin-layout">


<main class="admin-main">


<h1>
Edit Competition
</h1>



<?php if (!empty($message)): ?>

<p>
<?= htmlspecialchars($message) ?>
</p>

<?php endif; ?>




<section class="products-card">


<form method="POST">



<div class="form-group">

<label>
Competition Title
</label>

<input

type="text"

name="title"

value="<?= htmlspecialchars($event["title"]) ?>"

required

>

</div>




<div class="form-group">

<label>
Description
</label>


<textarea

name="description"

rows="5"

><?= htmlspecialchars($event["description"]) ?></textarea>


</div>




<div class="form-group">

<label>
Event Date
</label>


<input

type="date"

name="event_date"

value="<?= htmlspecialchars($event["event_date"]) ?>"

required

>


</div>





<div class="form-group">

<label>
Competition Time
</label>


<input

type="time"

name="event_time"

value="<?= htmlspecialchars($event["event_time"]) ?>"

required

>


</div>





<div class="form-group">

<label>
Location
</label>


<input

type="text"

name="location"

value="<?= htmlspecialchars($event["location"]) ?>"

required

>


</div>





<div class="form-group">

<label>
Entry Fee
</label>


<input

type="number"

name="entry_fee"

value="<?= htmlspecialchars($event["entry_fee"]) ?>"

step="0.01"

required

>


</div>





<div class="form-group">

<label>
Maximum Players
</label>


<input

type="number"

name="max_players"

value="<?= htmlspecialchars($event["max_players"]) ?>"

>


</div>




<button 
type="submit"
class="update-button"
>
Update Competition
</button>



</form>


</section>


</main>


</div>


</body>

</html>
