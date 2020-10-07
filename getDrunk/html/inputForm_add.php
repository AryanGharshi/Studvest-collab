

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

# Create a new bar into the database
if(isset($_POST['create_bar'])) {

    echo "new bar";

    # Load information from the input form
    $name = $_POST['name'];
    $description = $_POST['description'];
    $website = $_POST['website'];
    $phone = $_POST['phone'];
    if ($phone=='') {$phone='Null';}
    $location = $_POST['location'];

    # Add bar into the database
    $sql = "INSERT INTO bar (name, description, website, phone, location)
            VALUES ('$name', '$description', '$website', $phone, '$location')";
    echo $sql;
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

#Add drink to bar
if(isset($_POST['add_drink'])) {
    $drink = $_POST['drink'];
    $drink_type = $_POST['menu'];
    $price = $_POST['price'];
    $volume = $_POST['vol'];


    # Add new drink type to drink_type table
    $sql = "INSERT INTO drink_type(name)
                        VALUES('$drink_type')
                        ON DUPLICATE KEY UPDATE id=id;";
    $conn->query($sql);

    # Connect drink name to drink_type ID
    $sql = "INSERT INTO drink(id, name, drink_type_id)
                        VALUES ((SELECT id FROM drink WHERE name='$drink'), '$drink', (SELECT id FROM drink_type WHERE name='$drink_type'))
                        ON DUPLICATE KEY UPDATE id=id;";
    $conn->query($sql);

    # Connect drink table to drink_relationship table
    $sql = "INSERT INTO drink_relationship(drink_id, bar_id, drink_type, price, size)
                        VALUES((SELECT id FROM drink WHERE name='$drink'), '$barID', '$drink_type', '$price', '$volume');";
                        echo $sql;
    $conn->query($sql);
}

#Remove drink
if(isset($_POST['delete_drink'])) {
  $drinkID = $_POST['delete_drink'];
  $sql = "DELETE FROM drink_relationship
          WHERE drink_id=$drinkID AND bar_id=$barID;";
  $conn->query($sql);
}


# Remove tag
if(isset($_POST['remove_tag'])) {
    $tagID = $_POST['remove_tag'];
    $sql = "DELETE FROM tag_relationship
            WHERE bar_id=$barID AND tag_id=$tagID;";
    $conn->query($sql);
}

# Add images
if(isset($_POST['add_image']) and $_FILES['uploaddatei']['name'] <> "") {
    #Create directory if not exists
    $target_directory = "../media/pictures/$barID/";
    if (!is_dir($target_directory)) {
        mkdir($target_directory);
    }
    $target_file = $target_directory . $_FILES['uploaddatei']['name'];
    # Store file on the server
    move_uploaded_file ($_FILES['uploaddatei']['tmp_name'] , $target_file);
    # Update the database
    $sql = "INSERT INTO picture(bar_id, path)
                         VALUES($barID, '$target_file')
                         ON DUPLICATE KEY UPDATE bar_id=bar_id;";
    $conn->query($sql);
}

# Remove image
if(isset($_POST['remove_image'])) {
    $target_file = $_POST['remove_image'];
    # Remove file from server
    unlink($target_file);
    # Update the database
    $sql = "DELETE FROM picture
            WHERE bar_id=$barID AND path='$target_file'";
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

            # Load list of all drink types
            $sql_all_drink_types = "SELECT drink_type.name, id FROM drink_type;";
            $result_all_drink_types = ($conn->query($sql_all_drink_types));

            # Load list of all drinks
            $sql_all_drinks = "SELECT id, name FROM drink;";
            $result_all_drinks = ($conn->query($sql_all_drinks));

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
            echo $sql_drinks;
        }

        # Load and store the pictures of the bar
        $sql_pictures = "SELECT id, bar_id, path
                         FROM picture
                         WHERE bar_id=$barID";
        $result_pictures = ($conn->query($sql_pictures));

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
        echo "error";
        # If bar with passed bar_id does not exist, forward to error page
        #header("Location: 404.php");
        #die;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<head>
    <meta charset="UTF-8">
    <title><?php if (isset($info["name"])) {
      echo $info["name"];
    } else {
      echo "Edit bar";
    } ?></title>
    <link rel="stylesheet" href="../css/inputForm.css?version=<?= time() ?>">
    <link rel="stylesheet" href="../css/main.css?version=<?= time() ?>">
</head>

<?php include('header.php'); ?>
<div class="welcome">
    <div class="mainEdit">
        <div class="edit">
            <h1>Edit Bar</h1>

            <form id="editBar" method="post" enctype="multipart/form-data">
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
                        <td><input type="number" id="phone" name="phone" value="<?php echo($info["phone"]); ?>" placeholder="Enter phone"></td>
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
                        <td><button type="submit" class="add" name="add_tag" value="submit" formaction="">Add tag</button></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>';
                        if ($result_tags->num_rows > 0) {
                            while ($tag = $result_tags->fetch_assoc()) {
                                printf("<button type='submit' class='tag' name='remove_tag' value='" . $tag['tag_id'] ."'>" . $tag['tag_name'] .  "  &#10006;</button>");
                            }
                        }
    echo '
                        </td>
                    </tr>
                    <tr>
                        <td><label for="images">Images:</label></td>
                        <td><input type="file" name="uploaddatei" size="60" maxlength="255"><br></td>
                        <td><button type="submit" class="add" name="add_image" value="submit" formaction="">Upload picture</button></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>';
                        if ($result_pictures->num_rows > 0) {
                            while ($picture = $result_pictures->fetch_assoc()) {
                                printf("<button type='submit' class='tag' name='remove_image' value='" . $picture['path'] ."'>" . basename($picture['path']) .  "  &#10006;</button>");
                            }
                        }
    echo '
                        </td>
                    </tr>
                </table>

                <table id="existingDrinks" class="aboutBar">
                    <tr>
                        <th>Drink</th>
                        <th>Menu</th>
                        <th>Volume (in ml)</th>
                        <th>Price (in kr)</th>
                        <th></th>
                        <th></th>
                    </tr>
                    <tr class="drinkInp">
                        <td>
                            <input type="text" id="drink" name="drink" placeholder="Drink" list="drinkList">
                        </td>
                        <datalist id="drinkList">';
    foreach ($result_all_drinks as $drink) {
       echo '               <option value="'. $drink['name'] . '">';
    }

    echo '              </datalist>
                        <td>
                            <input type="text" id="menu" name="menu" value="" placeholder="Menu" list="menuList">
                        </td>
                        <datalist id="menuList">';
    foreach ($result_all_drink_types as $drink_type) {
        echo '               <option value="'. $drink_type['name'] . '">';
    }

    echo '              </datalist>
                        <td><input type="number" id="vol" name="vol" placeholder="ml" min=2 step=1"></td>
                        <td><input type="number" id="price" name="price" value="" placeholder="in kr" min=10 step=1"></td>
                        <td><button type="submit" class="add" name="add_drink" value="submit" formaction="">Add drink</button></td>
                        <td></td>
                    </tr>';
    foreach ($result_drinks as $drink) {
        echo "      <tr>
                        <td>" . $drink['drink_name'] . "</td>
                        <td>" . $drink['drink_type'] . "</td>
                        <td>" . $drink['volume'] . "</td>
                        <td>" . $drink['price'] . "</td>
                        <td><button type='button' class='modify'>edit</button></td>
                        <td><button type='button' class='delete' name='delete_drink'>delete</button></td>
                    </tr>";
    }

    echo '      </table>
                <div class="saveBtn">
                    <button type="submit" name="update_bar" value="submit" formaction="inputForm.php">Save & close</button>
                </div>';
}

else {
    echo '
                    <tr>
                        <td></td>
                        <td>
                        <div class="saveBtn">
                            <button type="submit" name="create_bar" value="submit" formaction="">Create bar</button>
                        </div>
                        </td>
                    </tr>
                </table>';
}
?>
            </form>
                <!--<div class="aboutBar">
                    <label for="">Menu:</label>

                </div>-->
        </div>
    </div>
</div>

</body>
</html>
