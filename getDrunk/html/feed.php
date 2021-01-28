<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudOut</title>


    <link rel="stylesheet" href="https://res.cloudinary.com/dxfq3iotg/raw/upload/v1569006288/BBBootstrap/choices.min.css?version=7.0.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script>
    <script src="https://res.cloudinary.com/dxfq3iotg/raw/upload/v1569006273/BBBootstrap/choices.min.js?version=7.0.0"></script>

    <link rel="stylesheet" href="../css/main.css?version=<?= time() ?>">
    <link rel="stylesheet" href="../css/feed.css?version=<?= time() ?>">
    <link rel='icon' href='../media/favicons/studvest.png' type='image/x-icon'/ >
</head>

<?php
    function console_log( $data ){
        echo '<script>';
        echo 'console.log('. json_encode( $data ) .')';
        echo '</script>';
    }
?>

<?php
    define("MAGICKEY", "ugugUGu221KHJBD84");
    require "../inc/connection/conn.php";

    // Extract filter selections
    if(isset($_POST['submit_filter'])) {

        $tags_selected = $_POST['select_tags'];
        $bars_selected = $_POST['select_bars'];

        if($tags_selected != null) {
            $where_clause = "WHERE (";
            foreach ($tags_selected as &$item) {
                $where_clause .= "tag_relationship.tag_id=$item OR ";
            }
            $where_clause .= "FALSE)";
        }

        if($bars_selected != null) {
            $where_clause .= $where_clause!="" ? " AND (" : "WHERE (";
            foreach ($bars_selected as &$item) {
                $where_clause .= "bar.id=$item OR ";
            }
            $where_clause .= "FALSE)";
        }
    }

    // Load bar details
    $sql = "SELECT DISTINCT bar.id, bar.name, picture.path FROM bar LEFT JOIN (SELECT picture.bar_id, picture.path FROM picture WHERE picture.is_cover=1) AS picture ON bar.id=picture.bar_id LEFT JOIN tag_relationship ON bar.id=tag_relationship.bar_id $where_clause";
    $result = ($conn->query($sql));
    if ($result->num_rows > 0) {
        $bars = [];
        while ($row = $result->fetch_assoc()) {
            array_push($bars, [$row["id"], $row["name"],  $row["path"]]);
        }
    } else {
        console_log("No bars were found for the following SQL query: ".$sql);
    }

    // Load all tags for the bars
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

    // Load all tags for the dropdown
    $sql = "SELECT tag.id, tag.name, COUNT(*) AS num
            FROM tag_relationship
            LEFT JOIN tag ON tag.id = tag_relationship.tag_id
            GROUP BY tag_relationship.tag_id
            ORDER BY num DESC";
    $res = ($conn->query($sql));
    $options_tags = "";
    while ($row = $res -> fetch_assoc()) { // Create html code for select options
        $id = $row['id'];
        $tag = $row['name'];
        $options_tags .= "<option value='$id'>$tag</option>";
    }
    foreach ($tags_selected as &$tag_id) {
        $options_tags = str_replace("<option value='$tag_id'>", "<option value='$tag_id' selected>", $options_tags);
    }

    // Load all bars for the dropdown
    $sql = "SELECT id, name FROM bar ORDER BY name";
    $res = ($conn->query($sql));
    $options_bars = "";
    while ($row = $res -> fetch_assoc()) { // Create html code for select options
        $id = $row['id'];
        $name = $row['name'];
        $options_bars .= "<option value='$id'>$name</option>";
    }
    foreach ($bars_selected as &$bar_id) {
        $options_bars = str_replace("<option value='$bar_id'>", "<option value='$bar_id' selected>", $options_bars);
    }

    $conn->close();
?>



<body>
    <a href="feed.php">
        <?php include("header.php"); ?>
    </a>

    <div class="wrapper">
        <div class="filter-box">
            <form action='' method='post'>
                <select id="select_tags" name="select_tags[]"  placeholder="Search for a tag" multiple> <?php echo $options_tags; ?> </select>
                <select id="select_bars" name="select_bars[]"  placeholder="Search for a bar" multiple> <?php echo $options_bars; ?> </select>
                <button type='submit' id='submit_filter' name='submit_filter' value=''>Show results</button>
                <button type='submit' id='submit_filter' name='reset_filter' value=''>Reset filter</button>
            </form>

        </div>

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

    <script type='text/javascript' src='../js/feed.js?version=<?= time() ?>'></script>
</body>
</html>
