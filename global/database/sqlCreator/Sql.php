<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 10.08.2018
 * Time: 21:27
 */

class Sql {

    public static function select($columns){
        return new Select($columns);
    }

    public static function insert($data, $mode = Insert::MODE_BY_COLUMN){
        return new Insert($data, $mode);
    }

    public static function delete(){
        return new Delete();
    }

    public static function update($tables){
        return new Update($tables);
    }

    public static function show($what){
        return new Show($what);
    }
}

Hub::get()->Sql;