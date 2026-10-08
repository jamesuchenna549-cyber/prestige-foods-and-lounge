<?php

session_start();

require "../backend/db_connection.php";


// Check login

if (!isset($_SESSION["user_id"])) {

    echo "Please login first.";

    exit;

}


$userId = $_SESSION["user_id"];


// Get event ID

$eventId = $_GET["event_id"] ?? "";


if (empty($eventId) || !ctype_digit($eventId)) {

    echo "Invalid event.";

    exit;

}



// Get event details

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



// Check available slots

$sql = "
SELECT COUNT(id) AS registered_players

FROM snooker_registrations

WHERE event_id = ?

AND payment_status = 'paid'
";


$stmt = $pdo->prepare($sql);

$stmt->execute([$eventId]);


$result = $stmt->fetch(PDO::FETCH_ASSOC);


$registeredPlayers = $result["registered_players"];




if (
    $event["max_players"] !== null &&
    $registeredPlayers >= $event["max_players"]
) {


    echo "Sorry, this competition is already full.";

    exit;


}




// Check duplicate registration

$sql = "
SELECT id

FROM snooker_registrations

WHERE user_id = ?

AND event_id = ?
";


$stmt = $pdo->prepare($sql);


$stmt->execute([

    $userId,

    $eventId

]);


$registration = $stmt->fetch();





if ($registration) {

?>


<!DOCTYPE html>

<html>

<head>

<title>Already Registered</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="css/snooker.css">

</head>


<body>


<section class="message-page">


<div class="message-card">


<p class="section-label">
DE PRESTIGE SNOOKER LOUNGE
</p>



<h1>
Already Registered
</h1>



<p>
You have already registered for this competition.
</p>



<a href="my_registrations.php">
View My Registrations
</a>



<a href="snooker.php">
Back To Competitions
</a>



</div>


</section>


</body>

</html>



<?php

exit;

}


?>



<!DOCTYPE html>

<html>

<head>


<meta name="viewport" content="width=device-width, initial-scale=1.0">


<title>
Register Snooker
</title>


<link rel="stylesheet" href="css/snooker.css">





</head>



<body>



<main class="snooker-page">



<div class="registration-card">



<p class="section-label">
DE PRESTIGE SNOOKER LOUNGE
</p>



<h1>

Register for <?= htmlspecialchars($event["title"]) ?>

</h1>



<p class="registration-intro">

Secure your place in this exciting snooker competition.

</p>




<div class="registration-details">



<div class="detail-item">

<span class="detail-label">
Date
</span>


<span class="detail-value">


<?php

$date = new DateTime($event["event_date"]);

echo $date->format("d F Y");

?>


</span>


</div>





<div class="detail-item">


<span class="detail-label">
Location
</span>


<span class="detail-value">

<?= htmlspecialchars($event["location"]) ?>

</span>


</div>





<div class="detail-item">


<span class="detail-label">
Entry Fee
</span>


<span class="detail-value entry-fee">

₦<?= htmlspecialchars($event["entry_fee"]) ?>

</span>


</div>





<div class="detail-item">


<span class="detail-label">
Players Registered
</span>


<span class="detail-value">

<?= htmlspecialchars($registeredPlayers) ?>

/

<?= htmlspecialchars($event["max_players"]) ?>


</span>


</div>




</div>







<form method="POST" action="save_registration.php" class="snooker-form">


<input 
type="hidden" 
name="event_id" 
value="<?= $event["id"] ?>"
>




<div class="form-group">


<label>
Full Name
</label>


<input

type="text"

name="full_name"

placeholder="Enter your full name"

required

>


</div>






<div class="form-group">


<label>
Phone Number
</label>


<input

type="text"

name="phone"

placeholder="Enter your phone number"

required

>


</div>






<button 
type="submit"
class="snooker-button">

Continue Registration

</button>


<a href="terms_conditions.php" class="rules-link">
View Competition Rules & Regulations
</a>



</form>



</div>



</main>



</body>

</html>
