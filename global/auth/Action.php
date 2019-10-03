<?php

/*Depends: User, Auth*/

/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 02.07.2017
 * Time: 13:39
 */

abstract class Actions{
    const CHANGE_PAGE_CONTENT = UserLevel::ADMIN;
    const CHANGE_PAGE_STRUCTURE = UserLevel::ADMIN;
    const UPLOAD_FILE = UserLevel::ADMIN;
    const GET_UPLOADED_LIST = UserLevel::ADMIN;
    const ADD_NEW_PAGE = UserLevel::ADMIN;
    const MODIFY_MENU_LINK = UserLevel::ADMIN;
    const DEBUG_PAGE = UserLevel::ADMIN;
    const CAN_LIST_USERS = UserLevel::ADMIN;
    const CAN_LIST_PERM_LEVELS = UserLevel::ADMIN;
    const CAN_MODIFY_USERS = UserLevel::ADMIN;
    const CAN_DELETE_USERS = UserLevel::ADMIN;
    const RENAME_FILES = UserLevel::ADMIN;
    const DELETE_FILES = UserLevel::ADMIN;
}

class Action {

    /**
     * @param $action int
     * @param $user User
     * @return bool
     */
    public function validateAction($action, $user = null){
        return $this->validate($action, $user);
    }

    public function validate($action, $user = null){
        if(!$user){
            $user = Hub::get()->Auth->getLogged();
        }
        return ($user->getPerms() <= $action);
    }
}
/*
  * Walidacja akcji:
  * validate(Actions::CHANGE_PAGE_CONTENT, $jakisUser);
  * */
