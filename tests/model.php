<?php
session_start();

require_once('../hub/hubUtil/Error_handle.php');

ini_set('display_errors', 0);
require_once("../hub/Hub.php");

Hub::get(); //init

/* @var \models\Pages $page*/
$page = \models\Pages::find(['id' => 'home'])->one();
foreach ($page->language_versions as $version){
    printr($version->getColumnAssoc());
    $version->header_text = "<p>Home page</p>";
    $version->save();
}
