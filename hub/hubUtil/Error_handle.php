<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 12.10.2017
 * Time: 00:17
 */

$v = explode('.', phpversion());
if($v[0] < 5 or ($v[0] == '5' and $v[1] < 6)){
    echo("PHP version: ".phpversion().' too low. At least 5.6 required.');
    exit();
}

require_once(__DIR__."/../Constants.php");

function getErrorTypeName($id){
    if($id == E_ERROR){
        return 'Fatal run-time error';
    }
    if($id == E_WARNING){
        return 'Warning';
    }
    if($id == E_PARSE){
        return 'Fatal parse error';
    }
    if($id == E_NOTICE){
        return 'Just a notice...';
    }
    if($id == E_STRICT){
        return 'CODE IS SHITTY TO EXTREME';
    }
    return "UNKNOWN(".$id.")";
}

function CMS_ERROR_HANDLE($errno, $errstr, $errfile, $errline, array $errcontext){
    if($errno == E_NOTICE){return;}
    $file = explode('\\', $errfile);
    $file = $file[count($file)-1];
    if(!Hub::isErrMuted()){
        echo("Error (".getErrorTypeName($errno).") has occured at line ".$errline." in ".$file." :/<br/>");
        echo("Error msg: ".$errstr."<br/>");
    }
}
function CMS_FATAL_HANDLE(){
    $err = error_get_last();
    if(!is_int($err['type'])){return;}
    $aditionalMessage = '';
    $words = explode(' ', $err['message']);
    if($words[0] == 'Class' and $words['2'] == 'not' and $words['3'] == 'found'){
        $aditionalMessage = 'Klasa '.$words[1].' nie znaleziona, załadowane klasy:<br/>';
        $aditionalMessage .= print_r(Hub::$loadedList, true);
    }
    Constants::logCrit(
        'ERR! type: '.getErrorTypeName($err['type']),
        'No to klops..',
        $err['message']."<br/>File: ".$err['file']."<br/>Line: ".$err['line']."<br/>".$aditionalMessage);
}
register_shutdown_function("CMS_FATAL_HANDLE");
set_error_handler('CMS_ERROR_HANDLE', E_ALL);
