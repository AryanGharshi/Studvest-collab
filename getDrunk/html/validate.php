<?php
ini_set('display_errors',1); error_reporting(E_ALL);

define("MAGICKEY", "ugugUGu221KHJBD84");
require "../inc/connection/conn.php";


#The password hash
$hash = '$2y$10$0wRmEjEdLvOkUo96G7k8AeNjwuV8/6p/0bt2Al7USJmZ1RW7yo5x.';


session_set_cookie_params(0);
session_start();
session_regenerate_id();


#If session not set, start session
if(!isset($_SESSION['username'])) {
  $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
  $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
  $stmt = $conn->prepare("SELECT * FROM adminlogin");
  $stmt->execute();
  $result = $stmt->get_result();
  $users = $result->fetch_all(MYSQLI_BOTH);


  foreach($users as $user) {
      #Checks if username and password match
      if(($user['username'] == $username) &&
          ($user['password'] == password_verify($password, $hash))) {
            header("Location: inputForm.php");
            $_SESSION['username'] = $username;
            $_SESSION['login_time'] = time();
        } else {
          #Send error message if username or password dont match
          echo "<script> alert('Wrong username or password.');
          window.location.href='login.php';
          </script>";
          die();
    }
  }
}


#Checks if session is set
if(isset($_SESSION['username'])) {
  header("Location: inputForm.php");
}


?>
