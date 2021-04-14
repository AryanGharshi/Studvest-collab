<?php
define("MAGICKEY", "ugugUGu221KHJBD84");
require "../inc/connection/conn.php";

session_start();


if(!isset($_SESSION['username'])|| time() - $_SESSION['login_time'] > 5400) {
  session_unset();
  session_destroy();

  header("location: login.php");
  exit;
  echo "Session timed out";

} else {
  echo "<br><a href='logout.php'><input type='button' class='logout' value='Log out' name='logout'></a>";

}


function console_log( $data ){
    echo '<script>';
    echo 'console.log('. json_encode( $data ) .')';
    echo '</script>';
}
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

<body id="body-main">

<!-- SQL COMMANDS -->

<?php
    # Delete Drinks / Drink Types / Tags / Bars
    if(isset($_POST['del'])){
        $section = $_POST['section'];
        $id = (int) $_POST['del'];

        # When deleting a bar, also unlink all the images
        if($section=='bar') {
            $picture_directory = "../media/pictures/$id/";
            if(is_dir($picture_directory)) {
                $files = glob($picture_directory . '*', GLOB_MARK); //GLOB_MARK adds a slash to directories returned
                foreach ($files as $file) {
                    unlink($file);
                }
                rmdir($picture_directory);
            }
        }

        # When deleting a section, adjust the ranks
        if($section=='drink_type') {
            $sql = "UPDATE drink_type as dt1 JOIN (SELECT rank AS delete_rank FROM drink_type WHERE id=$id) as dt2 SET dt1.rank=dt1.rank-1 WHERE rank>dt2.delete_rank";
            $conn->query($sql);
        }

        # Delete the item
        $sql_del = "DELETE FROM $section WHERE id=$id";
        $conn->query($sql_del);
    }

    # Add Drinks / Drink Types / Tags
    if(isset($_POST['add'])){
        $section = $_POST['section'];
        $newName = $_POST['name'];
        $id = $_POST['add'];
        if($section == 'drink') {
            $sql = "UPDATE drink
                        SET name='$newName', drink_type_id=" . $_POST['drink_type'] . "
                        WHERE id=$id";
            $conn->query($sql);
        }
        elseif($section == 'drink_type') {
            $img_url_active = $_POST['img_url_active'];
            $img_url_inactive = $_POST['img_url_inactive'];
            $volume_unit = $_POST['volume_unit'];
            $sql = "INSERT INTO drink_type(id, name, volume_unit, rank, img_url_inactive, img_url_active)
                    SELECT '$id', '$newName', '$volume_unit', MAX(rank)+1, '$img_url_inactive', '$img_url_active' FROM drink_type
                    ON DUPLICATE KEY UPDATE volume_unit='$volume_unit', rank=rank, name='$newName', img_url_inactive='$img_url_inactive', img_url_active='$img_url_active';";
            $conn->query($sql);
            console_log($sql);
        }
        elseif($section == 'tag') {
            $sql = "UPDATE $section
                        SET $section.name='$newName'
                        WHERE $section.id=$id";
            $conn->query($sql);
        }
    }

    # Process POST-statements to delete/modify
    if(isset($_POST['update_bar'])){
        $name = $_POST['name'];
        $description = $_POST['description'];
        $website = $_POST['website'];
        $phone = $_POST['phone'];
        $location = $_POST['location'];
        $id = $_POST['barID'];
        $sql = "UPDATE bar
                SET name='$name',
                    description='$description',
                    website='$website',
                    phone='$phone',
                    location='$location'
                WHERE id=$id";
        $conn->query($sql);
    }

    # Process POST statements to change the rank of a drink type
    if(isset($_POST['change_rank'])){
        $direction = $_POST['change_rank'];
        $id = $_POST['current_rank'];

        if($direction=="down") {
            $sql = "UPDATE drink_type AS dt1 JOIN (select MIN(rank) AS rank_next FROM drink_type WHERE rank>'$id') AS dt2
                    SET dt1.rank = (case when (dt1.rank = '$id' and dt2.rank_next!=0) then dt2.rank_next else '$id' end)
                    WHERE dt1.rank in ('$id', dt2.rank_next)";
            console_log($sql);
            $conn->query($sql);
        }
        else {
            $sql = "UPDATE drink_type AS dt1 JOIN (select MAX(rank) AS rank_prev FROM drink_type WHERE rank<'$id') AS dt2
                    SET dt1.rank = (case when (dt1.rank = '$id' and dt2.rank_prev!=0) then dt2.rank_prev else '$id' end)
                    WHERE dt1.rank in ('$id', dt2.rank_prev)";
            console_log($sql);
            $conn->query($sql);
        }
    }

    # Load Data from the Database
    $sql_bar = "SELECT * FROM bar";
    $result_bar = ($conn->query($sql_bar));
    $sql_drink_type = "SELECT * FROM drink_type ORDER BY rank";
    $result_drink_types = ($conn->query($sql_drink_type));
    $options_all_drink_types='';
    foreach($result_drink_types as $drink_type) {
        $options_all_drink_types .= "<option value='" . $drink_type["id"] . "'>" . $drink_type["name"] . "</option>";
    }
    $result_drink_types = ($conn->query($sql_drink_type));
    $sql_tags = "SELECT * FROM tag";
    $result_tags = ($conn->query($sql_tags));
    $sql_drinks = "SELECT drink.id AS id, drink.name AS name, drink_type.name AS drink_type
                   FROM drink
                   INNER JOIN drink_type ON drink.drink_type_id = drink_type.id";
    $result_drinks = ($conn->query($sql_drinks));

    $options_all_volume_units = "<option value='l'>l</option>
                                 <option value='cl'>cl</option>
                                 <option value='ml'>ml</option>
                                 <option value='dl'>dl</option>";

    $conn->close();
