<!DOCTYPE html>
<html lang="en">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <head>
        <meta charset="UTF-8">
        <title>Title</title>
        <link rel="stylesheet" href="css/input_welcome.css?version=<?= time() ?>">
        <link rel="stylesheet" href="css/main.css?version=<?= time() ?>">

    </head>

    <body>

      <?php
        $result_bar = array(
                  array('id' => '2','name' => 'diskuterbar'),
                  array('id' => '3','name' => 'Ricks'),
                  array('id' => '5','name' => 'Downstairs'),
                  array('id' => '6','name' => 'Kava')
                );

        print($result_bar[0]['name'])

        $result_drink = array(
                  array('id' => '12','name' => 'Hansa', 'menu' => 'Beer'),
                  array('id' => '11','name' => 'Ipa', 'menu' => 'Cider'),
                  array('id' => '14','name' => 'Plumbers', 'menu' => 'Beer'),
                  array('id' => '15','name' => 'Somersby', 'menu' => 'Beer'),
                  array('id' => '13','name' => 'Tuborg', 'menu' => 'Beer')
                );

        $result_menus = array(
                  array('id' => '3', 'name' => 'Beer'),
                  array('id' => '4', 'name' => 'Cider')
                );

        $result_menus = array(
                  array('id' => '1','name' => 'cozy'),
                  array('id' => '2','name' => 'dancefloor')
                );
      ?>

        <div class="welcome">
          
        <div class="titlebar">
            <div class="title">StudOut - Administration</div>
        </div>
        <div class="main">

          <h1>Welcome back.</h1>
          <button type="button" class="add">Add new bar</button>
          <p>Modify the information for an existing bar</p>
          <div class="item">
            <p >Diskuterbar</p>
            <button type="button" class="delete">delete</button>
            <button type="button" class="modify">modify</button>
          </div>
          <div class="item">
            <p >Kvarteret</p>
            <button type="button" class="delete">delete</button>
            <button type="button" class="modify">modify</button>
          </div>
          <div class="item">
            <p>Heidis</p>
            <button type="button" class="delete">delete</button>
            <button type="button" class="modify">modify</button>
          </div>
          <div class="item">
            <p>Integrarbar</p>
            <button type="button" class="delete">delete</button>
            <button type="button" class="modify">modify</button>
          </div>
          <div class="side_foot">
            <button type="button">Mange drinks</button>
            <button type="button">Mange tags</button>
            <button type="button">Mange menus</button>
          </div>

          </div>
        </div>



        </div>

    </body>
</html>
