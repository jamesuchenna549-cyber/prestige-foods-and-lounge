<?php

session_start();

require "../backend/db_connection.php";


// Get upcoming snooker events

$sql = "
SELECT 
    snooker_events.*,

    COUNT(snooker_registrations.id) AS registered_players

FROM snooker_events


LEFT JOIN snooker_registrations

ON snooker_events.id = snooker_registrations.event_id

AND snooker_registrations.payment_status = 'paid'


WHERE snooker_events.status = 'upcoming'
AND snooker_events.event_date >= CURDATE()

GROUP BY snooker_events.id


ORDER BY snooker_events.event_date ASC
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

<title>Snooker Lounge</title>

<link rel="stylesheet" href="css/snooker.css">
</head>


<body>
       <!-- HERO SECTION -->


<section class="snooker-hero">


<div class="hero-overlay">


<h1>
De Prestige Snooker Championship
</h1>


<p>
Compete, Connect and Experience the Game.
</p>

<div class="hero-buttons">

<a href="#events">
View Competitions
</a>


<?php if(isset($_SESSION["user_id"])): ?>

<a href="my_registrations.php">
My Registrations
</a>

<?php endif; ?>


</div>

</div>


</section>


<section class="why-play">

    <div class="why-play-heading">

        <p class="section-label">
            THE DE PRESTIGE EXPERIENCE
        </p>

        <h2>
            Why Play at De Prestige?
        </h2>

        <p>
            More than just a game. Experience competitive snooker,
            great hospitality, and a premium lounge atmosphere.
        </p>

    </div>


    <div class="benefits">


        <!-- Competitive Events -->

        <div class="benefit-card">

            <h3>
                Competitive Events
            </h3>

            <p>
                Take part in exciting snooker competitions
                and challenge yourself against other players.
            </p>

        </div>



        <!-- Premium Snooker Experience -->

        <div class="benefit-card">

            <h3>
                Premium Snooker Experience
            </h3>

            <p>
                Enjoy a professional and comfortable snooker
                lounge designed for players and enthusiasts.
            </p>

        </div>



        <!-- Food & Drinks -->

        <div class="benefit-card">

            <h3>
                Food &amp; Drinks
            </h3>

            <p>
                Enjoy delicious food and refreshing drinks
                while you play, relax, and socialize.
            </p>

        </div>



        <!-- Meet Other Players -->

        <div class="benefit-card">

            <h3>
                Meet Other Players
            </h3>

            <p>
                Connect with other snooker players,
                build friendships, and become part of the community.
            </p>

        </div>


    </div>

</section>


       <!-- EVENT SECTION-->


<section id="events">


<h2>
Upcoming Snooker Competitions
</h2>

<a href="all_competitions.php" class="view-all-competitions">
    View All Competitions
</a>

<section class="champions-link">

    <div class="champions-link-card">

        <p class="section-label">
            DE PRESTIGE HISTORY
        </p>

        <h2>
            Hall of Champions
        </h2>

        <p>
            View previous winners and championship history.
        </p>

        <a href="hall_of_champions.php">
            View Champions
        </a>

    </div>

</section>

<?php if(empty($events)): ?>


<p>
No upcoming competitions available.
</p>



<?php else: ?>



<?php foreach($events as $event): ?>


<div class="event-card">


<h3>
<?= htmlspecialchars($event["title"]) ?>
</h3>



<p>
<?= htmlspecialchars($event["description"]) ?>
</p>


<p>
<strong>Date:</strong>

<?php

$date = new DateTime($event["event_date"]);

echo $date->format("d F Y");

?>

</p>


<p>
<strong>Time:</strong>

<?php

$time = new DateTime($event["event_time"]);

echo $time->format("h:i A");

?>

</p>


<p>
<strong>Location:</strong>

<?= htmlspecialchars($event["location"]) ?>

</p>



<p>
<strong>Entry Fee:</strong>

<?= "₦" . htmlspecialchars($event["entry_fee"]) ?>

</p>


<p>
<strong>Maximum Players:</strong>

<?= htmlspecialchars($event["max_players"]) ?>

</p>


<p>
<strong>Registered Players:</strong>

<?= htmlspecialchars($event["registered_players"]) ?>

/

<?= htmlspecialchars($event["max_players"]) ?>

</p>

<div class="event-buttons">

<a href="register_snooker.php?event_id=<?= $event["id"] ?>">
    Register Now
</a>


<a href="registered_players.php?event_id=<?= $event["id"] ?>">
    View Registered Players
</a>

</div>



</div>



<?php endforeach; ?>



<?php endif; ?>



</section>



</body>

</html>
