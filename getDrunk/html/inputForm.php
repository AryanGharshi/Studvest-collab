<!DOCTYPE html>
<html lang="en">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link rel="stylesheet" href="../css/inputForm.css?version=<?= time() ?>">
    <link rel="stylesheet" href="../css/main.css?version=<?= time() ?>">
</head>

<body>
<?php
    define("MAGICKEY", "ugugUGu221KHJBD84");
    require "../inc/connection/conn.php";

    function addDataEntry($section, $name) {
        return "INSERT INTO $section (name) VALUES ('$name') ON DUPLICATE KEY UPDATE name='$name'";
    }
    $conn -> query(addDataEntry('bar', 'test_bar1'));
    $conn -> query(addDataEntry('bar', 'test_bar2'));
    $conn -> query(addDataEntry('tag', 'test_tag1'));
    $conn -> query(addDataEntry('tag', 'test_tag2'));
    $conn -> query(addDataEntry('drink', 'test_drink1'));
    $conn -> query(addDataEntry('drink', 'test_drink2'));
    $conn -> query(addDataEntry('menu', 'test_menu1'));
    $conn -> query(addDataEntry('menu', 'test_menu2'));

    # Process POST-statements to delete/modify
    if(isset($_POST['del'])){
        $id = (int) $_POST['del'];
        $section = $_POST['section'];
        $sql_del = "DELETE FROM $section WHERE id=$id";
        $conn->query($sql_del);
    }

    if(isset($_POST['add'])){
        $newName = $_POST['barName'];
        $section = $_POST['section'];
        $id = $_POST['add'];
        $sql_regi = "UPDATE $section
                    SET $section.name='$newName'
                    WHERE $section.id=$id";
        print($sql_regi);
        $conn->query($sql_regi);
    }

    $sql= "UPDATE bar SET bar.name='new Name' WHERE bar.id='1'";


    # Retrieve data from the database
    $sql_bar = "SELECT * FROM bar";
    $result_bar = ($conn->query($sql_bar));
    $sql_menu = "SELECT * FROM menu";
    $result_menus = ($conn->query($sql_menu));
    $sql_tags = "SELECT * FROM tag";
    $result_tags = ($conn->query($sql_tags));
    $sql_drinks = "SELECT drink.id AS id, drink.name AS name, menu.name AS menu FROM drink INNER JOIN menu ON drink.menu_id = menu.id";
    $result_drinks = ($conn->query($sql_drinks));

    $conn->close();

    $result_menu = array(
        array('id' => '3', 'drink_name' => 'Hansa', 'menu_name' => 'Beer', 'price' => '85', 'size' => '0.5'),
        array('id' => '4', 'drink_name' => 'Sommersby', 'menu_name' => 'Cider', 'price' => '95', 'size' => '0.5')
    );
