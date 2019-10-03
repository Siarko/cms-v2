<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 04.08.2017
 * Time: 21:05
 */

class SettingsDb extends Db{

    /*
     * Check if settings table exists in database
     * if not, generate error*/


    public function init(){
        if(!$this->tableExists(Constants::$SETTINGS_TABLE)){
            Hub::get()->log("Settings table does not exist!", LogLevel::ERR);
        }
    }

    public function exists($name, $a = null, $b = null){
        $name = $this->filter($name);
        return parent::exists('name', $name, Constants::$SETTINGS_TABLE);
    }

    public function getSetting($name){
        $response = $this->query("SELECT value FROM ".Constants::$SETTINGS_TABLE." WHERE name='".$name."'");
        if($response->num_rows == 1){
            return $response->fetch_assoc()['value'];
        }else{
            Hub::get()->log("Tried to fetch non-existing setting!<br>".$name, LogLevel::WARN);
            return null;
        }
    }

    public function saveSetting($name, $value){
        $name = $this->filter($name);
        $value = $this->filter($value);
        if($this->exists($name)){
            $this->query('UPDATE '.Constants::$SETTINGS_TABLE.' SET value="'.$value.'" WHERE name="'.$name.'"');
        }else{
            $this->query('INSERT  INTO '.Constants::$SETTINGS_TABLE.'(name, value) VALUES( "'.$name.'","'.$value.'")');
        }
    }

    public function removeSetting($name){
        $name = $this->filter($name);
        if($this->exists($name)){
            $this->query('DELETE FROM '.Constants::$SETTINGS_TABLE.' WHERE name="'.$name.'"');
        }
    }
} 