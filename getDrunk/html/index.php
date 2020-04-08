<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudOut</title>
    <link rel="stylesheet" href="../css/main.css?version=<?= time() ?>">
    <link rel="stylesheet" href="../css/feed.css?version=<?= time() ?>">
    <link rel='icon' href='../media/favicons/studvest.png' type='image/x-icon'/ >
</head>


<?php
    define("MAGICKEY", "ugugUGu221KHJBD84");
    require "../inc/connection/conn.php";

    $sql = "SELECT id, name FROM bar";
    $result = ($conn->query($sql));

    if ($result->num_rows > 0) {
        $bars = [];

        while ($row = $result->fetch_assoc()) {
            array_push($bars, [$row["id"], $row["name"]]);
        }
    } else {
        echo "0 results";
    }
    $conn->close();
?>

<body>
    <?php include('header.php'); ?>

    <div class="wrapper">


        <?php

        foreach ($bars as $elem) {
            echo '
        <a href="barView.php?barID=' . $elem[0] . '">
            <div class="examplebar">
                <div class="barText">
                    <h1>' . $elem[1] . '</h1>
                </div>
                <div class="ellipse">
                    <div class="barlogo">
                        <img src="http://www.heidisbierbar.no/media/heidisbierbar_finallogo.png" alt="Bar Logo">
                    </div>
                </div>
            </div>
        </a>
        ';
        }

        ?>
    </div>
</body>
</html>
