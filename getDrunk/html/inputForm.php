<!DOCTYPE html>
<html lang="en">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link rel='icon' href='../media/favicons/studvest.png' type='image/x-icon'/ >
    <link rel="stylesheet" href="../css/main.css?version=<?= time() ?>">
    <link rel="stylesheet" href="../css/inputForm.css?version=<?= time() ?>">
</head>

<body id="body-main">

<!-- SQL COMMANDS -->
<?php
    define("MAGICKEY", "ugugUGu221KHJBD84");
    require "../inc/connection/conn.php";

    # Delete Drinks / Drink Types / Tags / Bars
    if(isset($_POST['del'])){
        $id = (int) $_POST['del'];
        $section = $_POST['section'];
        $sql_del = "DELETE FROM $section WHERE id=$id";
        $conn->query($sql_del);

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
            $sql = "UPDATE drink_type
                    SET name='$newName', img_url_inactive='".$_POST['img_url_inactive']."', img_url_active='".$_POST['img_url_active']."'
                    WHERE id=$id";
            $conn->query($sql);
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

    # Load Data from the Database
    $sql_bar = "SELECT * FROM bar";
    $result_bar = ($conn->query($sql_bar));
    $sql_drink_type = "SELECT * FROM drink_type";
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

    $conn->close();
?>

<!-- LOAD HEADER -->

    <a href="inputForm.php">
        <?php include('header.php'); ?>
    </a>

    <div id="main">
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
                <tr id='$n'>
                    <form action='' method='post' onsubmit='return sub(".'"barinput"'.",$id,$input)'>
                        <td>$name</td>
                        <input type='hidden' name='barID' value='$id' id='$input'>
                        <input type='hidden' name='section' value='bar'>
                        <td><button type='submit' class='delete' name='req_del' id='delete$input' value=$id>delete</button></td>
                        <td><button type='submit' class='modify' value='submit' formaction='inputForm_add.php'>modify</button></td>
                    </form>
                </tr>";
                }
                ?>
            </table>
        </div>

        <div class='footer'>
            <form action='' method='post'>
                <button type='submit' class='btn-nav' name='section' value='drink'>Manage drinks</button>
                <button type='submit' class='btn-nav' name='section' value='tag'>Manage tags</button>
                <button type='submit' class='btn-nav' name='section' value='drink_type'>Manage main menus</button>
            </form>
        </div>
    </div>

    <div id="popup_drink" class="popup">
        <h1>Manage drinks</h1>
        <form action='' method='post'><input type="image" name="submit" src="../media/icons/exit_white.png" alt="submit" class="btn-close"></form>
        <p>Please be careful. If you modify or delete a drink, it will affect all bars offering that drink. Changes cannot be undone.</p>
        <div class="list">
            <table>
                <tr>
                    <th>Drink</th>
                    <th>Main menu</th>
                    <th></th>
                </tr>
                <?php
                while($currentRow = mysqli_fetch_array($result_drinks)) {
                    $name = $currentRow['name'];
                    $id=$currentRow['id'];
                    $type=$currentRow['drink_type'];
                    $columns = json_encode(['name', 'drink_type']);
                    echo "
                <tr id='$n'>
                    <form action='' method='post'>
                        <td><input type='text' id='name$id' name='name' value='$name' disabled></td>
                        <td><select id='drink_type$id' name='drink_type' disabled>" . str_replace(">".$type, "selected='selected'>".$type, $options_all_drink_types) . "</select></td>
                        <input type='hidden' name='section' value='drink'>
                        <td>
                            <button type='button' id='mod$id' name='req_mod' class='modify' onclick ='req_modify($id, $columns)'>modify</button>
                            <button type='submit' id='add$id' name='add' class='add' value=$id style='display: none'>save</button>
                        </td>
                        <td>
                            <button type='submit' id='del$id' name='req_del' class='delete' value=$id>delete</button>
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
        <form action='' method='post'><input type="image" name="submit" src="../media/icons/exit_white.png" alt="submit" class="btn-close"></form>
        <p>Please be careful! Your changes will affect all bars that have this tag assigned. Changes cannot be undone.</p>
        <div class="list">
            <table>
                <tr>
                    <th>Tag name</th>
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
                <tr id='$n'>
                    <form action='' method='post'>
                        <td><input type='text' id='name$id' name='name' value='$name' disabled></td>
                        <input type='hidden' name='section' value='tag'>
                        <td>
                            <button type='button' id='mod$id' name='req_mod' class='modify' onclick ='req_modify($id, $columns)'>modify</button>
                            <button type='submit' id='add$id' name='add' class='add' value=$id style='display: none'>save</button>
                        </td>
                        <td>
                            <button type='submit' id='del$id' name='req_del' class='delete' value=$id>delete</button>
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
        <form action='' method='post'><input type="image" name="submit" src="../media/icons/exit_white.png" alt="submit" class="btn-close"></form>
        <p>Please be careful! Your changes will affect all bars. Changes cannot be undone.</p>
        <div class="list">
            <table>
                <tr>
                    <th>Main menu</th>
                    <th>Icon (grey)</th>
                    <th>Icon (blue)</th>
                    <th></th>
                </tr>
                <?php
                while($currentRow = mysqli_fetch_array($result_drink_types)) {
                    $name = $currentRow['name'];
                    $id=$currentRow['id'];
                    $img_url_inactive=$currentRow['img_url_inactive'];
                    $img_url_active=$currentRow['img_url_active'];
                    $n='menus'.$id;
                    $input = 'menusinput'.$id;
                    $columns = json_encode(['name', 'img_url_inactive', 'img_url_active']);
                    echo "
                    <tr id='$n'>
                        <form action='' method='post'>
                            <td><input type='text' id='name$id' name='name' value='$name' disabled></td>
                            <td><input type='text' id='img_url_inactive$id' name='img_url_inactive' value='$img_url_inactive' disabled></td>
                            <td><input type='text' id='img_url_active$id' name='img_url_active' value='$img_url_active' disabled></td>
                            <input type='hidden' name='section' value='drink_type'>
                            <td>
                                <button type='button' id='mod$id' name='req_mod' class='modify' onclick ='req_modify($id, $columns)'>modify</button>
                                <button type='submit' id='add$id' name='add' class='add' value=$id style='display: none'>save</button>
                            </td>
                            <td>
                                <button type='submit' id='del$id' name='req_del' class='delete' value=$id>delete</button>
                            </td>
                        </form>
                    </tr>";
                }
                ?>
            </table>
        </div>
    </div>

    <div id="popup_confirmation" class="popupdel">
        <h1>Are you sure?</h1>
        <p>This action cannot be undone. Do you really want to delete this item?.</p>
        <?php
        $id = (int) $_POST['req_del'];
        $section = $_POST['section'];
        echo "
        <form action='' method='post'>
            <input type='hidden' name='section' value='$section'>
            <button type='submit' class='delete' name='del' value=$id>delete</button>
            <button type='submit' class='modify' name='keep'>keep</button>
        </form>";
        ?>
    </div>

<?php
    if(isset($_POST['section'])) {
        echo "<script>
                  target_popup = document.getElementById('popup_" . $_POST['section'] . "').style.display='block';
                  document.getElementById('main').style.opacity = '0.3';
              </script>";
    }

    if(isset($_POST['req_del'])){
        echo "<script>
                  document.getElementById('popup_confirmation').style.display='block';
                  document.getElementById('main').style.opacity = '0.3';
              </script>";
    }
    ?>

  <script type='text/javascript' src='../js/inputForm.js?version=<?= time() ?>'> </script>
  <script type='text/javascript' src='../js/input_del.js?version=<?= time() ?>'></script>

</body>
</html>
