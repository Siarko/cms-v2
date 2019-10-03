<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 11.08.2018
 * Time: 23:18
 */

class Delete extends ConditionedQuery {

    use FromTableSelector;

    public function parse() {
        $sql = 'DELETE FROM ';
        $sql .= $this->arrayToList($this->tables);
        if(!$this->isConditionEmpty()){
            $sql .= $this->createConditionString();
        }
        return $sql;
    }
}