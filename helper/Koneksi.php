<?php

$host = 'localhost';
$username = 'root';
$password = '123456';
$database = 'amit_db';

try {
   
    $db = mysqli_connect($host, $username, $password, $database);
    $db->set_charset('utf8mb4');
} catch (\Throwable $th) {
    throw $th;
}

