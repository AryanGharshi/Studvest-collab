<!DOCTYPE html>
<html lang="en">
<meta name="viewport" content="width=device-width, initial-scale=1.0">



<!-- LOAD DATA FROM DATABASE -->

<?php
define("MAGICKEY", "ugugUGu221KHJBD84");
require "../inc/connection/conn.php";

function console_log( $data ){
    echo '<script>';
    echo 'console.log('. json_encode( $data ) .')';
    echo '</script>';
}

$barID = $_GET['barID'];

# Get list of bar infos from database
$sql_barInfos = "SELECT bar.name AS barname, bar.description, bar.website, bar.phone, bar.location
                 FROM bar
                 WHERE bar.id=$barID";
$result_barInfos = ($conn->query($sql_barInfos));

# Check if bar exists
if ($result_barInfos->num_rows > 0) {

    # Store the information about the bar
    while ($row = $result_barInfos ->fetch_assoc()) {
        $info["name"] = $row["barname"];
        $info["description"] = $row["description"];
        $info["website"] = $row["website"];
        $info["phone"] = $row["phone"];
        $info["location"] = $row["location"];
    }

    # Load list of drink types
    $sql_drink_types = "SELECT DISTINCT drink_type.name AS name,
                                        drink_type.id AS id,
                                        drink_type.img_url_inactive AS url_inactive,
                                        drink_type.img_url_active AS url_active
                        FROM drink_relationship
                        LEFT JOIN drink ON drink_relationship.drink_id=drink.id
                        LEFT JOIN drink_type ON drink.drink_type_id=drink_type.id
                        WHERE drink_relationship.bar_id=$barID
                        ORDER BY drink_type.rank";
    $result_drink_types = ($conn->query($sql_drink_types));

    # Load list of drinks
    $sql_drinks = "SELECT drink.id,
                          drink.name AS drink_name,
                          CONCAT(drink_relationship.price, ',-') AS price,
                          CONCAT(drink_relationship.student_price, ',-') AS student_price,
                          drink_type.volume_unit AS volume_unit,
                          (drink_relationship.size/volume_units.milliliters) AS volume,
                          drink_relationship.menu AS menu,
                          drink_type.name AS drink_type,
                          drink_type.id AS drink_type_id
                   FROM drink_relationship
                   LEFT JOIN drink ON drink_relationship.drink_id=drink.id
                   LEFT JOIN drink_type ON drink.drink_type_id=drink_type.id
                   LEFT JOIN volume_units ON drink_type.volume_unit=volume_units.unit
                   WHERE drink_relationship.bar_id=$barID
                   ORDER BY drink_type.rank, menu, drink_name";
    $result_drinks = ($conn->query($sql_drinks));

    # Load pictures of bar
    $sql_pictures = "SELECT id, bar_id, path
                     FROM picture
                     WHERE bar_id=$barID";
    $result_pictures = ($conn->query($sql_pictures));

    # Store the pictures about the bar
    $info['pictures'] = [];
    while ($picture = $result_pictures ->fetch_assoc()) {
        array_push($info['pictures'], $picture['path']);
    }

    # Load tags
    $sql_tags = "SELECT tag.id as tag_id, tag.name as tag_name
                 FROM tag_relationship
                 INNER JOIN bar ON tag_relationship.bar_id=bar.id
                 INNER JOIN tag ON tag_relationship.tag_id=tag.id
                 WHERE bar.id=$barID
                 ORDER BY tag_name";
    $result_tags = ($conn->query($sql_tags));
}
else {
    # If bar with passed bar_id does not exist, forward to error page
    header( "Location: 404.php" );
    die;
}

$conn->close();
?>



<!-- HEADER -->

<head>
    <meta charset="UTF-8">
    <title><?php echo $info["name"]; ?> - StudOut</title>
    <link rel='icon' href='../media/favicons/studvest.png' type='image/x-icon'/ >
    <link rel="stylesheet" href="../css/main.css?version=<?= time() ?>">
    <link rel="stylesheet" href="../css/barView.css?version=<?= time() ?>">
</head>



