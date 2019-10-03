<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 14.08.2018
 * Time: 22:13
 */

class FormData{

    private $missing;
    private $data;

    function __construct($data = null, $missing = []) {
        $this->missing = $missing;
        $this->data = $data;
    }

    public function isComplete(){
        return (count($this->missing) == 0);
    }

    public function getMissing(){
        return $this->missing;
    }

    public function getData(){
        return $this->data;
    }
}