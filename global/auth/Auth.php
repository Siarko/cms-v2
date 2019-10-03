<?php
/*Depends: Variables, User*/
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 22.06.2017
 * Time: 12:40
 */

abstract class PermLevel{
    const levels = ['root','admin','moderator','użytkownik', 'użytkownik nieaktywny', 'anonim'];

    public static function getName($level){
        return PermLevel::levels[$level-1];
    }
}

class Auth {

    /* @var User $loggedUser*/
    private $loggedUser = null;
    
    
    public function init(){
        $this->update();
        $this->loadVariables();
    }

    /**
     * Ładuje 'zmienne' które mogą być później wywołane w Template albo PageParts
     */
    private function loadVariables(){
        Hub::get()->Variables->loggedUser = $this->loggedUser->getName();
        Hub::get()->Variables->loggedUserLevel = $this->loggedUser->getPerms();
    }

    private function getSessionKey(){
        return @$_SESSION[Constants::SESSION_LOGGED_KEY];
    }

    public function clearSessionKey(){
        unset($_SESSION[Constants::SESSION_LOGGED_KEY]);
    }

    public function update(){
        $logged = $this->getSessionKey();
        if($logged !== null){
            $this->loggedUser = Hub::get()->User->loadLogged($logged);
            if(!$this->loggedUser){
                $this->loggedUser = Hub::get()->User->getAnonymous();
                $this->clearSessionKey();
            }
        }else{
            $this->loggedUser = Hub::get()->User->getAnonymous();
        }
    }

    public function getLogged(){
        return $this->loggedUser;
    }
} 