?>

<!-- LOAD HEADER -->

    <a href="inputForm.php">
        <?php include('header.php'); ?>
    </a>


    <div id="main" class="main">
      <div id="mySidenav" class="sidenav">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <div class="sidenav_content">
          <h1>Instructions</h1>

          <h2>Create New Bar</h4>

          <h3>Location</h5>
          <ul class="a">
            <li>When creating a new bar a location from Google Maps can be linked by searching the bar address in Google Maps and then copying the link found under the button “Share” into the location field in “Create new bar”. </li>
          </ul>
          <img class="helpimg" src="../media/pictures/locationhelp.png" alt="" height="300" width="450">

          <h5>Cover photo</h5>
          <ul>
            <li> Insert the photo you want to display for the bar in the feed.</li>
          </ul>
          <h5>More pictures</h5>
          <ul>
            <li>Here you can add more pictures for your bar, they will be displayed once the user clicks the bar in a picture carousell.</li>
          </ul>
          <h5>Drink menu</h5>
          <ul>
            <li>In the drink menu you can insert the drink (name of the drink), the main menu (this is the categories displayed inside the feed bar), sub menu which is categories of types of drinks inside one category (wine - white wine, red wine..).</li>
          </ul>

          <h5>Manage Drinks</h4>
            <ul>
              <li>Clicking on the "manage drinks" button you will be able to change a drink across several bars. Be careful here because all drinks of the type you change will be affected in the bars that contain this drink. Changing Peroni to Peroni Beer will change it in all bars. To manage individual drinks go to the bar you want to and change the drink name there.</li>
              <li>Here you can also delete drinks across all bars containing a spesific drink.</li>
            </ul>

          <h5>Manage Tags</h4>
            <ul>
              <li>Here you can change tags across all bars, changing a tag here will effect all bars that contain that spesific tag. To change individual tags go to the particular bar you want to change.</li>
              <li>You can also delete tags completely here.</li>
            </ul>

          <h5>Manage Main Menus</h4>
            <ul>
              <li>Here you can manage main menus (these are the categories displayed with icons in the bar view for users.) If you want to rename a category click modify, and delete to completely remove a category.</li>
              <li>The main menus should only be managed by one of the editors.</li>
            </ul>
            <a href="https://docs.google.com/document/d/1zevghs4wtIxRtjFtiZSW_w_7LM40hciB78S3tPJPc0s/edit?usp=sharing">Information for developers</a>

        </div>
      </div>

    <img src="../media/icons/help.png" class="openicon" onclick="openNav()">

        <div class="above-list">
            <h1>Welcome back.</h1>
            <a href="inputForm_add.php">
                <button class="btn-nav" id='addNewBar'>Create new bar</button>
            </a>
        </div>

        <div class="list">
            <p>Modify the information for an existing bar:</p>
            <table>
                <?php
                while($currentRow = mysqli_fetch_array($result_bar)) {
                    $name = $currentRow['name'];
                    $id = $currentRow['id'];
                    $n = 'barname'.$id;
                    $input = 'barinput'.$id;
                    echo "
                <tr id='row$id'>
                    <form action='' method='post'>
                        <td>$name</td>
                        <input type='hidden' name='barID' value='$id' id='$input'>
                        <input type='hidden' name='section' value='bar'>
                        <td class='td-submit'><button type='submit' class='modify' value='submit' formaction='inputForm_add.php'>modify</button></td>
                        <td class='td-submit'><button type='button' id='del$id' class='delete' onclick='req_delete($id, \"bar\", \"main\")'>delete</button></td>
                    </form>
                </tr>";
                }
                ?>
            </table>
        </div>

        <div class='footer'>
            <form action='' method='post'>
                <button type='button' class='btn-nav' onclick='open_popup("popup_drink", "main")'>Manage drinks</button>
                <button type='button' class='btn-nav' onclick='open_popup("popup_tag", "main")'>Manage tags</button>
                <button type='button' class='btn-nav' onclick='open_popup("popup_drink_type", "main")'>Manage main menus</button>
            </form>
        </div>
    </div>

    <div id="popup_drink" class="popup">
        <h1>Manage drinks</h1>
        <img class="btn-close" src="../media/icons/exit_white.png" onclick="close_popup('popup_drink', 'main')">
        <p>Modifying or deleting drinks will affect all bars offering that drink.<br>So please be careful, the Changes cannot be undone.</p>
        <div class="list">
            <table>
                <tr>
                    <th>Drink</th>
                    <th>Main menu</th>
                    <th></th>
                    <th></th>
                </tr>
                <?php
                while($currentRow = mysqli_fetch_array($result_drinks)) {
                    $name = $currentRow['name'];
                    $id=$currentRow['id'];
                    $type=$currentRow['drink_type'];
                    $columns = json_encode(['name', 'drink_type']);
                    echo "
                <tr id='row$id'>
                    <form action='' method='post'>
                        <td><input type='text' id='name$id' name='name' value='$name' disabled></td>
                        <td><select id='drink_type$id' name='drink_type' disabled>" . str_replace(">".$type, "selected='selected'>".$type, $options_all_drink_types) . "</select></td>
                        <input type='hidden' name='section' value='drink'>
                        <td class='td-submit'>
                            <button type='button' id='mod$id' name='req_mod' class='modify' onclick ='req_modify(\"popup_drink\", $id, $columns)'>modify</button>
                            <button type='submit' id='add$id' name='add' class='add' value=$id style='display: none'>save</button>
                        </td>
                        <td class='td-submit'>
                            <button type='button' id='del$id' class='delete' onclick='req_delete($id, \"drink\", \"popup_drink\")'>delete</button>
                        </td>
                    </form>
                </tr>";
                }
                ?>
            </table>
        </div>
    </div>

    <div id="popup_tag" class="popup">
        <h1>Manage tags</h1>
        <img class="btn-close" src="../media/icons/exit_white.png" onclick="close_popup('popup_tag', 'main')">
        <p>Be careful, changing a tag here will change it for all venues.<br>If you want to change a tag for one venue only,<br>you can do this by selecting the particular venue on the main view.</p>
        <div class="list">
            <table>
                <tr>
                    <th>Tag name</th>
                    <th></th>
                    <th></th>
                </tr>
                <?php
                while($currentRow = mysqli_fetch_array($result_tags)) {
                    $name = $currentRow['name'];
                    $id=$currentRow['id'];
                    $n='tags'.$id;
                    $type=$currentRow['drink_type'];
                    $input = 'tagsinput'.$id;
                    $columns = json_encode(['name']);
                    echo "
                <tr id='row$id'>
                    <form action='' method='post'>
                        <td><input type='text' id='name$id' name='name' value='$name' disabled></td>
                        <input type='hidden' name='section' value='tag'>
                        <td class='td-submit'>
                            <button type='button' id='mod$id' name='req_mod' class='modify' onclick ='req_modify(\"popup_tag\", $id, $columns)'>modify</button>
                            <button type='submit' id='add$id' name='add' class='add' value=$id style='display: none'>save</button>
                        </td>
                        <td class='td-submit'>
                            <button type='button' id='del$id' class='delete' onclick='req_delete($id, \"tag\", \"popup_tag\")'>delete</button>
                        </td>
                    </form>
                </tr>";
                }
                ?>
            </table>
        </div>
    </div>

    <div id="popup_drink_type" class="popup">
        <h1>Manage main menus</h1>
        <img class="btn-close" src="../media/icons/exit_white.png" onclick="close_popup('popup_drink_type', 'main')">
        <p>Please be very careful with the main menu. Those should only be managed by one of the editors.<br>Deleting a menu will delete all drinks across all bars that are attached to it.</p>
        <div class="list">
            <table>
                <tr>
                    <th>Main menu</th>
                    <th>Volume unit</th>
                    <th>Icon (grey)</th>
                    <th>Icon (blue)</th>
                    <th></th>
                    <th></th>
                </tr>
                <tr id="row0">
                    <form action='' method='post'>
                        <input type='hidden' name='section' value='drink_type'>
                        <td><input type='text' id='name0' name='name'></td>
                        <td><select id='volume_unit0' name='volume_unit'><?php echo $options_all_volume_units?></select></td>
                        <td><input type='text' id='img_url_inactive0' name='img_url_inactive'></td>
                        <td><input type='text' id='img_url_active0' name='img_url_active'></td>
                        <td class="td-submit"><button type='submit' class='add' id='add0' name='add' value=''>add</button></td>
                        <td></td>
                        <td></td>
                    </form>
                </tr>
                <?php
                while($currentRow = mysqli_fetch_array($result_drink_types)) {
                    $name = $currentRow['name'];
                    $id=$currentRow['id'];
                    $rank=$currentRow['rank'];
                    $img_url_inactive=$currentRow['img_url_inactive'];
                    $img_url_active=$currentRow['img_url_active'];
                    $volume_unit=$currentRow['volume_unit'];
                    $n='menus'.$id;
                    $input = 'menusinput'.$id;
                    $columns = json_encode(['name', 'volume_unit', 'img_url_inactive', 'img_url_active']);
                    echo "
                    <tr id='row$id'>
                        <form action='' method='post'>
                            <input type='hidden' name='section' value='drink_type'>
                            <input type='hidden' name='current_rank' value='$rank'>
                            <td><input type='text' id='name$id' name='name' value='$name' disabled></td>
                            <td><select id='volume_unit$id' name='volume_unit' disabled>". str_replace("<option value='$volume_unit'>", "<option value='$volume_unit' selected>", $options_all_volume_units). "</select></td>
                            <td><input type='text' id='img_url_inactive$id' name='img_url_inactive' value='$img_url_inactive' disabled></td>
                            <td><input type='text' id='img_url_active$id' name='img_url_active' value='$img_url_active' disabled></td>
                            <td>
                                <button type='submit' name='change_rank' id='up$id' value='up'>  ▲</button>
                                <button type='submit' name='change_rank' id='up$id' value='down'>▼</button>
                            </td>
                            <td class='td-submit'>
                                <button type='button' id='mod$id' name='req_mod' class='modify' onclick ='req_modify(\"popup_drink_type\", $id, $columns)'>modify</button>
                                <button type='submit' id='add$id' name='add' class='add' value=$id style='display: none'>save</button>
                            </td>
                            <td class='td-submit'>
                                <button type='button' id='del$id' class='delete' onclick='req_delete($id, \"drink_type\", \"popup_drink_type\")'>delete</button>
                            </td>
                        </form>
                    </tr>";
                }
                ?>
            </table>
        </div>
    </div>

    <div id="popup_confirmation" class="popup">
        <h1>Are you sure?</h1>
        <p>This action cannot be undone. Do you really want
           to delete this item?.</p>
        <form action='' method='post'>
            <input type='hidden' id='confirm-section' name='section' value=''>
            <input type='hidden' id='popup_confirmation-source' value=''>
            <button type='button' id='confirm-keep' class='modify' onclick='close_popup("popup_confirmation", null)'>keep</button>
            <button type='submit' id='confirm-delete' class='delete' name='del' value=''>delete</button>
        </form>
    </div>

    <div id="popup_resolution_warning" class="popup">
        <h1>Sorry, your resolution is too low.</h1>
        <p>This form contains tables that are more usable on a larger screen.</p>
    </div>

    <script type='text/javascript' src='../js/inputForm.js?version=<?= time() ?>'> </script>

    <!-- Open popup-->
    <?php if(isset($_POST['section'])) { echo "<script>open_popup('popup_" . $_POST['section'] . "', 'main')</script>"; }?>

</body>
</html>
