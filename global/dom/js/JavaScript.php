<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 13.04.2019
 * Time: 17:45
 */

class JavaScript {

    public $_SCOPE_VARIABLES = [];

    public $console;
    public $methodScope = null;

    function __construct() {
        $this->console = new Console($this);
    }

    function __set($name, $value) {
        if(is_callable($value)){
            $this->methodScope = $name;
            $value('');
            $this->methodScope = null;
        }else{
            $this->_SCOPE_VARIABLES[$name] = [
                'type' => gettype($value),
                'value' => $value
            ];
        }
    }


    private function constructVariable($name, $data){
        $parsedValue = '';
        if($data['type'] == 'boolean'){
            $parsedValue = ($data['value']?'true':'false').';';
        }
        if($data['type'] == 'string'){
            $parsedValue = '"'.$data['value'].'";';
        }
        if($data['type'] == 'integer'){
            $parsedValue = $data['value'].';';
        }
        if($data['type'] == 'array'){
            $parsedValue = json_encode($data['value']).';';
        }
        if($data['type'] == 'method'){
            $methodLines = '';
            foreach ($data['lines'] as $line) {
                $methodLines .= "\t".$line.";\n";
            }
            $parsedValue = "function(){\n".$methodLines."};\n";
        }
        return 'let '.$name.' = '.$parsedValue."\n";
    }

    public function parse(){
        $text = '';

        foreach ($this->_SCOPE_VARIABLES as $name => $data) {
            $text .= $this->constructVariable($name, $data);
        }

        return $text;
    }

}