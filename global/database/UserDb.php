<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 27.06.2017
 * Time: 00:19
 */

class UserDb extends Db{

    public function init(){
        if(!$this->tableExists(Constants::$USERS_TABLE)){
            Hub::get()->log("Table Users does not exist!", LogLevel::ERR);
        }
    }

    public function getAll(){
        return Db::fetch_all_assoc($this->query("SELECT u.name, permissions, date, pl.name as permname FROM users u JOIN user_perm_levels pl ON u.permissions = pl.id"));
    }

    public function exists($name, $a = null, $b = null){
        $name = $this->filter($name);
        $res = $this->query("SELECT name FROM users WHERE name='".$name."'");
        return $res->num_rows == 1;
    }

    public function create($name, $passHash, $perms){
        $name = $this->filter($name);
        $passHash = $this->filter($passHash);
        $user = new \models\Users();
        $user->name = $name;
        $user->passhash = $passHash;
        $user->permissions = $perms;
        $user->banned = 0;
        $user->deleted = 0;

        return $user->save();
    }

    public function getUser($nick){
        $nick = $this->filter($nick);
        $ret = $this->query("SELECT name, passhash, permissions, banned, date FROM users WHERE name='".$nick."'");
        if($ret->num_rows == 1){
            return $ret->fetch_assoc();
        }else{
            return null;
        }
    }

    public function deleteUser($name){
        $name = $this->filter($name);
        $response = null;
        if($this->exists($name)){
            $response = $this->query("DELETE FROM ".Constants::$USERS_TABLE." WHERE name='".$name."'");
            if($response){
                $response = AjaxResponse::SUCCESS;
            }else{
                $response = AjaxResponse::MYSQL_ERROR;
            }
            Hub::get()->log("Delete user: ".$response);
        }else{
            $response = AjaxResponse::USER_NOT_FOUND;
            Hub::get()->log("Trying to remove non-existing user!", LogLevel::ERR);
        }
        return $response;
    }

    public function changeId($name, $newName){
        $name = $this->filter($name);
        $newName = $this->filter($newName);
        if($this->exists($name)){
            $response = $this->query("UPDATE ".Constants::$USERS_TABLE." SET name='".$newName."' WHERE name='".$name."'");
            if($response){
                $result = AjaxResponse::SUCCESS;
            }else{
                $result = AjaxResponse::MYSQL_ERROR;
            }
        }else{
            $result = AjaxResponse::USER_NOT_FOUND;
        }
        return $result;

    }
    public function changePermLevel($name, $permlevel){
        $name = $this->filter($name);
        $permlevel = $this->filter($permlevel);
        if($this->exists($name)){
            $response = $this->query("UPDATE ".Constants::$USERS_TABLE." SET permissions='".$permlevel."' WHERE name='".$name."'");
            if($response){
                $result = AjaxResponse::SUCCESS;
            }else{
                $result = AjaxResponse::MYSQL_ERROR;
            }
        }else{
            $result = AjaxResponse::USER_NOT_FOUND;
        }
        return $result;

    }

    /**
     * @param User $u
     * @return array|bool|mysqli_result|null
     */
    public function login($u){

        $user = $this->getUser($u->getName());
        if($user != null){
            $valid = password_verify($u->getPassHash(), $user['passhash']);
            if($valid){
                return $user;
            }else{
                Hub::get()->log("User password invalid", LogLevel::ERR);
                return null;
            }
        }else{
            Hub::get()->log("User not exists", LogLevel::ERR);
            return null;
        }
    }
} 