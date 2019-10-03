<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 11.03.2019
 * Time: 16:05
 */

namespace models;

/**
 * @property int id
 * @property string code
 * @property string name
 * @property string image_path
 * @property int active
 * */
class Languages extends ModelClass {

    public static $_ID = 'id';
    public static $_CODE = 'code';
    public static $_NAME = 'name';
    public static $_IMAGE_PATH = 'image_path';
    public static $_ACTIVE = 'active';

    /**
     * @return string
     */
    public static function getTableName() {
        return 'languages';
    }

    /**
     * @return string[]
     */
    public static function getColumns() {
        return [
            self::$_ID,
            self::$_CODE,
            self::$_NAME,
            self::$_IMAGE_PATH,
            self::$_ACTIVE
        ];
    }
}