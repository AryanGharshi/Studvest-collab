<?php
define("MAGICKEY", "ugugUGu221KHJBD84");
require "../inc/connection/conn.php";

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

#Add drink to bar
if(isset($_POST['add_drink'])) {
    $drink = $_POST['drink'];
    $drink_type = $_POST['drink_type'];
    $menu = $_POST['menu'];
    $price = $_POST['price'];
    $student_price = $_POST['student-price'];
    $volume = $_POST['vol'];

    # Add new drink if it doesn't exist
    $sql = "INSERT INTO drink(name, drink_type_id)
            VALUES ('$drink', (SELECT id FROM drink_type WHERE name='$drink_type'))
            ON DUPLICATE KEY UPDATE id=id";
    $conn->query($sql);

    # If drink was modified, unassign old drink from bar before adding the new one.
    if($_POST['add_drink']!="new") {
        $sql = "DELETE FROM drink_relationship WHERE drink_id=" .$_POST['add_drink']. " AND menu=$menu AND bar_id=$barID;";
        $conn->query($sql);
    }

    # Assign new drink to bar
    $sql = "INSERT INTO drink_relationship(drink_id, bar_id, menu, price, student_price, size)
                        VALUES((SELECT id FROM drink WHERE name='$drink'), '$barID', '$menu', '$price', '$student_price', '$volume')
                        ON DUPLICATE KEY UPDATE menu='$menu', price=$price, student_price=$student_price, size=$volume;";
    $conn->query($sql);

    # Jump back into input form
    echo '<script>
                  window.onload = function() {
                       document.getElementById("add-drink").focus();
                  }
              </script>';
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

            # Load list of all drink types (not only the current bar)
            $sql_all_drink_types = "SELECT drink_type.name, id FROM drink_type;";
            $result_all_drink_types = ($conn->query($sql_all_drink_types));

            # Load list of all existing menus for this bar
            $sql_all_menus = "SELECT DISTINCT drink_relationship.menu AS name
                              FROM drink_relationship
                              LEFT JOIN drink ON drink_relationship.drink_id=drink.id
                              LEFT JOIN drink_type ON drink.drink_type_id=drink_type.id
                              WHERE drink_relationship.bar_id=$barID
                              ORDER BY menu";
            $result_all_menus = ($conn->query($sql_all_menus));

            # Load list of all drinks (not only the current bar)
            $sql_all_drinks = "SELECT drink.id AS drink,
                                      drink.name AS name,
                                      drink_type.name AS drink_type
                               FROM drink
                               LEFT JOIN drink_type ON drink.drink_type_id=drink_type.id;";
            $result_all_drinks = ($conn->query($sql_all_drinks));

            # Load list of drinks
            $sql_drinks = "SELECT drink.id AS id,
                              drink.name AS drink_name,
                              CONCAT(drink_relationship.price, ',-') AS price,
                              CONCAT(drink_relationship.student_price, ',-') AS student_price,
                              CONCAT(drink_relationship.size, ' ml') AS volume,
                              drink_relationship.menu AS menu,
                              drink_type.name AS drink_type,
                              drink_type.id AS drink_type_id
                       FROM drink_relationship
                       LEFT JOIN drink ON drink_relationship.drink_id=drink.id
                       LEFT JOIN drink_type ON drink.drink_type_id=drink_type.id
                       WHERE drink_relationship.bar_id=$barID
                       ORDER BY drink_name";
            $result_drinks = ($conn->query($sql_drinks));
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
    <title><?php echo (isset($info["name"])) ? $info["name"] : "Create bar";?></title>
    <link rel='icon' href='../media/favicons/studvest.png' type='image/x-icon'/ >
    <link rel="stylesheet" href="../css/main.css?version=<?= time() ?>">
    <link rel="stylesheet" href="../css/inputForm.css?version=<?= time() ?>">
</head>

<body id="body-add">

<a href="inputForm.php">
    <?php include('header.php'); ?>
</a>

<div id="main">
    <div id="left-column">
        <h1>General Information</h1>
        <form id="editBar" method="post" enctype="multipart/form-data">
            <input type="hidden" name="barID" value=<?php echo($barID); ?>>
            <table class="aboutBar">
                <tr>
                    <td><label for="name">Name:</label></td>
                    <td><input type="text" id="name" name="name" value="<?php echo($info["name"]); ?>" placeholder="Enter bar name" required></td>
                </tr>
                <tr>
                    <td><label for="location">Location:</label></td>
                    <td><input type="text" id="address" name="location" value="<?php echo($info["location"]); ?>" placeholder="Enter address" required></td>
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
                    <td><textarea name="description" rows="8" cols="80" id="description" form="editBar" placeholder="Enter description" required><?php echo($info["description"]); ?></textarea></td>
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
                                printf("<button type='submit' class='tag' name='remove_tag' value='" . $tag['tag_id'] ."'>" . $tag['tag_name'] .  "  &#10006;</button>");
                            }
                        }
    echo '
                    </td>
                </tr>
                <tr>
                    <td><label for="images">Images:</label></td>
                    <td><input type="file" name="uploaddatei" size="60" maxlength="255"><br></td>
                    <td><button type="submit" class="add" name="add_image" value="submit" formaction="">upload</button></td>
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
                </tr>'
                ?>
            </table>
    </div>

    <h1 id="drink-menu-title">Drink Menu</h1>
    <button type="submit" class="btn-nav" id="btn-nav-save" name="update_bar" value="submit" formaction="inputForm.php">Save & close</button>
    </form>
    <div id="right-column" class="list">
        <div style="float: left;  display: inline; height: 40px">
        <form id="editBar" method="post" enctype="multipart/form-data">
            <input type="hidden" name="barID" value=<?php echo($barID); ?>>
            <?php
    echo '   <datalist id="drinkList">';
    foreach ($result_all_drinks as $drink) {
        echo '<option value="'. $drink['name'] . '">';
        $mapping_drink_drinkType[$drink['name']] = $drink['drink_type'];
    }
    echo    '</datalist>';
    echo    '<datalist id="menuList">';
    foreach ($result_all_menus as $menu) { echo '<option value="'. $menu['name'] . '">'; }
    echo    '</datalist>';

    echo '
             <table id="drink-menu-table">
                  <tr>
                      <th>Drink</th>
                      <th>Main Menu</th>
                      <th>Sub menu</th>
                      <th>Volume</th>
                      <th>Normal Price</th>
                      <th>Student Price</th>
                      <th></th>
                      <th></th>
                  </tr>
                  <tr class="drinkInp">
                      <td class="td-drink"><input type="text" id="add-drink" name="drink" placeholder="Drink" list="drinkList" required></td>
                      <td class="td-drink-type">
                          <select id="add-type" name="drink_type" required>
                              <option disabled selected value></option>';
    foreach ($result_all_drink_types as $drink_type) {
        $i = (isset($i) ? $i+1 : 1);
        echo '<option value="'. $drink_type['name'] . '">' . $drink_type['name']  . '</option>';
        $mapping_drinkType_selectIdx[$drink_type['name']] = $i;
    }
    echo '                  </select>
                        </td>
                        <td class="td-menu"><input type="text" id="add-menu" name="menu" list="menuList"></td>
                        <td class="td-vol"><input type="number" id="add-vol" name="vol" placeholder="ml" min=2 step=1" required></td>
                        <td class="td-price"><input type="number" id="add-price" name="price" value="" placeholder="in kr" min=10 step=1" required></td>
                        <td class="td-price"><input type="number" id="add-student-price" name="student-price" value="" placeholder="in kr" min=10 step=1"></td>
                        <td class="td-submit"><button type="submit" id="add-submit" name="add_drink" class="add" value="new" formaction="">add</button></td>
                        <td></td>
                    </tr>';

    foreach ($result_drinks as $drink) {
        $id = $drink['id'];
        echo "      <tr>
                        <td id='drink-$id-name' class='td-drink'>" . $drink['drink_name'] . "</td>
                        <td id='drink-$id-type' class='td-drink-type'>" . $drink['drink_type'] . "</td>
                        <td id='drink-$id-menu' class='td-menu'>" . $drink['menu'] . "</td>
                        <td id='drink-$id-volume' class='td-volume'>" . $drink['volume'] . "</td>
                        <td id='drink-$id-price' class='td-price'>" . $drink['price'] . "</td>
                        <td id='drink-$id-student-price' class='td-price'>" . $drink['student_price'] . "</td>
                        <td id='drink-$id-modify' class='td-submit'><button type='button' class='modify' onclick ='modify($id)'>modify</button></td>
                        <td id='drink-$id-delete' class='td-submit'><button type='submit' class='delete' name='delete_drink' value=$id>delete</button></td>
                    </tr>";
    }
    echo '      </table>';
}

else {
    echo '  <tr><td></td><td>
            <button type="submit" class="btn-nav" name="create_bar" value="submit" formaction="">Create bar</button></td></table>';
}
?>
        </form>
        </div>
    </div>

    <div id="popup_add">
        <h1>This website have form with long menu items,<br> please use laptop to fill in information</h1>
        <input type="image" src="../media/icons/exit_white.png" alt="submit" class="btn-close">
    </div>

</div>






    <script>
        let mapping_drink_drinkType = <?php echo json_encode($mapping_drink_drinkType, JSON_HEX_TAG); ?>; // Don't forget the extra semicolon!
        let mapping_drinkType_selectIdx = <?php echo json_encode($mapping_drinkType_selectIdx, JSON_HEX_TAG); ?>; // Don't forget the extra semicolon!
    </script>
    <script type='text/javascript' src='../js/inputForm.js?version=<?= time() ?>'> </script>


</body>
</html>
