<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 13.04.2019
 * Time: 18:05
 */

class Console {
    private $constructor;

    /* @var JavaScript $scriptConstructor*/
    function __construct($scriptConstructor) {
        $this->constructor = $scriptConstructor;
    }

    public function log(...$values){
        $this->constructor->_SCOPE_VARIABLES[$this->constructor->methodScope] = [
            'type' => 'method',
            'lines' => [
                'console.log("'.$values[0].'")'
            ]
        ];
    }
}