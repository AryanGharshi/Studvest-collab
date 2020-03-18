<!DOCTYPE html>
<html lang="en">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<?php
define("MAGICKEY", "ugugUGu221KHJBD84");
require "../inc/connection/conn.php";

$barID = $_GET['barID'];

$sql = "SELECT bar.id AS barid, bar.name AS barname, bar.description, bar.website, bar.phone, bar.location, menu.id AS menuid, menu.name AS menuname FROM bar
                INNER JOIN menu ON bar.id = menu.bar_id
                WHERE bar.id = " . $barID;

$result = ($conn->query($sql));

if ($result->num_rows > 0) {
    $info = [];
    $menus = [];

    while ($row = $result ->fetch_assoc()) {
        $info["name"] = $row["barname"];
        $info["description"] = $row["description"];
        $info["website"] = $row["website"];
        $info["phone"] = $row["phone"];
        $info["location"] = $row["location"];
        array_push($menus, $row["menuname"]);
    }
} else {
    header( "Location: 404.php" );
    die;
}
$conn->close();
?>

<head>
    <meta charset="UTF-8">
    <title><?php echo $info["name"]; ?></title>
    <link rel="stylesheet" href="../css/main.css?version=<?= time() ?>">
    <link rel="stylesheet" href="../css/barView.css?version=<?= time() ?>">
</head>


<body>
    <?php include('titlebar.php'); ?>

    <div class="gallery" id="galleryDiv">
        <div class="galleryNavigation">
            <span class="galleryNavigationPrevious" onclick="changeGalleryImage(-1)">
                <img class="navigationIcon" src="../media/icons/arrow_left.png">
            </span>
            <span class="galleryNavigationNext" onclick="changeGalleryImage(+1)">
                <img class="navigationIcon" src="../media/icons/arrow_right.png">
            </span>
        </div>
    </div>

    <div class="content">

        <script type="text/javascript">
            let menuTitles = <?php echo json_encode($menus); ?>;
        </script>

        <div>
            <a href="index.php">
                <img class="navigationIcon" id="closeIcon" src="../media/icons/close.png">
            </a>
        </div>
        <div class="barInfo">
            <div class="barName"><?php echo $info['name']?></div>
            <div class="barDesc"><?php echo $info['description']?></div>
        </div>

        <div class="links">
            <table class="linksTable">
                <tr>
                <?php
                    if($info['website']!='') {
                        printf('<td>');
                        printf('<a href="'.$info["website"].'">');
                        printf('<img class="linkIcon" src="../media/icons/website.png"><br>');
                        printf('Website');
                        printf('</td>');
                    }

                    if($info['phone']!='') {
                        printf('<td>');
                        printf('<a href="tel:+47'.$info["phone"].'">');
                        printf('<img class="linkIcon" src="../media/icons/call.png"><br>');
                        printf('Call');
                        printf('</td>');
                    }

                    if($info['location']!='') {
                        printf('<td>');
                        printf('<a href="'.$info["location"].'">');
                        printf('<img class="linkIcon" src="../media/icons/location.png"><br>');
                        printf('Location');
                        printf('</td>');
                    }
                ?>
                </tr>
            </table>
        </div>

        <div class="menu">
            <div class="menuSwitch" id="menuSwitch"></div>
            <div class="menuHeader" id="menuHeader"></div>
            <div class="menuBody" id="menuBody"></div>
        </div>
    </div>
<script src="../js/barView.js?version=<?= time() ?>"></script>
</body>
</html>