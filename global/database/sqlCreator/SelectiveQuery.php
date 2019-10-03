<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 10.08.2018
 * Time: 17:34
 */

namespace sqlCreator;

class SelectiveQuery extends \ConditionedQuery {

    protected $selectedColumns = [];
    protected $originPath = '';

    function __construct($object = null) {
        if($object === null){
            return;
        }
        if($object instanceof \Select){
            $this->tables = $object->tables;
            $this->selectedColumns = $object->selectedColumns;
        }
        if($object instanceof \Show){
            $this->tables = $object->tables;
            $this->selectedColumns = $object->selectedColumns;
        }
        $this->extendOriginPath($object);
    }

    private function extendOriginPath($object){
        $this->originPath .= ((strlen($this->originPath) != 0)?'.':'').get_class($object);
    }

    protected function originatedFrom($path){
        return $this->originPath == $path;
    }


    function __toString() {
        if($this->originatedFrom('Select')){
            $string = 'SELECT ';
            $string .= $this->arrayToList($this->selectedColumns);
            $string .= 'FROM ';
            $string .= $this->arrayToList($this->tables);
            if(!$this->isConditionEmpty()){
                $string .= $this->createConditionString();
            }
            $string .= $this->getLimitSql().$this->getOffsetSql();
            return $string;
        }
        if($this->originatedFrom('Show')){
            $string = 'SHOW ';
            $string .= $this->arrayToList($this->selectedColumns);
            $string .= 'FROM ';
            $string .= $this->arrayToList($this->tables);
            if(!$this->isConditionEmpty()){
                $string .= $this->createConditionString();
            }
            $string .= $this->getLimitSql().$this->getOffsetSql();
            return $string;
        }

        printr($this->originPath);

        throw new \NoOriginException($this->originPath);

    }

    public function parse(){
        return $this->__toString();
    }


}