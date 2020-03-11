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
    array('id' => '1', 'name' => 'Loud-music'),
    array('id' => '2', 'name' => 'For-student')
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
                echo "<div class='item'>";
                echo " <input value=$name >";
                echo "<button type='button' class='delete' onclick ='del($i)' >delete</button>
                  <button type='button' class='modify'>modify</button>
                </div>";
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
                $n=$i+count($result_bar);
                echo "<div class='item' id='item'>";
                echo " <input value=$name >";
                echo "<button type='button' class='delete' onclick ='del($n)' > delete </button>
                  <button type='button' class='modify'>modify</button>
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
                $n_2=$i+count($result_bar)+count($result_drink);
                echo "<div class='item'>";
                echo " <input value=$name >";
                echo "<button type='button' class='delete' onclick='del($n_2)'>delete</button>
                      <button type='button' class='modify'>modify </button>
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
                  $n_3=$i+count($result_bar)+count($result_drink)+count($result_tags);
                  echo "<div class='item'>";
                  echo " <input value=$name >";
                  echo "<button type='button' class='delete' onclick='del($n_3)' >delete</button>
                    <button type='button' class='modify'>modify</button>
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
<script type='text/javascript' src='../js/input_del.js'></script>

</body>
</html>
