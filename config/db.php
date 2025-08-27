<?php

$host = 'localhost';
$user = 'root';
$pass = '';

$bomDb = '1_bom';
$hrisDb = '1_hris';

$bomMysqli = new mysqli($host, $user, $pass, $bomDb);

$hrisMysqli = new mysqli($host, $user, $pass, $hrisDb);

if ($bomMysqli->connect_error) {
    exit('BOM Database connection failed: ' . $bomMysqli->connect_error);
}

if ($hrisMysqli->connect_error) {
    exit('HRIS Database connection failed: ' . $hrisMysqli->connect_error);
}
