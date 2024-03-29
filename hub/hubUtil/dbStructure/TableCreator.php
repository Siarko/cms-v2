<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 14.05.2019
 * Time: 12:28
 */

namespace hub\hubUtil\dbStructure;

abstract class TableCreator {

    /**
     * @return null|string
     */
    public function getTableName(){
        return null;
    }

    /**
     * @return array
     */
    public abstract function getTableStructure();

    /**
     * @return null|array
     */
    public function getTableValues(){
        return null;
    }
}