<body>

    <!-- TITLE BAR -->

    <a href="feed.php">
        <?php include('header.php'); ?>
    </a>

    <div class="main">


        <!-- GALLERY -->

        <div class="gallery" id="galleryDiv">
        <span class="nav" id="nav-prev" onclick="changeGalleryImage(-1)">
            <img class="icon" src="../media/icons/left-arrow_white.png">
        </span>
            <span class="nav" id="nav-next" onclick="changeGalleryImage(+1)">
                <img class="icon" src="../media/icons/right-arrow_white.png">
        </span>
        </div>

        <script>
            let galleryImgs = <?php echo json_encode($info['pictures']); ?>;
            let currImgID = 0;

            // Start slideshow
            automaticSlideshow();

            // Function to change gallery pictures (direction=-1: previous picture; direction=+1: next picture)
            function changeGalleryImage (direction) {
                currImgID = (currImgID+direction+galleryImgs.length)%galleryImgs.length;
                document.getElementById("galleryDiv").style.backgroundImage = "url("+galleryImgs[currImgID]+")";
            }

            // Function for automatic slideshow
            function automaticSlideshow() {
                setTimeout(automaticSlideshow, 5000); // Change image every 5 seconds
                changeGalleryImage(+1);
            }
        </script>




        <div class="content">

            <!-- GENERAL INFORMATION ABOUT BAR (NAME, DESCRIPTION, TAGS) -->

            <div class="bar-header">
                <div id="buttons">
                    <?php
                    if($info['location']!='') {
                        $url = preg_replace("(^https?://)", "", $info['location'] );
                        printf('<div class="btn" id="btn-location">');
                        printf('<a href="//'.$url.'">');
                        printf('<img class="icon" src="../media/icons/location_black.png"><br>');
                        printf('</a>');
                        printf('</div>');
                    }
                    if($info['website']!='') {
                        $url = preg_replace("(^https?://)", "", $info['website'] );
                        printf('<div class="btn" id="btn-website">');
                        printf('<a href="//'.$url.'">');
                        printf('<img class="icon" src="../media/icons/websiteicon.png"><br>');
                        printf('</a>');
                        printf('</div>');
                    }
                    if($info['phone']!='') {
                        printf('<div class="btn" id="btn-call">');
                        printf('<a href="tel:+47'.$info["phone"].'">');
                        printf('<img class="icon" src="../media/icons/call_black.png"><br>');
                        printf('</a>');
                        printf('</div>');
                    }
                    ?>
                </div>
                <div id="name">
                    <?php echo $info['name']?>
                </div>
            </div>

            <!-- GENERAL INFORMATION ABOUT BAR (DESCRIPTION, TAGS) -->

            <div class="bar-info">
                <?php
                while ($tag = $result_tags ->fetch_assoc()) {
                    printf("<button type='button' class='tag'>".$tag['tag_name']."</button>");
                }
                ?>
                <div id="desc"><?php echo $info['description']?></div>
            </div>




            <!-- LINKS (CALL, LOCATION, WEBSITE) -->



            <!-- MENU WITH DRINKS-->

            <div class="menu">
                <div id="tabs">
                    <table class="table">
                        <tr>
                            <?php
                            $drink_type = $result_drink_types->fetch_assoc();
                            $min_drink_id = $drink_type['id'];
                            do {
                                echo("<td class='tab_cell' id='tab_cell_".$drink_type['id']."'  onclick='show_drink_tab(".$drink_type['id'].")'>");
                                echo('<img class="tab_icon_active" id="tab_icon_active_'.$drink_type['id'].'" src="'.$drink_type['url_active'].'">');
                                echo('<img class="tab_icon_inactive" id="tab_icon_inactive_'.$drink_type['id'].'" src="'.$drink_type['url_inactive'].'">');
                                echo('<br>'.$drink_type['name']);
                                echo("</td>");
                            }
                            while ($drink_type = $result_drink_types->fetch_assoc());
                            ?>
                        </tr>
                    </table>
                </div>
                <div id="drinks">
                    <?php

                    $drink = $result_drinks->fetch_assoc();

                    while ($drink != NULL) {

                        $tab_name = $drink['drink_type'];

                        # Print separate table for each drink type
                        echo '<div class="drinks_tab" id="drinks_tab_'.$drink['drink_type_id'].'">';

                        while ($tab_name === $drink['drink_type']) {

                            $menu_name = $drink['menu'];

                            echo ("<div class='menu-title'>$menu_name</div>");

                            # Print separate table for that drink type
                            echo '<table class="drinks_table" id="drinks_table_'.$menu_name.'">';

                            # Print header row of table
                            echo("<tr>");
                            echo("<th></th>");
                            echo("<th>Volume</th>");
                            echo("<th>Students</td>");
                            echo("<th>Normal</td>");
                            echo("</tr>");

                            while ($menu_name === $drink['menu'] && $tab_name === $drink['drink_type']) {

                                echo("<tr>");
                                echo("<td class='col-drink'>$drink[drink_name]</td>");
                                echo("<td class='col-normal'>$drink[volume]$drink[volume_unit]</td>");
                                echo("<td class='col-normal'>$drink[student_price]</td>");
                                echo("<td class='col-highlight'>$drink[price]</td>");
                                echo("</tr>");

                                $drink = $result_drinks->fetch_assoc();
                            }
                            echo '</table>';
                        }
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>

            <script>

                show_drink_tab(<?php echo $min_drink_id; ?>);

                function show_drink_tab(id) {

                    // Disable all tabs
                    let all_tab_cells = document.getElementsByClassName("tab_cell");
                    let all_drink_tabs = document.getElementsByClassName("drinks_tab");
                    let all_tab_icons_active = document.getElementsByClassName("tab_icon_active");
                    let all_tab_icons_inactive = document.getElementsByClassName("tab_icon_inactive");

                    for (let i = 0; i < all_tab_cells.length; i++) {
                        all_tab_cells[i].style.color = 'var(--col-secondary)';
                        all_drink_tabs[i].style.display = 'none';
                        all_tab_icons_active[i].style.display = 'none';
                        all_tab_icons_inactive[i].style.display = 'inline';
                    }

                    // Highlight selected tab
                    document.getElementById("tab_cell_"+id).style.color = 'var(--col-highlight)';
                    document.getElementById("drinks_tab_"+id).style.display = 'inline';
                    document.getElementById("tab_icon_inactive_"+id).style.display = 'none';
                    document.getElementById("tab_icon_active_"+id).style.display = 'inline';
                }
            </script>


        </div>
    </div>
    <div class="background">
        <img class="city" src="../media/pictures/CityStudout.png">
    </div>
</body>
</html>
