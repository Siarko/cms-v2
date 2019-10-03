<?php

/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 18.08.2017
 * Time: 12:35
 */
class Variables {

    private $variables;

    public function init(){
        $this->variables = [];
    }

    function __set($name, $value){
        $this->variables[$name] = $value;
    }

    function __get($name){
        return $this->variables[$name];
    }

    public function _getVars(){
        return $this->variables;
    }

}