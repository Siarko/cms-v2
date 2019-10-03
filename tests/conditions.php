<?php

use database\sqlCreator\Condition;

session_start();

require_once('../hub/hubUtil/Error_handle.php');

ini_set('display_errors', 0);
require_once("../hub/Hub.php");

Hub::get(); //init

$query = (new Update('table'))->set(['a'], ['b'])->where([
    'a' => 'av'
]);

echo($query->parse());