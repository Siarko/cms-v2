<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 01.07.2017
 * Time: 14:35
 */

class TemplateDb extends Db{


    public function init(){
        if(!$this->tableExists(Constants::$TEMPLATES_TABLE)){
            Hub::get()->log("Table templates does not exist!", LogLevel::ERR);
        }
    }

    public function get($id){
        $id = $this->filter($id);
        $ret = $this->query("SELECT htmlFile FROM templates WHERE id='".$id."'");
        if($ret !== null){
            $templateFile = $ret->fetch_assoc()['htmlFile'];
            if(!isset(pathinfo($templateFile)['extension'])){
                $templateFile .= '.php';
            }
            return $templateFile;
        }else{
            Hub::get()->log("Cannot load page template! id=".$id, LogLevel::ERR);
            return null;
        }
    }
} 