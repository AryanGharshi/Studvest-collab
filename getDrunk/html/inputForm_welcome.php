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
  <p id="demo">2</p>

<?php
$result_bar = array(
    array('id' => '2', 'name' => 'diskuterbar'),
    array('id' => '3', 'name' => 'Ricks'),
    array('id' => '5', 'name' => 'Downstairs'),
    array('id' => '6', 'name' => 'Kava')
);

$result_drink = array(
    array('id' => '12', 'name' => 'Hansa', 'menu' => 'Beer'),
    array('id' => '11', 'name' => 'Ipa', 'menu' => 'Cider'),
    array('id' => '14', 'name' => 'Plumbers', 'menu' => 'Beer'),
    array('id' => '15', 'name' => 'Somersby', 'menu' => 'Beer'),
    array('id' => '13', 'name' => 'Tuborg', 'menu' => 'Beer')
);

$result_menus = array(
    array('id' => '3', 'name' => 'Beer'),
    array('id' => '4', 'name' => 'Cider'),
    array('id' => '5', 'name' => 'Soft-drink'),
    array('id' => '6', 'name' => 'Wine')
);

$result_tags = array(
    array('id' => '1', 'name' => 'cozy'),
    array('id' => '2', 'name' => 'dancefloor'),
    array('id' => '3', 'name' => 'Loud-music'),
    array('id' => '4', 'name' => 'For-student')
);
?>
<div class="welcome">

    <div class="titlebar">
        <div class="title">StudOut - Administration</div>
    </div>
    <div class="main">
        <div id="are">
            <h1>Welcome back.</h1>
            <button type="button" class="add">Add new bar</button>
            <p class="change">Modify the information for an existing bar</p>
            <?php
            for ($i = 0; $i < count($result_bar); $i++) {
                $name = $result_bar[$i]['name'];
                $id = $result_bar[$i]['id'];
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
            for ($i = 0; $i < count($result_drink); $i++) {
                $name = $result_drink[$i]['name'];
                $id=$result_drink[$i]['id'];
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
            for ($i = 0; $i < count($result_tags); $i++) {
                $name = $result_tags[$i]['name'];
                $id=$result_tags[$i]['id'];
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
              for ($i = 0; $i < count($result_menus); $i++) {
                  $name = $result_menus[$i]['name'];
                  $id=$result_menus[$i]['id'];
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
