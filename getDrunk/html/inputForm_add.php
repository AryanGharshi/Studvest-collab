<?php
define("MAGICKEY", "ugugUGu221KHJBD84");
require "../inc/connection/conn.php";

session_start();


if(!isset($_SESSION['username'])|| time() - $_SESSION['login_time'] > 1800) {
  session_unset();
  session_destroy();
  echo "<script> alert('Your session has expired. Please log in again.');
  window.location.href='login.php';
  </script>";

} else {
  echo "<br><a href='logout.php'><input type=button class=logout value=logout name=logout></a>";

}

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

    # Load information from the input form
    $name = $_POST['name'];
    $description = $_POST['description'];
    $website = $_POST['website'];
    $phone = $_POST['phone'];
    if ($phone=='') {$phone='Null';}
    $location = $_POST['location'];

    # Add bar into the database
    $sql = "INSERT IGNORE INTO bar (name, description, website, phone, location)
            VALUES ('$name', '$description', '$website', $phone, '$location')";
    $conn->query($sql);
    $barID = $conn->insert_id;

    # Check if duplicate bar name
    if ($barID==0) {
        $barID = null;
        $error_message = "Another bar with the name you have entered, already exists. Please enter a different name.";
    }

    console_log("New bar was added to the database.");
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

    console_log("Tag got assigned");
}

#Remove drink
if(isset($_POST['delete_drink'])) {
  $drinkRelationshipID = $_POST['delete_drink'];
  $sql = "DELETE FROM drink_relationship
          WHERE drink_relationship.id=$drinkRelationshipID";
  $conn->query($sql);


    console_log("Drink got removed");
}

# Remove tag
if(isset($_POST['remove_tag'])) {
    $tagID = $_POST['remove_tag'];
    $sql = "DELETE FROM tag_relationship
            WHERE bar_id=$barID AND tag_id=$tagID;";
    $conn->query($sql);

    console_log("Tag got removed");
}

# Add images
$uploadfile = $_FILES['uploadfile'."-".$_POST['add_image']];
if(isset($_POST['add_image']) and ($uploadfile['name'] <> "")) {
    $is_cover = $_POST['add_image']=='cover';

    #Create directory if not exists
    $target_directory = "../media/pictures/$barID/$is_cover/";
    if (!is_dir($target_directory)) {
        mkdir($target_directory);
    }
    $target_file = $target_directory . $uploadfile['name'];
    # Store file on the server
    move_uploaded_file ($uploadfile['tmp_name'] , $target_file);
    # Update the database
    $sql = "INSERT INTO picture(bar_id, path, is_cover)
                         VALUES($barID, '$target_file', '$is_cover')
                         ON DUPLICATE KEY UPDATE bar_id=bar_id;";
    $res = $conn->query($sql);

    # Delete previous cover photo, if new cover photo was uploaded
    if($is_cover and isset($_POST['cover-path'])) {
        $_POST['remove_image'] = $_POST['cover-path'];
    }
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

    console_log("Image got removed.");
}

