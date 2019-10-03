<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 12.08.2018
 * Time: 01:13
 */

namespace models;

/**
 * @property string $name
 * @property string $passhash
 * @property int $permissions
 * @property string $date
 * @property int $banned
 * @property int $deleted
 * @property string $mail
 * */
class Users extends ModelClass {

    const NAME = 'name';
    const PASSHASH = 'passhash';
    const PERMISSIONS = 'permissions';
    const DATE = 'date';
    const BANNED = 'banned';
    const DELETED = 'deleted';
    const MAIL = 'mail';

    public static function getTableName() {
        return 'users';
    }

    public static function getColumns() {
        return [
            self::NAME,
            self::PASSHASH,
            self::PERMISSIONS,
            self::DATE,
            self::BANNED,
            self::DELETED,
            self::MAIL
        ];
    }


}