<!DOCTYPE html>
<html lang="en">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<?php
define("MAGICKEY", "ugugUGu221KHJBD84");
require "../inc/connection/conn.php";

$barID = $_GET['barID'];

# Get list of bar infos from database
$sql_barInfos = "SELECT bar.name AS barname, bar.description, bar.website, bar.phone, bar.location   
                 FROM bar  
                 WHERE bar.id=$barID";
$result_barInfos = ($conn->query($sql_barInfos));

# Get list of drinks from database
$sql_drinks = "SELECT drink.id, drink.name AS drink_name, drink_relationship.price, drink_relationship.size, menu.name AS menu_name
               FROM drink_relationship 
               LEFT JOIN drink ON drink_relationship.drink_id=drink.id 
               LEFT JOIN menu ON drink.menu_id=menu.id 
               WHERE drink_relationship.bar_id=$barID
               ORDER BY menu_name, drink_name";
$result_drinks = ($conn->query($sql_drinks));

$conn->close();

# Check if bar exists
if ($result_barInfos->num_rows > 0) {

    # Store the informations about the bar
    while ($row = $result_barInfos ->fetch_assoc()) {
        $info["name"] = $row["barname"];
        $info["description"] = $row["description"];
        $info["website"] = $row["website"];
        $info["phone"] = $row["phone"];
        $info["location"] = $row["location"];
    }

    # Store the drinks
    $menus = [];
    while ($row = $result_drinks ->fetch_assoc()) {

        # Retrieve name of the menu for the respective drink
        $menu_name = $row["menu_name"];

        # Check if there's already a menu for this type of drink, else create new menu
        if(array_key_exists($menu_name, $menus)) { $menu = $menus[$menu_name]; }
        else {                                     $menu = []; }

        # Add drink to the menu
        array_push($menu, $row);

        # Store the menu
        $menus[$menu_name] = $menu;
    }

} else {
    # If bar with passed bar_id does not exist, forward to error page
    header( "Location: 404.php" );
    die;
}
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
                <img class="galleryIcon" src="../media/icons/arrow_left.png">
            </span>
            <span class="galleryNavigationNext" onclick="changeGalleryImage(+1)">
                <img class="galleryIcon" src="../media/icons/arrow_right.png">
            </span>
        </div>
    </div>

    <div class="content">
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
                        printf('<a href="'.$info["website"].'" target="_blank">');
                        printf('<img class="linkIcon" src="../media/icons/website.png"><br>');
                        printf('Website');
                        printf('</td>');
                    }

                    if($info['phone']!='') {
                        printf('<td>');
                        printf('<a href="tel:+47'.$info["phone"].'" target="_blank">');
                        printf('<img class="linkIcon" src="../media/icons/call.png"><br>');
                        printf('Call');
                        printf('</td>');
                    }

                    if($info['location']!='') {
                        printf('<td>');
                        printf('<a href="'.$info["location"].'" target="_blank">');
                        printf('<img class="linkIcon" src="../media/icons/location.png"><br>');
                        printf('Location');
                        printf('</td>');
                    }
                    ?>
                </tr>
            </table>
        </div>

        <div class="menu">
            <?php
            foreach ($menus as $menu_name => $drinks) {
                echo("<table class='menuTable'>");
                echo("<tr>");
                echo("<td class='menuName'>$menu_name</td>");
                echo("<td class='header' id='headerVolume'>Volume</td>");
                echo("<td class='header' id='headerPrice'>Price</td>");
                echo("</tr>");

                foreach ($drinks as $drink) {
                    echo("<tr>");
                    echo("<td class='drinkName'>$drink[drink_name]</td>");
                    echo("<td class='volume'>$drink[size]l</td>");
                    echo("<td class='price'>$drink[price],-</td>");
                    echo("</tr>");
                }
                echo("</table>");
            }
            ?>
            <!--<div class="menuSwitch" id="menuSwitch"></div>
               <div class="menuHeader" id="menuHeader"></div>
               <div class="menuBody" id="menuBody"></div>-->
        </div>
    </div>

    <!-- Transform menu array into javascript format -->
    <script type="text/javascript">let menus = <?php echo json_encode($menus); ?>;</script>
    <script src="../js/barView.js?version=<?= time() ?>"></script>
</body>
</html>