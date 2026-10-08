<?php

require "../backend/db_connection.php";


// Get past winners

$sql = "

SELECT

snooker_winners.player_name,

snooker_winners.year,

snooker_winners.image,

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
Hall of Champions
</title>


<link rel="stylesheet" href="css/snooker.css">

<link rel="stylesheet" href="css/hall_of_champions.css">


</head>


<body>



<main class="snooker-page">



<section class="champions-page">



<p class="section-label">
DE PRESTIGE HISTORY
</p>




<h1>
Hall of Champions 🏆
</h1>




<p class="champions-intro">

Celebrating the players who have made history at De Prestige Snooker Lounge.

</p>





<div class="champions-list">



<?php if(empty($winners)): ?>



<p>
No champions recorded yet.
</p>



<?php else: ?>



<?php foreach($winners as $winner): ?>



<div class="champion-card">





<div class="champion-image">



<?php if(!empty($winner["image"])): ?>


<img

src="../uploads/<?= htmlspecialchars($winner["image"]) ?>"

alt="<?= htmlspecialchars($winner["player_name"]) ?>"

>

<?php else: ?>


<img

src="images/default-champion.png"

alt="Champion"

>



<?php endif; ?>



</div>







<div class="champion-info">



<h2>

<?= htmlspecialchars($winner["player_name"]) ?>

</h2>





<p>

Competition:

<strong>

<?= htmlspecialchars($winner["title"]) ?>

</strong>

</p>






<p>

Year:

<strong>

<?= htmlspecialchars($winner["year"]) ?>

</strong>

</p>





</div>





</div>



<?php endforeach; ?>



<?php endif; ?>





</div>




</section>



</main>




</body>

</html>
