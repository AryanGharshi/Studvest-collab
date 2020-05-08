<!DOCTYPE html>
<html lang="en">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<head>
    <meta charset="UTF-8">
    <title>Edit bar details</title>
    <link rel="stylesheet" href="../css/inputForm.css?version=<?= time() ?>">
    <link rel="stylesheet" href="../css/main.css?version=<?= time() ?>">
</head>

<body>
<?php

if (isset($_POST['barID'])) {

    define("MAGICKEY", "ugugUGu221KHJBD84");
    require "../inc/connection/conn.php";

    $barID = $_POST['barID'];

    # Get list of bar infos from database
    $sql_barInfos = "SELECT bar.name AS barname, bar.description, bar.website, bar.phone, bar.location   
                     FROM bar  
                     WHERE bar.id=$barID";
    $result_barInfos = ($conn->query($sql_barInfos));

    # Check if bar exists
    if ($result_barInfos->num_rows > 0) {

        # Store the information about the bar
        while ($row = $result_barInfos->fetch_assoc()) {
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
                            WHERE drink_relationship.bar_id=$barID";
        $result_drink_types = ($conn->query($sql_drink_types));

        # Load list of drinks
        $sql_drinks = "SELECT drink.id, 
                              drink.name AS drink_name, 
                              CONCAT(drink_relationship.price, ',-') AS price, 
                              CONCAT(drink_relationship.student_price, ',-') AS student_price, 
                              CONCAT(drink_relationship.size, 'l') AS volume, 
                              drink_relationship.menu AS menu,
                              drink_type.name AS drink_type,
                              drink_type.id AS drink_type_id
                       FROM drink_relationship 
                       LEFT JOIN drink ON drink_relationship.drink_id=drink.id 
                       LEFT JOIN drink_type ON drink.drink_type_id=drink_type.id 
                       WHERE drink_relationship.bar_id=$barID
                       ORDER BY drink_type, menu, drink_name";
        $result_drinks = ($conn->query($sql_drinks));

        # Load pictures of bar
        $sql_pictures = "SELECT id, bar_id, path
                         FROM picture 
                         WHERE bar_id=$barID";
        $result_pictures = ($conn->query($sql_pictures));

        # Store the pictures about the bar
        $info['pictures'] = [];
        while ($picture = $result_pictures->fetch_assoc()) {
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
    } else {
        # If bar with passed bar_id does not exist, forward to error page
        header("Location: 404.php");
        die;
    }

    $conn->close();
}
?>
<div class="welcome">
    <?php include('header.php'); ?>
    <div class="mainEdit">
        <div>
            <h1>Edit Bar</h1>
            <img src="../media/icons/exit_white.png" alt="cancel" class="close" id="close">
            <form id="editBar">
                <table class="aboutBar">
                    <tr>
                        <td><label for="name">Name:</label></td>
                        <td><input type="text" id="name" name="name" value="<?php echo($info["name"]); ?>" placeholder="Enter bar name"></td>
                    </tr>
                    <tr>
                        <td><label for="location">Location:</label></td>
                        <td><input type="text" id="address" name="location" value="<?php echo($info["location"]); ?>" placeholder="Enter address"></td>
                    </tr>
                    <tr>
                        <td><label for="website">Website:</label></td>
                        <td><input type="text" id="website" name="website" value="<?php echo($info["website"]); ?>" placeholder="Enter website"></td>
                    </tr>
                    <tr>
                        <td><label for="phone">Phone:</label></td>
                        <td><input type="text" id="phone" name="phone" value="<?php echo($info["phone"]); ?>" placeholder="Enter phone"></td>
                    </tr>
                    <tr>
                        <td><label for="description">Description:</label></td>
                        <td><textarea name="description" rows="8" cols="80" id="description" form="editBar" placeholder="Enter description"><?php echo($info["description"]); ?></textarea></td>
                    </tr>
                    <tr>
                        <td><label for="tags">Tags:</label></td>
                        <td><input type="text" name="menu" value="" placeholder="Add new tag"><br></td>
                        <td><button type="button" class="add" name="submit">add</button></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <?php
                            while ($tag = $result_tags ->fetch_assoc()) {
                                printf("<button type='button' class='tag'>".$tag['tag_name']."</button>");
                            }
                            ?>
                        </td>
                    </tr>
                </table>
                <br>
                <!--<div class="aboutBar">
                    <label for="">Menu:</label>
                    <table id="existingDrinks">
                        <tr>
                            <th>Drink</th>
                            <th>Menu</th>
                            <th>Vol</th>
                            <th>Price</th>
                            <th></th>
                            <th></th>
                        </tr>
                        <tr class="drinkInp">
                            <td><input type="text" id="drink" name="drink" value="" placeholder="Drink"
                                       list="drinkList"></td>
                            <datalist id="drinkList">
                                <option value="Bulmers Berries & Lime">
                                <option value="Grevens Fruktsmak"></option>
                            </datalist>
                            <td><input type="text" id="menu" name="menu" value="" placeholder="Menu" list="menuList">
                            </td>
                            <datalist id="menuList">
                                <option value="Øl"></option>
                                <option value="Cider"></option>
                            </datalist>

                            <td><input type="text" id="vol" name="vol" value="" placeholder="Vol"></td>
                            <td><input type="text" id="price" name="price" value="" placeholder="Price"></td>
                            <td>
                                <button type="button" class="add">add</button>
                            </td>
                            <td></td>
                        </tr>
                        <?php
                        foreach ($result_menu as $drink) {
                            echo "<tr>";
                            echo "<td>" . $drink[drink_name] . "</td>";
                            echo "<td>" . $drink[menu_name] . "</td>";
                            echo "<td>" . $drink[size] . "</td>";
                            echo "<td>" . $drink[price] . "</td>";
                            echo "<td><button type='button' class='modify'>edit</button></td>";
                            echo "<td><button type='button' class='delete'>delete</button></td>";
                            echo "</tr>";
                        }
                        ?>
                    </table>
                </div>-->
            </form>
        </div>
        <br>
        <div class="saveBtn">
            <button type="submit" form="editBar">save and close</button>
        </div>
    </div>
</div>

</body>
</html>
