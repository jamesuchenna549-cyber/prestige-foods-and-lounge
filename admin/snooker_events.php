<?php
session_start();

require "../backend/db_connection.php";


// =========================
// ADMIN ACCESS CHECK
// =========================

if (!isset($_SESSION["admin_id"])) {

    header("Location: index.html");
    exit;

}


// =========================
// MESSAGES
// =========================

$message = "";
$messageType = "";



// =========================
// UPDATE COMPETITION STATUS
// =========================

if (
    $_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST["update_status"])
) {


    $eventId = $_POST["event_id"] ?? "";
    $status = $_POST["status"] ?? "";


    if (
        empty($eventId)
        || !ctype_digit($eventId)
    ) {


        $message = "Invalid competition.";
        $messageType = "error";


    }


    elseif (
        !in_array(
            $status,
            [
                "upcoming",
                "ongoing",
                "completed",
                "cancelled"
            ],
            true
        )
    ) {


        $message = "Invalid status.";
        $messageType = "error";


    }


    else {


        $sql = "
            UPDATE snooker_events
            SET status = ?
            WHERE id = ?
        ";


        $stmt = $pdo->prepare($sql);


        $stmt->execute([
            $status,
            $eventId
        ]);


        $message = "Competition status updated successfully.";
        $messageType = "success";

    }

}





// =========================
// CREATE COMPETITION
// =========================

if (
    $_SERVER["REQUEST_METHOD"] === "POST"
    && !isset($_POST["update_status"])
) {


    $title = trim($_POST["title"] ?? "");

    $description = trim($_POST["description"] ?? "");

    $eventDate = trim($_POST["event_date"] ?? "");

    $eventTime = trim($_POST["event_time"] ?? "");

    $location = trim($_POST["location"] ?? "");

    $entryFee = trim($_POST["entry_fee"] ?? "");

    $maxPlayers = trim($_POST["max_players"] ?? "");



    if (
        empty($title)
        || empty($eventDate)
        || empty($eventTime)
        || empty($location)
        || empty($entryFee)
    ) {


        $message = "Please fill in all required fields.";
        $messageType = "error";


    }


    elseif (
        !is_numeric($entryFee)
        || $entryFee < 0
    ) {


        $message = "Please enter a valid entry fee.";
        $messageType = "error";


    }


    elseif (
        $maxPlayers !== ""
        &&
        (
            !ctype_digit($maxPlayers)
            ||
            $maxPlayers < 1
        )
    ) {


        $message = "Please enter a valid maximum players.";
        $messageType = "error";


    }


    else {


        if ($maxPlayers === "") {

            $maxPlayers = null;

        }



        // Default new competitions to upcoming

        $status = "upcoming";



        $sql = "
            INSERT INTO snooker_events
            (
                title,
                description,
                event_date,
                event_time,
                location,
                entry_fee,
                max_players,
                status
            )

            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
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

            $status

        ]);



        $message = "Competition created successfully.";

        $messageType = "success";


    }

}




// =========================
// GET ALL COMPETITIONS
// =========================


$sql = "
    SELECT *
    FROM snooker_events
    ORDER BY event_date ASC
";


$stmt = $pdo->prepare($sql);

$stmt->execute();


$events = $stmt->fetchAll(PDO::FETCH_ASSOC);



?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Snooker Competitions</title>

<link rel="stylesheet" href="admin.css">
<link rel="stylesheet" href="css/snooker_event.css">



</head>


<body>


<div class="admin-layout">


<!-- =========================
     SIDEBAR
========================== -->


<aside class="admin-sidebar">


<div class="admin-brand">

<h2>Prestige</h2>

<p>Restaurant & Lounge</p>

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


<a href="#" class="logout-link">
Logout
</a>


</aside>





<!-- =========================
     MAIN CONTENT
========================== -->


<main class="admin-main">



<?php if(!empty($message)): ?>


<p class="admin-message <?= $messageType ?>">

<?= htmlspecialchars($message) ?>

</p>


<?php endif; ?>





<header class="admin-header">


<div>

<h1>
Snooker Competitions
</h1>


<p>
Create and manage snooker competitions.
</p>


</div>


</header>






<!-- =========================
 CREATE COMPETITION
========================== -->


<section class="products-card">


<h2>
Create Competition
</h2>



<form method="POST" action="">



<div class="form-group">

<label>
Competition Title
</label>


<input 
type="text"
name="title"
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
></textarea>


</div>






<div class="form-group">

<label>
Event Date
</label>


<input
type="date"
name="event_date"
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
step="0.01"
min="0"
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
min="1"
>


</div>






<button
type="submit"
class="product-form-button"
>

Create Competition

</button>



</form>


</section>







<!-- =========================
 EXISTING COMPETITIONS
========================== -->


<section class="products-card">


<h2>
Existing Competitions
</h2>



<div class="products-container">



<?php if(empty($events)): ?>


<p>
No competitions created yet.
</p>



<?php else: ?>



<?php foreach($events as $event): ?>



<div class="admin-product">



<h2>
<?= htmlspecialchars($event["title"]) ?>
</h2>




<p>
<strong>Date:</strong>

<?= htmlspecialchars($event["event_date"]) ?>

</p>



<p>
<strong>Time:</strong>

<?= htmlspecialchars($event["event_time"]) ?>

</p>



<p>
<strong>Location:</strong>

<?= htmlspecialchars($event["location"]) ?>

</p>




<p>
<strong>Entry Fee:</strong>

₦<?= htmlspecialchars($event["entry_fee"]) ?>

</p>




<p>
<strong>Status:</strong>

<?= htmlspecialchars($event["status"]) ?>

</p>






<!-- STATUS UPDATE -->


<form method="POST" action="">



<input
type="hidden"
name="event_id"
value="<?= $event["id"] ?>"
>



<select name="status">


<option value="upcoming"
<?= $event["status"] == "upcoming" ? "selected" : "" ?>
>
Upcoming
</option>



<option value="ongoing"
<?= $event["status"] == "ongoing" ? "selected" : "" ?>
>
Ongoing
</option>



<option value="completed"
<?= $event["status"] == "completed" ? "selected" : "" ?>
>
Completed
</option>



<option value="cancelled"
<?= $event["status"] == "cancelled" ? "selected" : "" ?>
>
Cancelled
</option>



</select>





<button
type="submit"
name="update_status"
onclick="return confirm('Change competition status?')"
>

Update Status

</button>



</form>







<div class="competition-actions">



<a href="edit_competition.php?id=<?= $event['id'] ?>">

Edit

</a>





<a href="admin_snooker_registrations.php?event_id=<?= $event['id'] ?>">

View Players

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


















