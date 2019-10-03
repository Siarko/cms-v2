<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 25.05.2019
 * Time: 23:43
 */

class Stack {

    private $values = [];

    public function push($value){
        $this->values[] = $value;
    }

    public function pop(){
        $index = count($this->values)-1;
        $v = $this->values[$index];
        unset($this->values[$index]);
        return $v;
    }
}