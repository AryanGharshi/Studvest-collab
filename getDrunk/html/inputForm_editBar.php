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
        $result_menu = array(
            array('id' => '3', 'drink_name' => 'Hansa', 'menu_name' => 'Beer', 'price' => '85', 'size' => '0.5'),
            array('id' => '4', 'drink_name' => 'Sommersby', 'menu_name' => 'Cider', 'price' => '95', 'size' => '0.5')
          );

        $result_bar = array(
            array('id' => '2','name' => 'diskuterbar','description' => 'digg bar med billige priser', 'website' => 'https://www.facebook.com/diskuterbar/','phone' => '40399114','location' => 'https://www.google.com/maps/place/Diskuterbar/@60.3884287,5.3234435,15z/data=!4m5!3m4!1s0x0:0x6a780df5601c78ce!8m2!3d60.3884287!4d5.3234435')
          );

      ?>


        <div class="titlebar">
            <div class="title">Studvest Bar Pulse - Administration</div>
        </div>
        <div class="mainEdit">
          <h1>Edit Bar</h1>
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
                  echo "<td id="cob of column and id">".$drink[menu_name]."</td>";
                  echo "<td>".$drink[size]."</td>";
                  echo "<td>".$drink[price]."</td>";
                  echo "<td><button type='button' class='modify'>edit</button></td>";
                  echo "<td><button type='button' class='delete'>delete</button></td>";
                }
                ?>
              </table>
          </form>
          </div>
            <br>

          <div class="saveBtn">
            <button type="submit" form="editBar">save and close</button>
          </div>

    </body>
</html>
