<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 16.03.2018
 * Time: 22:17
 */

class PermLevels {

    public function listAll(){
        $response = Hub::get()->PermLevelsDb->getAll();
        return Db::fetch_all_assoc($response);
    }
}