<?php

require 'vendor/autoload.php';
require 'app/APIBase/APIBase.php';

$dbUser = "root";
$dbPass = "";
$dbHost = "localhost";
$dbName = "apibase";

$APIBase = new \APIBase\APIBase($dbHost, $dbName, $dbUser, $dbPass);

$APIBase->enable();
