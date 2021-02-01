<!DOCTYPE html>
<html lang="en">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<head>
  <meta charset="UTF-8">
  <title>Studout - Administration</title>
  <link rel='icon' href='../media/favicons/studvest.png' type='image/x-icon'/ >
  <link rel="stylesheet" href="../css/main.css?version=<?= time() ?>">
  <link rel="stylesheet" href="../css/login.css?version=<?= time() ?>">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
</head>
<body>
  <a href="inputForm.php">
      <?php include('header.php'); ?>
    </a>
    <div id="main" class="main">
      <form action="validate.php" method="post">
        <table align="center">
          <tr>
            <th colspan="2"><h2 align="center">Studout Login</h2></th>
          </tr>
          <tr>
            <td><i class="fas fa-user"></i>
              <input type="text" class="logininput" name="username" placeholder="Username..">
            </td>
          </tr>
          <tr>
            <td><i class="fas fa-lock"></i>
              <input type="password" class="logininput" name="password" placeholder="Password..">
            </td>
          </tr>
          <tr>
            <td align="center" colspan="2" class="loginsection"><input type="submit" class="loginbutton" name="login" value=Login></td>
          </tr>
        </div>
      </body>
      </html>
