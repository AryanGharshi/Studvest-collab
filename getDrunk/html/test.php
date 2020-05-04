<?php
    echo $_POST['section'];
?>

<form action="" method='post'>
    <input type="hidden" name="section" value="bar"><br>
    <label for="fname">First name:</label><br>
    <input type="text" id="fname" name="fname"><br>
    <label for="lname">Last name:</label><br>
    <input type="text" id="lname" name="lname"><br><br>
    <input type="submit" name="submit_del" value="del">
    <input type="submit" name="submit_regi" value="regi">
</form>