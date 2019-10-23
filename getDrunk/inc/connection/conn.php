<?php
if (!defined("MAGICKEY")) {
    exit("MAGICKEY was not defined");
}

if (MAGICKEY == "ugugUGu221KHJBD84") {

    $host = "localhost";
    $password = "GfkQOv0xe8V5vHgnFj";
    $username = "u534907271_x";
    $database = "u534907271_barpulsen";

    $conn = new mysqli($host, $username, $password, $database);

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