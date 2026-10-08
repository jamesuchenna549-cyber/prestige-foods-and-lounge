<?php

session_start();

require "../backend/db_connection.php";


// Get event ID from URL

$eventId = $_GET["event_id"] ?? "";


// Validate event ID

if (empty($eventId) || !ctype_digit($eventId)) {

    echo "Invalid competition.";

    exit;

}


// Get competition details

$sql = "
    SELECT
        snooker_events.*,

        COUNT(
            CASE
                WHEN snooker_registrations.payment_status = 'paid'
                THEN snooker_registrations.id
            END
        ) AS registered_players

    FROM snooker_events

    LEFT JOIN snooker_registrations
        ON snooker_events.id = snooker_registrations.event_id

    WHERE snooker_events.id = ?

    GROUP BY snooker_events.id
";


$stmt = $pdo->prepare($sql);

$stmt->execute([$eventId]);

$event = $stmt->fetch(PDO::FETCH_ASSOC);


// Check if competition exists

if (!$event) {

    echo "Competition not found.";

    exit;

}

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?= htmlspecialchars($event["title"]) ?>
    </title>

    <link
        rel="stylesheet"
        href="css/snooker.css" >

 <link rel="stylesheet" href="css/competition_page_design.css">

</head>


<body>


    <main class="snooker-page">


        <section class="competition-details-page">


            <p class="section-label">
                DE PRESTIGE SNOOKER CHAMPIONSHIP
            </p>


            <h1>
                <?= htmlspecialchars($event["title"]) ?>
            </h1>


            <p class="competition-description">

                <?= htmlspecialchars($event["description"]) ?>

            </p>


            <div class="competition-details">


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
                        Time
                    </span>

                    <span class="detail-value">

                        <?php

                        $time = new DateTime($event["event_time"]);

                        echo $time->format("h:i A");

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
                        Maximum Players
                    </span>

                    <span class="detail-value">

                        <?= htmlspecialchars($event["max_players"]) ?>

                    </span>

                </div>


                <div class="detail-item">

                    <span class="detail-label">
                        Registered Players
                    </span>

                    <span class="detail-value">

                        <?= htmlspecialchars($event["registered_players"]) ?>

                        /

                        <?= htmlspecialchars($event["max_players"]) ?>

                    </span>

                </div>


                <div class="detail-item">

                    <span class="detail-label">
                        Status
                    </span>

                    <span class="detail-value">

                        <?= htmlspecialchars($event["status"]) ?>

                    </span>

                </div>


            </div>


            <div class="competition-actions">


                <a
                    href="register_snooker.php?event_id=<?= $event["id"] ?>"
                >
                    Register Now
                </a>


                <a
                    href="registered_players.php?event_id=<?= $event["id"] ?>"
                >
                    View Registered Players
                </a>


                <a
                    href="all_competitions.php"
                >
                    Back to Competitions
                </a>


            </div>


        </section>


    </main>


</body>

</html>
