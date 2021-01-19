<?php
session_start();
if(isset($_SESSION['username'])){

  session_regenerate_id();
  session_unset();
  session_destroy();

  echo "<script>location.href='login.php'</script>";

} else {

  echo "<script>location.href='login.php'</script>";
}
?>
