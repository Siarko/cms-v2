<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 22.06.2017
 * Time: 12:57
 */

require_once(__DIR__."/../../hub/Constants.php");

class Db {
    /* @var Mysqli $handle*/
    protected static $handle = null;
    protected static $critERR;
    protected static $closed = false;

    /**
     * @param $result mysqli_result
     */
    public static function fetch_all_assoc($result){
        $array = array();
        while($row = $result->fetch_assoc())
            $array[] = $row;
        return $array;
    }


    public function init(){
        if(self::$handle){return;} //only one mysqli instance
        self::$critERR = false;
        self::$handle = new mysqli(
            Constants::getDbHost(),
            Constants::getDbUserName(),
            Constants::getDbPassword(),
            Constants::getDbName()
        );

        if(mysqli_connect_errno()){
            self::$critERR = true;
            Constants::logCrit("Błąd w łączeniu z bazą danych!", "Skontaktuj się z adminem ;)");
        }else{
            self::$handle->set_charset("utf8");
        }
    }

    function __destruct(){
        if(!self::$critERR and !self::$closed){
            self::$closed = true;
            self::$handle->close();
        }
    }

    protected  function filter($string){
        return self::$handle->escape_string($string);
    }

    /**
     * @param $query string
     * @return bool|mysqli_result|null
     */
    public function query($query){
        if($result = self::$handle->query($query)){
            if(self::$handle->error != null){
                Hub::get()->log('Query error: '.self::$handle->error, LogLevel::ERR);
                Hub::get()->log('SQL: '.$query, LogLevel::ERR);
            }
            return $result;
        }else{
            Hub::get()->log("Database error: ".self::$handle->error, LogLevel::ERR);
            Hub::get()->log("SQL EXECUTED: ".$query, LogLevel::ERR);
            return null;
        }
    }

    public function exists($field, $id, $table){
        $id = $this->filter($id);
        $table = $this->filter($table);
        $field = $this->filter($field);
        $response = $this->query("SELECT EXISTS(SELECT * FROM ".$table." WHERE ".$field."='".$id."') as 'exists'");
        if(!$response){
            return false;
        }
        return $response->fetch_assoc()['exists'];
    }

    public function tableExists($name){
        $response = $this->query("SELECT * FROM information_schema.tables WHERE
            table_schema = '".Constants::getDbName()."' AND table_name = '".$name."' LIMIT 1;");
        return $response->num_rows;
    }

    /**
     * @param string $table table from witch row will be removed
     * @param string $field field to compare
     * @param string $equals value compared with field
     */
    public function removeRow($table, $field, $equals){
        $response = $this->query("REMOVE FROM ".$table." WHERE ".$field."='".$equals."'");
        Hub::get()->log("Remove response: ".print_r($response->fetch_assoc(), false));
    }

    public function getContext(){
        return self::$handle;
    }

    /* Database reconstruction, build DB from scratch*/
    public function buildDbFromScratch($overwrite = false){
        /* TODO przywracanie struktury bazy z pliku .sql*/
        if($overwrite){

        }else{

        }
    }

}

Hub::get()->Db; //init