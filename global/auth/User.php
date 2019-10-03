<?php
/*Depends: UserDb, SettingsDb*/
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 27.06.2017
 * Time: 00:07
 */


class User {

    /*
     * @var string $name
     * @var string $passHash
     * @var number $perms
     *
     * @var boolean $banned
     * */
    private $name;
    private $passHash;
    private $perms = 6;
    private $creationDate;
    private $banned;
    private $anonymous = false;


    public function getNew() {
        return new User();
    }

    public function getByName($name){

    }

    public function getAllUsers() {
        return Hub::get()->UserDb->getAll();
    }

    public function loadLogged($name) {
        $user = $this->getNew();
        $ret = Hub::get()->UserDb->getUser($name);
        if ($ret !== null) {
            $user->loadDataFromAssoc($ret);
            Hub::get()->logIf(
                Messages::format(Messages::$LOGGED_AS_USER, $name),
                LogLevel::INFO, Constants::URL_UTILS
            );
            return $user;
        }
        Hub::get()->log(
            Messages::format(Messages::$TRYING_TO_LOAD_NON_E_USER, $name),
            LogLevel::ERR
        );
        return null;
    }

    public function getAnonymous() {
        $usr = $this->getNew();
        $usr->anonymous = true;
        $usr->name = "Anonim";
        $usr->perms = Hub::get()->SettingsDb->getSetting('anonymousUserPerm');
        $usr->banned = false;
        Hub::get()->logIf(Messages::$LOGGED_AS_ANONYMOUS,
            LogLevel::INFO, Constants::LOGGED_USER_INFO
        );
        return $usr;
    }

    /**
     * @param Page $page
     * @return bool
     */
    public function isPermitted($page) {
        return $this->perms <= $page->getPermLevel();
    }

    public function loadDataFromAssoc($assoc) {
        $this->name = $assoc['name'];
        $this->creationDate = $assoc['date'];
        $this->perms = $assoc['permissions'];
        $this->banned = $assoc['banned'];
    }

    public function login($updateAuth = true) {
        /* @var array $dbResponse
         * @var User $user
         */

        /*TODO W dbResponse może znaleźć się null jeżeli nie nastąpiło logowanie*/
        $dbResponse = Hub::get()->UserDb->login($this);
        if ($dbResponse) {
            $_SESSION[Constants::SESSION_LOGGED_KEY] = $dbResponse['name'];
            if ($updateAuth) {
                Hub::get()->Auth->update();
            }
            /*TODO przekierowanie po zalogowaniu, chyba*/
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        }
        return $dbResponse;
    }

    public function logout($updateAuth = true) {
        Hub::get()->Auth->clearSessionKey();
        if ($updateAuth) {
            Hub::get()->Auth->update();
        }
    }

    public function isLogged() {
        return @$_SESSION[Constants::SESSION_LOGGED_KEY] !== null;
    }

    public function create($name, $pass, $perms = 3) {
        $this->name = $name;
        $this->passHash = $pass;
        $this->perms = $perms;
    }

    public function createInDb() {
        if (!Hub::get()->UserDb->exists($this->name)) {
            $this->passHash = $this->hash($this->passHash);
            return Hub::get()->UserDb->create($this->name, $this->passHash, $this->perms);
        } else {
            return null;
        }
    }

    public function delete($name = null){
        if($name == null){
            $name = $this->name;
            $this->logout();
        }
        return Hub::get()->UserDb->deleteUser($name);
    }

    public function isAnonymous(){
        return $this->anonymous;
    }

    public function setBan($state) {
        $this->banned = $state;
        Hub::get()->UserDb->update($this); //not sure what I wanted to do. Update whole user?
    }

    public function isBanned() {
        return $this->banned;
    }

    protected function hash($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public function modify($userId){
        $result = [];
        $post = Hub::get()->Request->getPost();
        $post->ifContains('user_permlevel', function($value) use ($userId, &$result){
            $result['user_change_perm_level'] = Hub::get()->UserDb->changePermLevel($userId, $value);
        });
        $post->ifContains('user_name', function($value) use ($userId, &$result){
            $result['user_change_id'] = Hub::get()->UserDb->changeId($userId, $value);
            $result['user_new_id'] = $value;
        });
        return $result;
    }

    /**
     * @param $page Page
     * @return bool
     */
    public function canAccess($page){
        return ($page->getPermLevel() >= $this->getPerms());
    }

    public function canAccessLevel($pageLevel){
        return ($pageLevel >= $this->getPerms());
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getPassHash() {
        return $this->passHash;
    }

    /**
     * @return mixed
     */
    public function getPerms() {
        return $this->perms;
    }

    /**
     * @return mixed
     */
    public function getCreationDate() {
        return $this->creationDate;
    }


}