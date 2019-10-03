<?php
session_start();
error_reporting(E_ALL);
require_once('hub/hubUtil/Error_handle.php');

ini_set('display_errors', 0);
require_once("hub/Hub.php");

/*TODO Uprawnienia
 * każdy użytkownik, zamiast poziomu ma nadaną nazwę schematu uprawnień
 * np root ma:
 * root
 * I teraz istnieje tabela z uprawnieniami, gdzie zdefiniowane są nazwy powiązane z id uprawnień
 * |root|1,2,3,4,5,6,7|
 * I teraz, np 1 to edytowanie treści stron
 * 2 to dodawanie nowej strony(i pewnie usuwanie)
 * 3 to...usuwanie użytkownika innego niż ten który usuwa ale tylko innych użytkowników
 * 4 to usuwanie moderatorów i użytkowników
 * 5 to usuwanie adminów
 * root jest nieusuwalny i ma wszystkie uprawnienia
 * Chociaż poziom też się przyda, np każda strona będzie miała poziom użytkownika który będzie mógł lub nie się na nią
 * dostać.
 * Tak więc strona /admin będzie wymagać uprawnień poziomu 1
 * root - poziom 0;
 * Jeżeli uzytkownik ma poziom 0 to może dostać się na wszystkie powyższe poziomy. Jeżeli ma jeden to na wszystkie >= 1
 * */

Hub::get(); //init

Hub::get()->DomBuilder->meta("viewport", "width=device-width");
Hub::get()->DomBuilder->setCharset("UTF-8");
Hub::get()->DomBuilder->setHome(Hub::get()->Url->getPrefix());

Hub::get()->DomBuilder->embedStyle('https://fonts.googleapis.com/css?family=Roboto');
Hub::get()->DomBuilder->embedStyle('http://fonts.googleapis.com/css?family=Advent+Pro&subset=latin,latin-ext');
//Hub::get()->DomBuilder->embedStyle('https://use.fontawesome.com/releases/v5.0.6/css/all.css');
Hub::get()->DomBuilder->addStyle('console');
//Hub::get()->DomBuilder->addStyle('main');
Hub::get()->DomBuilder->addStyle('jquery-ui');
Hub::get()->DomBuilder->addStyle('common');
Hub::get()->DomBuilder->addStyle('content-tools/content-tools.min');
Hub::get()->DomBuilder->addStyle('content-tools/alignments');
Hub::get()->DomBuilder->addStyle('plugins/jBox.all');
Hub::get()->DomBuilder->addStyle('plugins/jBox.Notice');
Hub::get()->DomBuilder->addStyle('switch');
Hub::get()->DomBuilder->addStyle('CustomSelect');


Hub::get()->DomBuilder->generateInfoScript(Hub::get()->DomBuilder->getInfoScript());
Hub::get()->DomBuilder->addScript('jquery');
Hub::get()->DomBuilder->addScript('jquery-ui');
Hub::get()->DomBuilder->addScript('common'); //common script; runs all extensions
Hub::get()->DomBuilder->addScript('Element'); //common script; runs all extensions
Hub::get()->DomBuilder->addScript('switch'); //common script; runs all extensions
Hub::get()->DomBuilder->addScript('console'); //console expanding, dragging
Hub::get()->DomBuilder->addScript('plugins/jBox.all.min');
Hub::get()->DomBuilder->addScript('plugins/jBox.Notice.min');
Hub::get()->DomBuilder->addScript('modules/CustomSelect');

Hub::get()->DomBuilder->embedScript('https://use.fontawesome.com/releases/v5.0.6/js/all.js'); //custom font icons

//Hub::get()->DomBuilder->addScript('content-tools/content-tools.min', false);
if (Hub::get()->Action->validateAction(Actions::CHANGE_PAGE_CONTENT)) {
    Hub::get()->DomBuilder->addScript('content-tools/content-tools', false);
    Hub::get()->DomBuilder->addScript('content-tools/editor', false);
}

$rest = Hub::get()->Rest->getInstance();

Hub::get()->RoutingTable->registerRoutes($rest);

$rest->get('#db_check#i', function () {
    Hub::deployment();
    exit();
});

$rest->get('#root/*#i', function () {
    $url = 'root';
    Page::standardLoad($url);
});

$rest->get('#(?<pageId>.*)?#i', function ($id) {
    $url = explode('?', $id['pageId'])[0];
    Hub::get()->Url->setCurrentLocation($url);
    Page::standardLoad($url);
});



$rest->setMethod(Hub::get()->Request->getType());
$rest->setUrl(Hub::get()->Request->getRestUrl());

$rest->parse();

if (isset($_SESSION['debug']) and $_SESSION['debug'] == 1 and !Hub::get()->DomBuilder->inJsonMode()) {
    Hub::get()->DomBuilder->body(Hub::get()->Util->getLogConsole(Hub::get()->getLogs())); //debug console
}

if(Hub::get()->Action->validate(Actions::CHANGE_PAGE_STRUCTURE) and Page::$current != 'root'){
    Hub::get()->DomBuilder->addScript('structureEditor/editor');
    Hub::get()->DomBuilder->addStyle('structureEditor/editor');
}

echo(Hub::get()->DomBuilder->build());