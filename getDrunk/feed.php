<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Title</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/feed.css">
</head>

<?php
define("MAGICKEY", "ugugUGu221KHJBD84");
require "inc/connection/conn.php";

$sql = "SELECT name FROM bar";
$result = ($conn->query($sql));

if ($result->num_rows > 0) {
    $bars =[];

    while ($row = $result ->fetch_assoc()) {
        array_push($bars, $row["name"]);
    }
} else {
    echo "0 results";
}
$conn->close();
?>

<body>
<div class="titlebar">App Name</div>
<div class="wrapper">


<?php

foreach ($bars as $elem){
    echo '
    <div class="examplebar">
        <div class="barText">
            <h1>'.$elem.'</h1>
            </div>
            <div class="ellipse">
            <div class="barlogo">
                <img src="http://www.heidisbierbar.no/media/heidisbierbar_finallogo.png" alt="Bar Logo">
            </div>
          </div>
        </div>
    ';
}

?>
</div>
</body>
</html>
