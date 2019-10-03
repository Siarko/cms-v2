<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 16.03.2018
 * Time: 22:15
 */

class PermLevelsDb extends Db{

    public function getAll(){
        return $this->query("SELECT * FROM user_perm_levels");
    }

}