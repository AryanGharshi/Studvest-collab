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
define("MAGICKEY", "ugugUGu221KHJBD84");
require "../inc/connection/conn.php";

function console_log( $data ){
    echo '<script>';
    echo 'console.log('. json_encode( $data ) .')';
    echo '</script>';
}

# Retrieve the barID if available
if(isset($_POST['barID'])) {
    $barID = $_POST['barID'];
}

# Add new bar entry into the database
if(isset($_POST['create_bar'])) {

    # Load information from the input form
    $name = $_POST['name'];
    $description = $_POST['description'];
    $website = $_POST['website'];
    $phone = $_POST['phone'];
    $location = $_POST['location'];

    # Add bar into the database
    $sql = "INSERT INTO bar (name, description, website, phone, location)
            VALUES ('$name', '$description', '$website', $phone, '$location')";
    $conn->query($sql);
    $barID = $conn->insert_id;
}

# Add tags
if(isset($_POST['add_tag'])) {

    # Create new tag in the database if necessary
    $tag = $_POST['tag'];
    $sql = "INSERT INTO tag(name)
                         VALUES('$tag')
                         ON DUPLICATE KEY UPDATE id=id;";
    $conn->query($sql);

    # Assign tag to the bar
    $sql = "INSERT INTO tag_relationship(bar_id, tag_id)
                         VALUES($barID, (SELECT id FROM tag WHERE name='$tag'))
                         ON DUPLICATE KEY UPDATE bar_id=bar_id;";
    $conn->query($sql);
}

# Remove tag
if(isset($_POST['remove_tag'])) {
    $tagID = $_POST['remove_tag'];
    $sql = "DELETE FROM tag_relationship 
            WHERE bar_id=$barID AND tag_id=$tagID;";
    $conn->query($sql);
}

# Load the bar information, if there a barID is defined
if (isset($barID)) {

    # Load bar infos
    $sql_barInfos = "SELECT bar.name AS barname, bar.description, bar.website, bar.phone, bar.location   
                     FROM bar  
                     WHERE bar.id=$barID";
    $result_barInfos = ($conn->query($sql_barInfos));

    # Check if the bar ID is valid and the bar actually exists in the databse
    if ($result_barInfos->num_rows > 0) {

        # If the page got reloaded because of an added/removed tag, load all bar information from the $_POST variables
        if(isset($_POST['add_tag']) || isset($_POST['remove_tag'])){
            $info["name"] = $_POST["name"];
            $info["description"] = $_POST["description"];
            $info["website"] = $_POST["website"];
            $info["phone"] = $_POST["phone"];
            $info["location"] = $_POST["location"];
        }

        # Else: Load all the information from the database
        else {
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
        }

        # Load and store the pictures of the bar
        $sql_pictures = "SELECT id, bar_id, path
                         FROM picture 
                         WHERE bar_id=$barID";
        $result_pictures = ($conn->query($sql_pictures));
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
    }
    else {
        # If bar with passed bar_id does not exist, forward to error page
        header("Location: 404.php");
        die;
    }
}
$conn->close();
?>
<div class="welcome">
    <?php include('header.php'); ?>
    <div class="mainEdit">
        <div>
            <h1>Edit Bar</h1>
            <form id="editBar" method="post">
                <input type="hidden" name="barID" value=<?php echo($barID); ?>>
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

<?php
if (isset($_POST['barID'])) {
    echo '
                    <tr>
                        <td><label for="tags">Tags:</label></td>
                        <td><input type="text" name="tag" value="" placeholder="Add new tag"><br></td>
                        <td><button type="submit" class="add" name="add_tag" value="submit" formaction="">add</button></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>';
                        if ($result_tags->num_rows > 0) {
                            while ($tag = $result_tags->fetch_assoc()) {
                                printf("<button type='submit' class='tag' name='remove_tag' value='" . $tag['tag_id'] ."'>" . $tag['tag_name'] .  " X</button>");
                            }
                        }
    echo '   
                        </td>
                    </tr>
                </table>
                <div class="saveBtn">
                    <button type="submit" name="update_bar" value="submit" formaction="inputForm.php">save & close</button>
                </div>';
}
else {
    echo '
                    <tr>
                        <td></td>
                        <td>
                        <div class="saveBtn">
                            <button type="submit" name="create_bar" value="submit" formaction="">create bar</button>
                        </div>
                        </td>
                    </tr>
                </table>';
}
?>
            </form>
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
                    </table>
                </div>-->
        </div>
        <br>
    </div>
</div>

</body>
</html>
