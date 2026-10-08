<?php

session_start();

require "../backend/db_connection.php";


// Get all snooker competitions

$sql = "
    SELECT *
    FROM snooker_events
    ORDER BY event_date ASC, event_time ASC
";

$stmt = $pdo->prepare($sql);

$stmt->execute();

$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>All Competitions</title>

    <link
        rel="stylesheet"
        href="css/snooker.css"
    >
<link rel="stylesheet" href="css/all_competition_page_design.css">

</head>


<body>


    <main class="snooker-page">

        <section class="all-competitions-page">


            <h1>
                All Snooker Competitions
            </h1>


            <p>
                Explore upcoming, ongoing, completed,
                and previous De Prestige competitions.
            </p>


            <?php if (empty($events)): ?>

                <p>
                    No competitions available.
                </p>


            <?php else: ?>


                <?php foreach ($events as $event): ?>

                    <div class="competition-card">


                        <h2>
                            <?= htmlspecialchars($event["title"]) ?>
                        </h2>


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

                            ₦<?= htmlspecialchars($event["entry_fee"]) ?>

                        </p>


                        <p>
                            <strong>Status:</strong>

                            <?= htmlspecialchars($event["status"]) ?>

                        </p>


                        <a
                            href="competition.php?event_id=<?= $event["id"] ?>"
                        >
                            View Competition
                        </a>


                    </div>


                <?php endforeach; ?>


            <?php endif; ?>


        </section>


    </main>


</body>

</html>
