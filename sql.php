<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once "./defined.php";

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