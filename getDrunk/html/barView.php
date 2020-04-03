<!DOCTYPE html>
<html lang="en">
<meta name="viewport" content="width=device-width, initial-scale=1.0">



<!-- LOAD DATA FROM DATABASE -->

<?php
define("MAGICKEY", "ugugUGu221KHJBD84");
require "../inc/connection/conn.php";

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

    # Load list of drinks
    $sql_drinks = "SELECT drink.id, drink.name AS drink_name, drink_relationship.price, drink_relationship.size, menu.name AS menu_name
                   FROM drink_relationship 
                   LEFT JOIN drink ON drink_relationship.drink_id=drink.id 
                   LEFT JOIN menu ON drink.menu_id=menu.id 
                   WHERE drink_relationship.bar_id=$barID
                   ORDER BY menu_name, drink_name";
    $result_drinks = ($conn->query($sql_drinks));

    # Put drinks into separate menus
    $menus = [];
    while ($drink = $result_drinks ->fetch_assoc()) {
        $menu_name = $drink["menu_name"];
        # If the menu for the drink already exists: Append the drink to the menu
        if(array_key_exists($menu_name, $menus)) {
            array_push($menus[$menu_name], $drink);
        }
        # Else: Create a new menu with just that drink
        else {
            $menus[$menu_name] = [$drink];
        }
    }

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

    <?php include('header.php'); ?>



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

        <div class="bar-info">
            <div id="name"><?php echo $info['name']?></div>
            <div id="desc"><?php echo $info['description']?></div>
            <?php
            while ($tag = $result_tags ->fetch_assoc()) {
                printf("<button type='button' class='tag'>".$tag['tag_name']."</button>");
            }
            ?>
        </div>




        <!-- LINKS (CALL, LOCATION, WEBSITE) -->

        <div class="links" id="links-mobile">
            <a type='button' class="btn" href="<?php echo($info["location"]) ?>>">
                <img class="icon" src="../media/icons/location_black.png">
            </a>
        </div>
        <div class="links" id="links-web">
            <table class="table" >
                <tr>
                    <?php
                    if($info['website']!='') {
                        printf('<td>');
                        printf('<a href="'.$info["website"].'" target="_blank">');
                        printf('<img class="icon" src="../media/icons/web_white.png"><br>');
                        printf('Website');
                        printf('</td>');
                    }

                    if($info['phone']!='') {
                        printf('<td>');
                        printf('<a href="tel:+47'.$info["phone"].'" target="_blank">');
                        printf('<img class="icon" src="../media/icons/call_white.png"><br>');
                        printf('Call');
                        printf('</td>');
                    }

                    if($info['location']!='') {
                        printf('<td>');
                        printf('<a href="'.$info["location"].'" target="_blank">');
                        printf('<img class="icon" src="../media/icons/location_white.png"><br>');
                        printf('Location');
                        printf('</td>');
                    }
                    ?>
                </tr>
            </table>
        </div>



        <!-- MENU WITH DRINKS-->

        <div class="menu">
            <?php
            foreach ($menus as $menu_name => $drinks) {
                echo("<table class='table'>");
                echo("<tr>");
                echo("<td class='menu-title'>$menu_name</td>");
                echo("<td class='table-header' id='header-volume'>Volume</td>");
                echo("<td class='table-header' id='header-price'>Price</td>");
                echo("</tr>");

                foreach ($drinks as $drink) {
                    echo("<tr>");
                    echo("<td class='col-drink'>$drink[drink_name]</td>");
                    echo("<td class='col-volume'>$drink[size]l</td>");
                    echo("<td class='col-price'>$drink[price],-</td>");
                    echo("</tr>");
                }
                echo("</table>");
            }
            ?>
        </div>
    </div>
</body>
</html>