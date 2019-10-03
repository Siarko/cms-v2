<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 15.08.2018
 * Time: 14:38
 */

namespace models;


/**
 * @property int $id
 * @property string $htmlfile
 * @property string|null $cssfile
 * */
class Templates extends ModelClass {

    public static $_ID = 'id';
    public static $_HTMLFILE = 'htmlfile';
    public static $_CSSFILE = 'cssfile';

    public static function getTableName() {
        return 'templates';
    }

    public static function getColumns() {
        return [
            self::$_ID, self::$_HTMLFILE, self::$_CSSFILE
        ];
    }
}