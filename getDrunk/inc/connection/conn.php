<?php
if (!defined("MAGICKEY")) {
    exit("MAGICKEY was not defined");
}

if (MAGICKEY == "ugugUGu221KHJBD84") {

    $host = "localhost";
    $password = "XxlGUEQhle";
    $username = "admin_bar";
    $database = "admin_bar";

    $conn = new mysqli($host, $username, $password, $database);
    $conn -> set_charset("utf8");

    if ($conn->connect_error) {
        die("connection failed: " . $conn->connect_error);
    }

    //input check tool
    function check($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
}