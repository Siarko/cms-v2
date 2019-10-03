<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 28.01.2018
 * Time: 23:36
 */

class Vec {

    public $x, $y;

    public function __construct($x, $y) {
        $this->x = $x;
        $this->y = $y;
    }

    public function copy(){
        return new Vec($this->x, $this->y);
    }

    public function getLen(){
        return sqrt(pow($this->x, 2) + pow($this->y, 2));
    }

    public function normalize(){
        $len = $this->getLen();
        return new Vec($this->x/$len, $this->y/$len);
    }

    public function rotate($theta, $ox = 0, $oy = 0){
        $nx = $this->x * cos($theta) - $this->y * sin($theta);
        $ny = $this->y * cos($theta) + $this->x * sin($theta);

        $this->x = $nx;
        $this->y = $ny;
        return $this;
    }
}