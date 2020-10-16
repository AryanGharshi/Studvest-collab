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

    $sql_tags = "SELECT bar.id as bar_id, tag.name as tag_name
                 FROM tag_relationship
                 INNER JOIN bar ON tag_relationship.bar_id=bar.id
                 INNER JOIN tag ON tag_relationship.tag_id=tag.id
                 WHERE bar.id= bar.id
                 ORDER BY tag_name";
    $result_tags = ($conn->query($sql_tags));

    if ($result_tags->num_rows > 0) {
        $tags = [];

      while ($row = $result_tags -> fetch_assoc()) {
          array_push($tags, [$row["bar_id"], $row["tag_name"]]);
      }
    }


    $sql = "SELECT bar.id, bar.name, picture.path FROM bar LEFT JOIN (SELECT picture.bar_id, picture.path FROM picture WHERE picture.is_cover=1) AS picture ON bar.id=picture.bar_id";
    $result = ($conn->query($sql));

    if ($result->num_rows > 0) {
        $bars = [];

        while ($row = $result->fetch_assoc()) {
            array_push($bars, [$row["id"], $row["name"],  $row["path"]]);
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
          if($elem[2]==NULL){
            $elem[2]='http://www.bagherra.eu/wp-content/uploads/2016/11/orionthemes-placeholder-image-1-1024x683.png';
          }
            echo '
        <a href="barView.php?barID=' . $elem[0] . '">
            <div class="bar-container" style="background-image: linear-gradient(to bottom, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0) 100%), linear-gradient(180deg, rgba(0, 0, 0, 0) 0%, rgb(0, 0, 0) 100%), url(' . $elem[2] . ')">
                <span class="bar-details">
                    <h1 class="bar-name">' . $elem[1] . '</h1>
                ';
                foreach ($tags as $i) {
                  if ($i[0] == $elem[0]){
                    printf("<button type='button' class='tag'>".$i[1]."</button>");
                  }
                }
                echo '

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
