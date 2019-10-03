<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 01.08.2017
 * Time: 10:06
 */

class PagePartDb extends Db{

    public function init(){
        if(!$this->tableExists(Constants::$PAGEPARTS_TABLE)){
            Hub::get()->log("Table Pageparts does not exist!", LogLevel::ERR);
        }
    }

    public function exists($id , $a = null, $b = null){
        $id = $this->filter($id);
        $e = parent::exists('id', $id, 'pageparts');
        return $e;
    }

    public function getPart($id){
        if(!$this->exists($id)){
            Hub::get()->log("Requested page part doesn't exist!", LogLevel::WARN);
            return null;
        }
        $id = $this->filter($id);
        $response = $this->query("SELECT htmlFile, permlevel FROM pageparts WHERE id='".$id."'");
        $response = $response->fetch_assoc();
        if($response['htmlFile'] == null){
            $response['htmlFile'] = $id.'.php';
        }elseif(!isset(pathinfo($response['htmlFile'])['extension'])){
            $response['htmlFile'] .= '.php';
        }
        return [$response['htmlFile'], $response['permlevel']];
    }
} 