?>
<div class="welcome" >
    <?php include('header.php'); ?>
    <div class="main" id='main' >
        <div id="are">
            <h1>Welcome back.</h1>
            <button type="button" class="add" id='addNewBar' value="popup_4">Add new bar</button>
            <p class="change">Modify the information for an existing bar</p>
            <?php
            while($currentRow = mysqli_fetch_array($result_bar)) {
                $name = $currentRow['name'];
                $id = $currentRow['id'];
                $n = 'barname'.$id;
                $input = 'barinput'.$id;
                echo "<div class='item' id='$n' >

                      <form action='' method='post' onsubmit='return sub(".'"barinput"'.",$id,$input)'>
                          <input name='barName' value='$name' id='$input' disabled=false/>
                          <input type='hidden' name='section' value='bar'>
                          <button type='submit' class='delete' name='del' value=$id>delete</button>
                          <button type='button' class='modify' name='regi' id='modify$input' onclick ='reg(".'"barinput"'.",$id,$input)'>modify</button>
                          <button type='submit' class='add_btn' name='add' value=$id id='submit$input' onclick='sub(".'"barinput"'.",$id,$input)'>add</button>
                      </form>
                      </div> ";
            }
            ?>
            </div>

        <div id="popup_1" class="popup">
          <div  class="content">
            <h1>Mange drinks</h1>
            <img src="../media/icons/exit_white.png" alt="cancel" class="close" id="close">
            <p>Change affect all bars with tags</p>
            <?php
            while($currentRow = mysqli_fetch_array($result_drinks)) {
                $name = $currentRow['name'];
                $id=$currentRow['id'];
                $n='drinks'.$id;
                $input = 'drinksinput'.$id;
                echo "<div class='item' id='$n'>
                      <input name='$name' value='$name' id='$input' disabled=false/>
                      <form action='' method='post'>
                      <input type='hidden' name='section' value='drink'>
                      <button type='submit' class='delete' name='del' value=$id>delete</button>
                      <button type='button' class='modify' name='regi' id='modify$input' onclick ='reg(".'"drinksinput"'.",$id,$input)'>modify</button>
                      <button type='submit' class='add_btn' name='add' value=$id id='submit$input'>add</button>
                      </form>
                      </div>";
            }
            ?>

          </div>
        </div>
        <div id="popup_2" class="popup">
          <div  class="content">
            <h1>Mange tags</h1>
            <img src="../media/icons/exit_white.png" alt="cancel" class="close" id="close">
            <p class="change">Change affect all bars with tags</p>
            <?php
            while($currentRow = mysqli_fetch_array($result_tags)) {
                $name = $currentRow['name'];
                $id=$currentRow['id'];
                $n='tags'.$id;
                $input = 'tagsinput'.$id;
                echo "<div class='item' id='$n'>
                      <input value='$name' id='$input' disabled=false/>
                      <form action='' method='post'>
                      <input type='hidden' name='section' value='tag'>
                      <button type='submit' class='delete' name='del' value=$id>delete</button>
                      <button type='button' class='modify' id='modify$input' onclick ='reg(".'"tagsinput"'.",$id,$input)'>modify</button>
                      <button type='submit' class='add_btn' name='add' value=$id id='submit$input'>add</button>
                      </form>
                      </div>";
            }
            ?>
          </div>

        </div>
        <div id="popup_3" class="popup">
            <div  class="content">
              <h1 >Mange menus</h1>
              <img src="../media/icons/exit_white.png" alt="cancel" class="close" id="close">
              <p class="change">Change affect all bars with tags</p>
              <?php
              while($currentRow = mysqli_fetch_array($result_menus)) {
                  $name = $currentRow['name'];
                  $id=$currentRow['id'];
                  $n='menus'.$id;
                  $input = 'menusinput'.$id;
                  echo "<div class='item' id='$n'>
                        <input value='$name' id='$input' disabled=false/>
                        <form action='' method='post'>
                        <input type='hidden' name='section' value='menu'>
                        <button type='submit' class='delete' name='del' value=$id>delete</button>
                        <button type='button' class='modify' name='regi' id='modify$input' onclick ='reg(".'"menusinput"'.",$id,$input)'>modify</button>
                        <button type='submit' class='add_btn' name='add' value=$id id='submit$input'>add</button>
                        </form>
                        </div>";
              }
              ?>

            </div>
        </div>

        <div id="popup_4" >
            <div>
                <h1>Edit Bar</h1>
                <img src="../media/icons/exit_white.png" alt="cancel" class="close" id="close">
                <form id="editBar">
                    <div class="aboutBar">
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" placeholder="Enter bar name">
                    </div>
                    <div class="aboutBar">
                        <label for="address">Address:</label>
                        <input type="text" id="address" name="address" placeholder="Enter address">
                    </div>
                    <div class="aboutBar">
                        <label for="website">Website:</label>
                        <input type="text" id="website" name="website" placeholder="Enter website">
                    </div>

                    <div class="aboutBar">
                        <label for="phone">Phone:</label>
                        <input type="text" id="phone" name="phone" placeholder="Enter phone">
                    </div>

                    <div class="aboutBar">
                        <label for="description">Description:</label>
                        <textarea name="description" rows="8" cols="80" id="description" form="editBar" placeholder="Enter description"></textarea>
                    </div>
                    <br>
                    <br>

                    <!--
                    $result_tags = array(
                        array('id' => '1', 'name' => 'cozy'),
                        array('id' => '2', 'name' => 'dancefloor')
                    );
                  -->
                    <div class="aboutBar">
                        <label for="tags">Tags:</label>
                        <input type="text" name="menu" value="" placeholder="Add new tag">
                        <span><button type="button" class="add" name="submit">add</button></span>
                    </div>
                    <br>
                    <br>
                    <div class="aboutBar">
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
                                <td><input type="text" id="drink" name="drink" value="" placeholder="Drink" list="drinkList"></td>
                                <datalist id="drinkList">
                                    <option value="Bulmers Berries & Lime">
                                    <option value="Grevens Fruktsmak"></option>
                                </datalist>
                                <td><input type="text" id="menu" name="menu" value="" placeholder="Menu" list="menuList"></td>
                                <datalist id="menuList">
                                    <option value="Øl"></option>
                                    <option value="Cider"></option>
                                </datalist>

                                <td><input type="text" id="vol"name="vol" value="" placeholder="Vol"></td>
                                <td><input type="text" id="price"name="price" value="" placeholder="Price"></td>
                                <td><button type="button" class="add">add</button></td>
                                <td></td>
                            </tr>
                            <?php
                            foreach ($result_menu as $drink) {
                                echo "<tr>";
                                echo "<td>".$drink[drink_name]."</td>";
                                echo "<td>".$drink[menu_name]."</td>";
                                echo "<td>".$drink[size]."</td>";
                                echo "<td>".$drink[price]."</td>";
                                echo "<td><button type='button' class='modify'>edit</button></td>";
                                echo "<td><button type='button' class='delete'>delete</button></td>";
                                echo "</tr>";
                            }
                            ?>
                        </table>
                    </div>
                </form>
            </div>
            <br>
            <div class="saveBtn">
                <button type="submit" form="editBar">save and close</button>
            </div>
        </div>

        <div class='side_foot' id='side_foot'>
        <button type='button' class='btn' value="popup_1">Mange drinks</button>
        <button type='button' class='btn' value="popup_2">Mange tags</button>
        <button type='button' class='btn' value="popup_3">Mange menus</button>
    </div>
    </div>
  </div>





<script type='text/javascript' src='../js/inputForm.js'> </script>
<script type='text/javascript' src='../js/input_del.js'></script>

</body>
</html>
