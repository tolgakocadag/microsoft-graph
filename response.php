<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

$mysql = [
    "host" => "35.195.74.217",
    "dbname" => "microsoft_graphs",
    "username" => "spechy",
    "password" => "spca1b2c3"
];

define("MYSQL", $mysql);

try {
    $sql = new PDO('mysql:dbname='.MYSQL['dbname'].';host='.MYSQL['host'].';charset=utf8',''.MYSQL['username'].'',''.MYSQL['password'].'',array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    $sql->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
    $sql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
    $sql->bConnected = true;
} catch(PDOException $e) {
    echo $e->getMessage();
    die();
}

define("SQL",$sql);