#Add drink to bar
if(isset($_POST['add_drink'])) {
    $drink_relationship_id=$_POST['add_drink'];
    $drink_name = $_POST['drink'];
    $drink_type = $_POST['drink_type'];
    $menu = $_POST['menu'];
    $price = $_POST['price'];
    $student_price = $_POST['student_price'];
    console_log($student_price);
    if ($student_price=='') {$student_price='Null';}
    console_log($student_price);
    $volume = $_POST['vol'];

    # Add new drink if it doesn't exist
    $sql = "INSERT INTO drink(name, drink_type_id)
            VALUES ('$drink_name', (SELECT id FROM drink_type WHERE name='$drink_type'))
            ON DUPLICATE KEY UPDATE id=id";
    $conn->query($sql);
    console_log($sql);

    # If drink was modified, unassign old drink from bar before adding the new one.
    if($_POST['add_drink']!="new") {
        $sql = "DELETE FROM drink_relationship WHERE drink_relationship.id=$drink_relationship_id;";
        $conn->query($sql);
        console_log($sql);
    }

    # Assign new drink to bar
    $sql = "INSERT INTO drink_relationship(drink_id, bar_id, menu, price, student_price, size)
                        VALUES((SELECT id FROM drink WHERE name='$drink_name'), '$barID', '$menu', '$price', $student_price, '$volume')
                        ON DUPLICATE KEY UPDATE menu='$menu', price=$price, student_price=$student_price, size=$volume;";
    $conn->query($sql);
    console_log($sql);

    # Jump back into input form
    echo '<script>
                  window.onload = function() {
                       document.getElementById("add-drink").focus();
                  }
              </script>';

    console_log("Drink got added");
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
            $sql_all_drink_types = "SELECT drink_type.name, id FROM drink_type ORDER BY drink_type.rank;";
            $result_all_drink_types = ($conn->query($sql_all_drink_types));

            # Load list of all existing menus for this bar
            $sql_all_menus = "SELECT DISTINCT drink_relationship.menu AS name
                              FROM drink_relationship
                              LEFT JOIN drink ON drink_relationship.drink_id=drink.id
                              LEFT JOIN drink_type ON drink.drink_type_id=drink_type.id
                              WHERE drink_relationship.bar_id=$barID
                              ORDER BY menu";
            console_log($sql_all_menus);
            $result_all_menus = ($conn->query($sql_all_menus));

            # Load list of all drinks (not only the current bar)
            $sql_all_drinks = "SELECT drink.id AS drink,
                                      drink.name AS name,
                                      drink_type.name AS drink_type,
                                      drink_type.rank AS drink_type_rank
                               FROM drink
                               LEFT JOIN drink_type ON drink.drink_type_id=drink_type.id;";
            $result_all_drinks = ($conn->query($sql_all_drinks));
            console_log($sql_all_drinks);

            # Load list of drinks
            $sql_drinks = "SELECT drink.id AS id,
                              drink.name AS drink_name,
                              CONCAT(drink_relationship.price) AS price,
                              CONCAT(drink_relationship.student_price) AS student_price,
                              CONCAT(drink_relationship.size) AS volume,
                              drink_relationship.menu AS menu,
                              drink_type.name AS drink_type,
                              drink_type.id AS drink_type_id,
                              drink_relationship.id AS drink_relationship_id
                       FROM drink_relationship
                       LEFT JOIN drink ON drink_relationship.drink_id=drink.id
                       LEFT JOIN drink_type ON drink.drink_type_id=drink_type.id
                       WHERE drink_relationship.bar_id=$barID
                       ORDER BY drink_name";
            $result_drinks = ($conn->query($sql_drinks));
            console_log($sql_drinks);
        }

        # Load and store the pictures of the bar
        $sql_pictures = "SELECT id, bar_id, path, is_cover
                         FROM picture
                         WHERE bar_id=$barID
                         ORDER BY is_cover DESC";
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
        console_log("ERROR: No bar with that ID exists");
    }

    console_log("Bar data was loaded successfully");
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<head>
    <meta charset="UTF-8">
    <title>Studout - Administration</title>
    <link rel='icon' href='../media/favicons/studvest.png' type='image/x-icon'/ >
    <link rel="stylesheet" href="../css/main.css?version=<?= time() ?>">
    <link rel="stylesheet" href="../css/inputForm.css?version=<?= time() ?>">
</head>

<body id="body-add">

<a href="inputForm.php">
    <?php include('header.php'); ?>
</a>

<div id="main" class="main">
    <div id="left-column">
        <h1>General Information</h1>
        <?php
            if($error_message != null) {
                echo $error_message;
            }
        ?>
        <form id="editBar" method="post" enctype="multipart/form-data">
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
if (isset($barID)) {
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
            printf("<button type='submit' class='tag' name='remove_tag' value='" . $tag['tag_id'] . "'>" . $tag['tag_name'] . "  &#10006;</button>");
        }
    }
    echo '
                    </td>
                </tr>
                <tr>
                    <td><label for="cover">Cover photo:</label></td>
                    <td><input type="file" name="uploadfile-cover" size="60" maxlength="255"><br></td>
                    <td><button type="submit" class="add" name="add_image" value="cover" formaction="">upload</button></td>
                </tr>
                <tr>
                    <td></td>
                    <td>';
    if ($result_pictures->num_rows > 0) {
        $picture = $result_pictures->fetch_assoc();
        if($picture['is_cover']) {
            printf("<input type='hidden' name='cover-path' value='" . $picture['path'] . "'>
                    <button type='submit' class='tag' name='remove_image' value='" . $picture['path'] . "'>" . basename($picture['path']) . "  &#10006;</button>");
        }
    }
    echo '
                    </td>
                </tr>
                <tr>
                    <td><label for="images">More pictures:</label></td>
                    <td><input type="file" name="uploadfile-normal" size="60" maxlength="255"><br></td>
                    <td><button type="submit" class="add" name="add_image" value="normal" formaction="">upload</button></td>
                </tr>
                <tr>
                    <td></td>
                    <td>';
    if($picture['is_cover'] == 0) {
        printf("<button type='submit' class='tag' name='remove_image' value='" . $picture['path'] . "'>" . basename($picture['path']) . "  &#10006;</button>");
    }
    if ($result_pictures->num_rows > 0) {
        while ($picture = $result_pictures->fetch_assoc()) {
            printf("<button type='submit' class='tag' name='remove_image' value='" . $picture['path'] . "'>" . basename($picture['path']) . "  &#10006;</button>");
        }
    }
    echo '
                    </td>
                </tr>';
}
else {
    echo '      <tr>
                    <td></td>
                    <td><button type="submit" class="btn-nav" name="create_bar" value="submit" formaction="">Create bar</button></td>
                </tr>';
}
?>
            </table>
        </div>


