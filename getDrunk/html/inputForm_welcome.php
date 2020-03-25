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

    # Retrieve list of all bars
    $sql_bar = "SELECT * FROM bar";
    $result_bar = ($conn->query($sql_bar));

    # Retrieve list of all menus
    $sql_menu = "SELECT * FROM menu";
    $result_menu = ($conn->query($sql_menu));

    # Retrieve list of all tags
    $sql_tags = "SELECT * FROM tag";
    $result_tags = ($conn->query($sql_tags));

    # Retrieve list of all drinks
    $sql_drinks = "SELECT drink.id AS id, drink.name AS name, menu.name AS menu FROM drink INNER JOIN menu ON drink.menu_id = menu.id";
    $result_drinks = ($conn->query($sql_drinks));

    $conn->close();
?>
<div class="welcome">
    <?php include('titlebar.php'); ?>
    <div class="main">
        <div id="are">
            <h1>Welcome back.</h1>
            <button type="button" class="add">Add new bar</button>
            <p class="change">Modify the information for an existing bar</p>
            <?php
            while($currentRow = mysqli_fetch_array($result_bar)) {
                $name = $currentRow['name'];
                $id = $currentRow['id'];
                $n = 'barname'.$id;
                $input = 'barinput'.$id;
                echo "<div class='item' id='$n' >
                      <input value='$name' id='$input' disabled=false/>
                      <button type='button' class='delete' onclick ='del(".'"barname"'.",$id)'>delete</button>
                      <button type='button' class='modify' onclick ='reg(".'"barinput"'.",$id)'>modify</button>
                      </div> ";
            }
            ?>
        </div>
        <div id="popup_1" class="popup">
          <div  class="content">
            <h1>Mange drinks</h1>
            <img src="../media/icons/close.png" alt="cancel" class="close" id="close">
            <p>Change affect all bars with tags</p>
            <?php
            while($currentRow = mysqli_fetch_array($result_drinks)) {
                $name = $currentRow['name'];
                $id=$currentRow['id'];
                $n='drinks'.$id;
                $input = 'drinksinput'.$id;
                echo "<div class='item' id='$n'>";
                echo " <input value=$name id='$input' disabled=false/>";
                echo "<button type='button' class='delete' onclick ='del(".'"drinks"'.",$id)'> delete </button>
                  <button type='button' class='modify' onclick ='reg(".'"drinksinput"'.",$id)'>modify</button>
                </div>";
            }
            ?>

          </div>
        </div>
        <div id="popup_2" class="popup">
          <div  class="content">
            <h1>Mange tags</h1>
            <img src="../media/icons/close.png" alt="cancel" class="close" id="close">
            <p class="change">Change affect all bars with tags</p>
            <?php
            while($currentRow = mysqli_fetch_array($result_tags)) {
                $name = $currentRow['name'];
                $id=$currentRow['id'];
                $n='tags'.$id;
                $input = 'tagsinput'.$id;
                echo "<div class='item' id='$n'>";
                echo " <input value=$name id='$input' disabled=false >";
                echo "<button type='button' class='delete' onclick ='del(".'"tags"'.",$id)'>delete</button>
                      <button type='button' class='modify' onclick ='reg(".'"tagsinput"'.",$id)'>modify </button>
                      </div>";
            }
            ?>
          </div>

        </div>
        <div id="popup_3" class="popup">
            <div  class="content">
              <h1>Mange drinks</h1>
              <img src="../media/icons/close.png" alt="cancel" class="close" id="close">
              <p class="change">Change affect all bars with tags</p>
              <?php
              while($currentRow = mysqli_fetch_array($result_menu)) {
                  $name = $currentRow['name'];
                  $id=$currentRow['id'];
                  $n='menus'.$id;
                  $input = 'menusinput'.$id;
                  echo "<div class='item' id='$n'>";
                  echo " <input value=$name  id='$input' disabled/>";
                  echo "<button type='button' class='delete' onclick ='del(".'"menus"'.",$id)'>delete</button>
                    <button type='button' class='modify' onclick ='reg(".'"menusinput"'.",$id)'>modify</button>
                  </div>";
              }
              ?>

            </div>
        </div>

    <div class='side_foot' id='side_foot'>
        <button type='button' class='btn'>Mange drinks</button>
        <button type='button' class='btn'>Mange tags</button>
        <button type='button' class='btn'>Mange menus</button>
    </div>

</div>

<script type='text/javascript' src='../js/inputwelcome_1.js'></script>
<script type='text/javascript' src='../js/input_reg.js'></script>
<script type='text/javascript' src='../js/input_del.js'></script>

</body>
</html>
