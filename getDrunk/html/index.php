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

    $sql = "SELECT bar_id, name, path FROM bar INNER JOIN picture ON bar.id = picture.bar_id WHERE picture.is_cover=1";
    $result = ($conn->query($sql));

    if ($result->num_rows > 0) {
        $bars = [];

        while ($row = $result->fetch_assoc()) {
            array_push($bars, [$row["bar_id"], $row["name"],  $row["path"]]);
            if path is not there push another path
        }
    } else {
        echo "0 results";
    }
    $conn->close();

?>



<body>
    <?php include('header.php'); ?>
    <div class="title">

    </div>
    <div class="wrapper">
        <?php
        foreach ($bars as $elem) {
            echo '
        <a href="barView.php?barID=' . $elem[0] . '">
            <div class="examplebar" style="background-image: linear-gradient(to bottom, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0) 100%), linear-gradient(180deg, rgba(0, 0, 0, 0) 0%, rgb(0, 0, 0) 100%), url(' . $elem[2] . ')">
                <div class="barText">
                    <h1>' . $elem[1] . '</h1>
                </div>
                <div class="ellipse">
                <!--
                    <div class="barlogo">
                        <img src="http://www.heidisbierbar.no/media/heidisbierbar_finallogo.png" alt="Bar Logo">
                    </div>
                    -->
                </div>
                <span class="barTags">

                </span>
            </div>
        </a>
        ';
        }
        ?>
    </div>
    <div class="background">
      <img class="city" src="../media/pictures/CityStudout.png">
    </div>
</body>
</html>
