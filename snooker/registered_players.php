<?php

session_start();

require "../backend/db_connection.php";


// Get event ID

$eventId = $_GET["event_id"] ?? "";

if (empty($eventId) || !ctype_digit($eventId)) {

    die("Invalid event.");

}


// Get event details

$sql = "
SELECT 
    title,
    event_date,
    location,
    max_players

FROM snooker_events

WHERE id = ?
";


$stmt = $pdo->prepare($sql);

$stmt->execute([$eventId]);

$event = $stmt->fetch(PDO::FETCH_ASSOC);



if (!$event) {

    die("Competition not found.");

}



// Get registered players

$sql = "
SELECT

    snooker_profiles.full_name,
    snooker_registrations.payment_status

FROM snooker_registrations


JOIN snooker_profiles

ON snooker_registrations.user_id = snooker_profiles.user_id


WHERE snooker_registrations.event_id = ?
AND snooker_registrations.payment_status = 'paid'
ORDER BY snooker_registrations.registered_at ASC

";


$stmt = $pdo->prepare($sql);

$stmt->execute([$eventId]);


$players = $stmt->fetchAll(PDO::FETCH_ASSOC);



?>


<!DOCTYPE html>

<html>

<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Registered Players</title>

<link rel="stylesheet" href="css/snooker.css">
<link rel="stylesheet" href="css/registered_players.css">



</head>


<body>


<section class="registered-page">


<div class="registered-header">


<h1>
<?= htmlspecialchars($event["title"]); ?>
</h1>


<div class="registered-info">


<p>
<strong>Date</strong>

<?php

$date = new DateTime($event["event_date"]);

echo $date->format("d F Y");

?>

</p>



<p>
<strong>Location</strong>

<?= htmlspecialchars($event["location"]); ?>

</p>



<p>
<strong>Players</strong>

<?= count($players); ?>

/

<?= htmlspecialchars($event["max_players"]); ?>

</p>


</div>


</div>




<div class="players-section">


<h2>
Registered Players
</h2>



<?php if(empty($players)): ?>


<p class="empty-message">
No players registered yet.
</p>



<?php else: ?>



<div class="players-list">


<?php foreach($players as $player): ?>


<div class="player-card">


<span>
Player
</span>


<h3>
<?= htmlspecialchars($player["full_name"]); ?>
</h3>


<p>
Payment Confirmed
</p>


</div>



<?php endforeach; ?>


</div>



<?php endif; ?>


</div>



</section>



</body>

</html>
