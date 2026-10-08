<?php

session_start();

require "../backend/db_connection.php";


// Check login

if (!isset($_SESSION["user_id"])) {

    die("Please login first.");

}


$userId = $_SESSION["user_id"];


// Get user's registrations

$sql = "

SELECT

snooker_registrations.id AS registration_id,

snooker_events.title,
snooker_events.event_date,
snooker_events.event_time,
snooker_events.location,
snooker_events.entry_fee,

snooker_registrations.payment_status,
snooker_registrations.payment_reference


FROM snooker_registrations


JOIN snooker_events

ON snooker_registrations.event_id = snooker_events.id


WHERE snooker_registrations.user_id = ?

ORDER BY snooker_registrations.registered_at DESC

";


$stmt = $pdo->prepare($sql);

$stmt->execute([$userId]);


$registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>


<!DOCTYPE html>

<html>

<head>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>My Snooker Registrations</title>

<link rel="stylesheet" href="css/snooker.css">
<link rel="stylesheet" href="css/my_registration_design.css">

</head>


<body>


<section class="snooker-page">


<div class="my-registrations-container">


<div class="page-heading">

<h1>
My Snooker Competitions
</h1>

<p>
Manage your registered competitions and payment status.
</p>

</div>



<?php if(empty($registrations)): ?>


<div class="empty-registrations">

<h2>
No Registrations Yet
</h2>

<p>
You have not registered for any snooker competition.
</p>

</div>



<?php else: ?>



<div class="registration-list">


<?php foreach($registrations as $registration): ?>


<div class="my-registration-card">



<div class="registration-card-header">


<div>

<p class="card-label">
COMPETITION
</p>


<h2>
<?= htmlspecialchars($registration["title"]) ?>
</h2>

</div>



<span class="payment-badge <?= $registration["payment_status"] ?>">

<?= htmlspecialchars($registration["payment_status"]) ?>

</span>


</div>





<div class="my-registration-details">


<div class="my-detail">

<span>
DATE
</span>

<strong>
<?php

$date = new DateTime($registration["event_date"]);

echo $date->format("d F Y");

?>

</strong>

</div>

<div class="my-detail">

<span>
TIME
</span>

<strong>
<?php

$time = new DateTime($registration["event_time"]);

echo $time->format("h:i A");

?>
</strong>

</div>




<div class="my-detail">

<span>
LOCATION
</span>

<strong>
<?= htmlspecialchars($registration["location"]) ?>
</strong>

</div>




<div class="my-detail">

<span>
ENTRY FEE
</span>

<strong class="gold-text">

₦<?= htmlspecialchars($registration["entry_fee"]) ?>

</strong>

</div>


</div>




<?php if($registration["payment_status"] === "unpaid"): ?>


<div class="registration-action">


<button 
class="snooker-button"
onclick="payForSnooker(<?= $registration['registration_id']; ?>)"
>

Pay Now

</button>


</div>


<?php endif; ?>


<div class="registration-action">


<a href="terms_conditions.php" class="rules-button">

View Rules & Regulations

</a>


</div>



</div>


<?php endforeach; ?>


</div>


<?php endif; ?>


</div>


</section>



<script>

function payForSnooker(registrationId){


fetch("initialize_snooker_payment.php", {

method: "POST",

headers: {

"Content-Type":"application/x-www-form-urlencoded"

},

body:

"registration_id=" + registrationId

})


.then(response => response.json())


.then(data => {


if(data.success){

window.location.href = data.authorization_url;

}else{

alert(data.message);

}


})


.catch(error => {


console.log(error);

alert("Something went wrong.");


});


}

</script>


</body>
</html>
