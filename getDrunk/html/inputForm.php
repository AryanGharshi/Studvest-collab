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
    $conn -> query(addDataEntry('drink_type', 'test_type1'));
    $conn -> query(addDataEntry('drink_type', 'test_type2'));

    # Process POST-statements to delete/modify
    if(isset($_POST['del'])){
        $id = (int) $_POST['del'];
        $section = $_POST['section'];
        $sql_del = "DELETE FROM $section WHERE id=$id";
        $conn->query($sql_del);
    }

    if(isset($_POST['add'])){
        $newName = $_POST['name'];
        $section = $_POST['section'];
        $id = $_POST['add'];
        $sql_regi = "UPDATE $section
                    SET $section.name='$newName'
                    WHERE $section.id=$id";
        print($sql_regi);
        $conn->query($sql_regi);
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
                    phone=$phone,
                    location='$location'
                WHERE id=$id";
        $conn->query($sql);
    }

    # Retrieve data from the database
    $sql_bar = "SELECT * FROM bar";
    $result_bar = ($conn->query($sql_bar));
    $sql_drink_type = "SELECT * FROM drink_type";
    $result_drink_types = ($conn->query($sql_drink_type));
    $sql_tags = "SELECT * FROM tag";
    $result_tags = ($conn->query($sql_tags));
    $sql_drinks = "SELECT drink.id AS id, drink.name AS name, drink_type.name AS drink_type FROM drink INNER JOIN drink_type ON drink.drink_type_id = drink_type.id";
    $result_drinks = ($conn->query($sql_drinks));

    $conn->close();
?>
<div class="welcome" >
    <?php include('header.php'); ?>
    <div class="main" id='addBar' >
        <div id="are">
            <h1>Welcome back.</h1>
            <a href="inputForm_add.php" class="add" id='addNewBar'>Add new bar</a>
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
                          <input type='hidden' name='barID' value='$id' id='$input'>
                          <input type='hidden' name='section' value='bar'>
                          <button type='submit' class='delete' name='del' value=$id>delete</button>
                          <button type='submit' class='modify' value='submit' formaction='inputForm_add.php'>modify</button>
                      </form>
                      </div> ";
            }
            ?>
            </div>

        <div id="popup_1" class="popup">
          <div  class="content">
            <h1>Mange drinks</h1>
            <img src="../media/icons/exit_white.png" alt="cancel" class="close" id="close">
            <p>Please be careful. If you modify or delete a drink, it will affect all bars offering that drink.</p>
            <?php
            while($currentRow = mysqli_fetch_array($result_drinks)) {
                $name = $currentRow['name'];
                $id=$currentRow['id'];
                $n='drinks'.$id;
                $input = 'drinksinput'.$id;
                echo "<div class='item' id='$n'>
                      <form action='' method='post'>
                      <input name='name' value='$name' id='$input' disabled=false/>
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
            <p class="change">Please be careful! Your changes will affect all bars that have this tag assigned.</p>
            <?php
            while($currentRow = mysqli_fetch_array($result_tags)) {
                $name = $currentRow['name'];
                $id=$currentRow['id'];
                $n='tags'.$id;
                $input = 'tagsinput'.$id;
                echo "<div class='item' id='$n'>
                      <form action='' method='post'>
                      <input name='name' value='$name' id='$input' disabled=false/>
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
              <h1 >Mange types of drinks</h1>
              <img src="../media/icons/exit_white.png" alt="cancel" class="close" id="close">
              <p class="change">Please be careful! Your changes will affect all bars.</p>
              <?php
              while($currentRow = mysqli_fetch_array($result_drink_types)) {
                  $name = $currentRow['name'];
                  $id=$currentRow['id'];
                  $n='menus'.$id;
                  $input = 'menusinput'.$id;
                  echo "<div class='item' id='$n'>
                        <form action='' method='post'>
                        <input name='name' value='$name' id='$input' disabled=false/>
                        <input type='hidden' name='section' value='drink_type'>
                        <button type='submit' class='delete' name='del' value=$id>delete</button>
                        <button type='button' class='modify' name='regi' id='modify$input' onclick ='reg(".'"menusinput"'.",$id,$input)'>modify</button>
                        <button type='submit' class='add_btn' name='add' value=$id id='submit$input'>add</button>
                        </form>
                        </div>";
              }
              ?>

            </div>
        </div>

        <div class='side_foot' id='side_foot'>
        <button type='button' class='btn' section="drinks" value="popup_1">Mange drinks</button>
        <button type='button' class='btn' section="tags" value="popup_2">Mange tags</button>
        <button type='button' class='btn' section="drink_type" value="popup_3">Mange types of drink</button>
    </div>
    </div>
  </div>
  <?php
      if($_POST['section']=='tag') {
          echo "<script>
                  document.getElementById('are').style.display='none';           // hides the main div
                  document.getElementById('side_foot').style.display='none';     // hides the footer
                  let target_popup = document.getElementById('popup_2');         // retrieves the object with the respective id
                  target_popup.style.display='block';
                 </script>";
      }

      elseif ($_POST['section']=='drink_type') {
          echo "<script>
                  document.getElementById('are').style.display='none';           // hides the main div
                  document.getElementById('side_foot').style.display='none';     // hides the footer
                  let target_popup = document.getElementById('popup_3');             // retrieves the object with the respective id
                  target_popup.style.display='block';
                 </script>";
      }
      elseif ($_POST['section']=='drink') {
          echo "<script>
                  document.getElementById('are').style.display='none';           // hides the main div
                  document.getElementById('side_foot').style.display='none';     // hides the footer
                  let target_popup = document.getElementById('popup_1');             // retrieves the object with the respective id
                  target_popup.style.display='block';
                 </script>";
      }
  ?>

  <script type='text/javascript' src='../js/inputForm.js?version=<?= time() ?>'> </script>
 <script type='text/javascript' src='../js/input_del.js?version=<?= time() ?>'></script>

</body>
</html>
