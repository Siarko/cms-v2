<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 16.03.2018
 * Time: 22:58
 */

class AbstractObject {

    private $_properties = [];

    public function getInstance(){
        return new AbstractObject();
    }

    public function ifContains($name, $function){
        if(array_key_exists($name, $this->_properties)){
            $function($this->$name);
        }
    }

    function __set($name, $value) {
        $this->_properties[$name] = $value;
    }

    function fill($array){
        $this->_properties = array_merge($this->_properties, $array);
    }

    /**
     * @param string $property
     */
    public function exists($property){
        return key_exists($property, $this->_properties);
    }

    function __get($name) {
        if($this->exists($name)){
            return $this->_properties[$name];
        }else{
            return null;
        }
    }
}