<?php
if (isset($barID)) {
    echo '
        <div id="right-column">
            <h1 id="drink-menu-title">Drink Menu</h1>
            <button type="submit" class="btn-nav" id="btn-nav-save" name="update_bar" value="submit" formaction="inputForm.php">Save & close</button>
    </form>';

    // Generate datalists
    $datalist_drinkList = '<datalist id="drinkList">';
    foreach ($result_all_drinks as $drink) {
        $datalist_drinkList .= '<option value="'. $drink['name'] . '">';
        $mapping_drink_drinkType[$drink['name']] = $drink['drink_type_rank'];
    }
    $datalist_drinkList .= '</datalist>';

    $datalist_menuList = '<datalist id="menuList">';
    foreach ($result_all_menus as $menu) { $datalist_menuList .= '<option value="'. $menu['name'] . '">'; }
    $datalist_menuList .= '</datalist>';

    $datalist_drinkTypeList = "<option disabled value></option>";
    foreach ($result_all_drink_types as $drink_type) { $datalist_drinkTypeList .= "<option value='" . $drink_type['name'] . "'>" . $drink_type['name']  . "</option>"; };

    $columns = json_encode(['drink-name', 'drink-type', 'drink-menu', 'drink-volume', 'drink-price', 'drink-student-price']);

    echo "
        <div class='drink-menu list'>
            <form id='editBar' method='post' enctype='multipart/form-data'>
                <input type='hidden' name='barID' value=$barID>
                $datalist_drinkList
                $datalist_menuList
                <table class='drink-menu-table'>
                    <tr>
                        <th class ='td-drink' style='padding-rigt:3.5em;'>Drink</th>
                        <th class ='td-drink_type' >Main Menu</th>
                        <th class ='td-menu' >Sub menu</th>
                        <th class ='td-vol'>Volume</th>
                        <th class ='td-normal'>Normal Price</th>
                        <th class ='td-student'>Student Price</th>
                        <th class ='td-submit'></th>
                        <th class ='td-submit'></th>
                    </tr>
                    <tr id='row0'>
                        <td class='td-drink'><input type='text' id='drink-name0' class='drink-name' name='drink' placeholder='Drink' list='drinkList' required></td>
                        <td class='td-drink-type'><select id='drink-type0' class='drink-type' name='drink_type' required>". str_replace("<option disabled value>", "<option disabled value selected>", $datalist_drinkTypeList). "</select></td>
                        <td class='td-menu'><input type='text' id='drink-menu0' name='menu' list='menuList'></td>
                        <td class='td-vol'><input type='number' id='drink-volume0' name='vol' min=2 step=1' required><span class='unit unit-active'>ml</span></td>
                        <td class='td-price'><input type='number' id='drink-price0'  name='price' value='' min=10 step=1' required><span class='unit unit-active'>,-</span></td>
                        <td class='td-price'><input type='number' id='drink-student-price0'  name='student_price' value='' min=10 step=1 ><span class='unit unit-active'>,-</span></td>
                        <td class='td-submit'><button type='submit' id='add0' name='add_drink' class='add' value='new' formaction=''>add</button></td>
                        <td class='td-submit'><button type='submit' id='clear0' name='add_drink' class='transparent' value='new' formaction=''>clear</button></td>
                    </tr>
                </table>
            </form>
        </div>

        <div class='drink-menu list'>
            <form id='editBar' method='post' enctype='multipart/form-data'>
                <input type='hidden' name='barID' value=$barID>
                <table class='drink-menu-table'>
                $datalist_drinkList
                $datalist_menuList";

    foreach ($result_drinks as $drink) {
        $id = $drink['id'];
        $drink_relationship_id = $drink["drink_relationship_id"];
        $delete_parameters = array("drink_relationship_id" => $drink["drink_relationship_id"], "section" => "drink");
        $columns_inactive = json_encode(['drink-type']);
        echo "      <tr id='row$drink_relationship_id'>
                        <td id='drink-$drink_relationship_id-name' class='td-drink'>
                            <input type='text' class='input-drink' id='drink-name$drink_relationship_id' class='drink-name' name='drink' value='" . $drink['drink_name'] . "' list='drinkList' required disabled>
                        </td>
                        <td id='drink-$drink_relationship_id-type' class='td-drink-type'>
                            <select class='input-type' id='drink-type$drink_relationship_id' class='drink-type' name='drink_type' required disabled>".str_replace('>' . $drink['drink_type'], 'selected >' . $drink['drink_type'], $datalist_drinkTypeList)."</select>
                        </td>
                        <td id='drink-$drink_relationship_id-menu' class='td-menu'>
                            <input type='text' class='input-menu' id='drink-menu$drink_relationship_id'  name='menu' value='" . $drink['menu'] . "' list='menuList' disabled>
                        </td>
                        <td id='drink-$drink_relationship_id-volume' class='td-vol'>
                            <input type='number' class='input-vol' id='drink-volume$drink_relationship_id' name='vol' value='" . $drink['volume'] ."' min=2 step=1 required disabled>
                            <span class='unit'>ml</span>
                        </td>
                        <td id='drink-$drink_relationship_id-price' class='td-price'>
                            <input type='number' class='input-price' id='drink-price$drink_relationship_id' name='price' value='" . $drink['price'] . "' min=10 step=1 required disabled>
                            <span class='unit'>,-</span>
                        </td>
                        <td id='drink-$drink_relationship_id-student-price' class='td-price'>
                            <input type='number' class='input-price' id='drink-student-price$drink_relationship_id' name='student_price' value='" . $drink['student_price'] . "' min=10 step=1 disabled>
                            <span class='unit'>,-</span>
                        </td>
                        <td class='td-submit'>
                            <button id='mod$drink_relationship_id' type='button' class='modify' onclick ='req_modify(\"right-column\", $drink_relationship_id, $columns, $columns_inactive)'>modify</button>
                            <button id='add$drink_relationship_id' type='submit' class='add' name='add_drink' value='$drink_relationship_id' formaction='' style='display: none'>save</button>
                        </td>
                        <td id='drink-$drink_relationship_id-delete' class='td-submit'><button type='button' class='delete' onclick ='req_delete($drink_relationship_id, \"drink_relationship\" , \"main\")'>delete</button></td>
                    </tr>";
    }
    echo "      </table>
            </form>
        </div>
    </div>";
}
?>

</div>

<div id="popup_resolution_warning" class="popup">
    <h1>Sorry, your resolution is too low.</h1>
    <p>This form contains tables that are more usable on a larger screen.</p>
</div>

<div id="popup_confirmation" class="popup">
    <h1>Are you sure?</h1>
    <p>This action cannot be undone. Do you really want to delete this item?.</p>
    <form action='' method='post'>
        <input type='hidden' id='confirm-section' name='section' value=''>
        <input type="hidden" name="barID" value=<?php echo($barID); ?>>
        <input type='hidden' id='popup_confirmation-source' value=''>
        <button type='submit' id='confirm-delete' class='delete' name='delete_drink' value=''>delete</button>
        <button type='button' id='confirm-keep' class='modify' onclick='close_popup("popup_confirmation", null)'>keep</button>
    </form>
</div>

<script>
    let mapping_drink_drinkType = <?php echo json_encode($mapping_drink_drinkType, JSON_HEX_TAG); ?>; // Don't forget the extra semicolon!
</script>
<script type='text/javascript' src='../js/inputForm.js?version=<?= time() ?>'> </script>


</body>
</html>
