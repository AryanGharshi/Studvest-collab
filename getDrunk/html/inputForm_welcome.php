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

    # Code below just creates a new, random bar (useful to test deletion)
    $sql_addBar = "INSERT INTO `bar` (`id`, `name`, `description`, `rating`, `website`, `phone`, `location`) VALUES ('11', 'Only delete this bar', NULL, NULL, '', '', '');";
    $conn->query($sql_addBar);

    # Process POST-statements to delete/modify
    if(isset($_POST['del'])){
        $id = (int) $_POST['del'];
        $section = $_POST['section'];
        $sql_del = "DELETE FROM $section WHERE id=$id";
        $conn->query($sql_del);
    }

    if(isset($_POST['reg'])){
        $id = (int) $_POST['del'];
        $section = $_POST['section'];
        $sql_del = "DELETE FROM $section WHERE id=$id";
        $conn->query($sql_del);
    }


    # Retrieve data from the database
    $sql_bar = "SELECT * FROM bar";
    $result_bar = ($conn->query($sql_bar));
    $sql_menu = "SELECT * FROM menu";
    $result_menu = ($conn->query($sql_menu));
    $sql_tags = "SELECT * FROM tag";
    $result_tags = ($conn->query($sql_tags));
    $sql_drinks = "SELECT drink.id AS id, drink.name AS name, menu.name AS menu FROM drink INNER JOIN menu ON drink.menu_id = menu.id";
    $result_drinks = ($conn->query($sql_drinks));

    $conn->close();
  ######                 editBar                                 ##########################################################################################
  $result_menu = array(
      array('id' => '3', 'drink_name' => 'Hansa', 'menu_name' => 'Beer', 'price' => '85', 'size' => '0.5'),
      array('id' => '4', 'drink_name' => 'Sommersby', 'menu_name' => 'Cider', 'price' => '95', 'size' => '0.5')
    );

  $result_bar = array(
      array('id' => '2','name' => 'diskuterbar','description' => 'digg bar med billige priser', 'website' => 'https://www.facebook.com/diskuterbar/','phone' => '40399114','location' => 'https://www.google.com/maps/place/Diskuterbar/@60.3884287,5.3234435,15z/data=!4m5!3m4!1s0x0:0x6a780df5601c78ce!8m2!3d60.3884287!4d5.3234435')
    );
?>
<div class="welcome" >
    <?php include('titlebar.php'); ?>
    <div class="main" id='main' >
        <div id="are">
            <h1>Welcome back.</h1>
            <button type="button" class="add" id='addNewBar'>Add new bar</button>
            <p class="change">Modify the information for an existing bar</p>
            <?php
            while($currentRow = mysqli_fetch_array($result_bar)) {
                $name = $currentRow['name'];
                $id = $currentRow['id'];
                $n = 'barname'.$id;
                $input = 'barinput'.$id;
                echo "<div class='item' id='$n' >
                      <input value='$name' id='$input' disabled=false/>
                      <form action='' method='post'>
                          <input type='hidden' name='section' value='bar'>
                          <button type='submit' class='delete' name='del' value=$id>delete</button>
                      </form>
                      <button type='button' class='modify' id='modify$input' onclick ='reg(".'"barinput"'.",$id,$input)'>modify</button>

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
                echo "<div class='item' id='$n'>
                      <input value='$name' id='$input' disabled=false/>
                      <form action='' method='post'>
                      <input type='hidden' name='section' value='drink'>
                      <button type='submit' class='delete' name='del' value=$id>delete</button>
                      </form>
                      <button type='button' class='modify' id='modify$input' onclick ='reg(".'"drinksinput"'.",$id,$input)'>modify</button>
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
                echo "<div class='item' id='$n'>
                      <input value='$name' id='$input' disabled=false/>
                      <form action='' method='post'>
                      <input type='hidden' name='section' value='tag'>
                      <button type='submit' class='delete' name='del' value=$id>delete</button>
                      </form>
                      <button type='button' class='modify' id='modify$input' onclick ='reg(".'"tagsinput"'.",$id,$input)'>modify</button>
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
                  echo "<div class='item' id='$n'>
                        <input value='$name' id='$input' disabled=false/>
                        <form action='' method='post'>
                        <input type='hidden' name='section' value='menu'>
                        <button type='submit' class='delete' name='del' value=$id>delete</button>
                        </form>
                        <button type='button' class='modify' id='modify$input' onclick ='reg(".'"menusinput"'.",$id,$input)'>modify</button>
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
  </div>





<script type='text/javascript' src='../js/inputwelcome_1.js'> </script>
<script type='text/javascript' src='../js/input_reg.js'></script>
<script type='text/javascript' src='../js/input_del.js'></script>

</body>
</html>
