<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 28.01.2018
 * Time: 21:20
 */

class TemplateUtils {

    public function getTextInClosures($text, $closurename, $remove = false){
        preg_match("'\{".$closurename."\}(.*?)\{".$closurename."\}'si", $text, $result);
        if($remove){
            preg_replace("'\{".$closurename."\}(.*?)\{".$closurename."\}'si", '', $text);
        }
        return $result;
    }

    public function replaceTextInClosures($text, $closurename, $replacement){
        return preg_replace("'\{".$closurename."\}(.*?)\{".$closurename."\}'si", $replacement, $text);
    }

}