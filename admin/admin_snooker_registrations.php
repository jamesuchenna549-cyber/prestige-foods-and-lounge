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
// GET EVENT ID
// =========================

$eventId = $_GET["event_id"] ?? "";


if (
    empty($eventId)
    || !ctype_digit($eventId)
) {

    echo "Invalid competition.";
    exit;

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





// =========================
// GET REGISTERED PLAYERS
// =========================


$sql = "

SELECT

    snooker_registrations.*,

    users.email

    


FROM snooker_registrations


JOIN users

ON snooker_registrations.user_id = users.id


LEFT JOIN user_profiles

ON users.id = user_profiles.user_id


WHERE snooker_registrations.event_id = ?


ORDER BY snooker_registrations.registered_at DESC

";



$stmt = $pdo->prepare($sql);

$stmt->execute([$eventId]);


$registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);






?>



<!DOCTYPE html>

<html lang="en">


<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">


<title>
Registered Players
</title>


<link rel="stylesheet" href="admin.css">
<link rel="stylesheet" href="css/admin_snooker_registrations.css">


</head>



<body>


<div class="admin-layout">



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


<a href="snooker.html" class="active">
Snooker
</a>


</nav>


</aside>





<main class="admin-main">



<header class="admin-header">


<div>

<h1>
Registered Players
</h1>


<p>
<?= htmlspecialchars($event["title"]) ?>
</p>


</div>


</header>






<section class="products-card">


<h2>
Competition Details
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



</section>







<section class="products-card">


<h2>

Players Registered
(<?= count($registrations) ?>)

</h2>



<?php if(empty($registrations)): ?>


<p>
No players registered yet.
</p>



<?php else: ?>



<div class="products-container">



<?php foreach($registrations as $player): ?>



<div class="admin-product">


<h3>


<strong>
Email:
</strong>

<?= htmlspecialchars($player["email"]) ?>



</h3>


<p>

<strong>
User ID:
</strong>

<?= htmlspecialchars($player["user_id"]) ?>

</p>



<p>

<strong>
Payment:
</strong>



<span class="<?= $player['payment_status'] == 'paid' ? 'payment-paid' : 'payment-unpaid' ?>">
    <?= htmlspecialchars($player["payment_status"]) ?>
</span>

</p>



<p>

<strong>
Reference:
</strong>


<?= htmlspecialchars(
$player["payment_reference"] ?? "Not available"
) ?>

</p>



<p>

<strong>
Registered:
</strong>


<?= htmlspecialchars($player["registered_at"]) ?>

</p>



</div>



<?php endforeach; ?>



</div>



<?php endif; ?>



</section>



</main>



</div>



</body>


</html>
