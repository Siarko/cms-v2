<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 13.04.2019
 * Time: 13:28
 */

namespace models;

/**
 * @property int $id
 * @property string $url
 * @property string $language
 * @property string $menuname
 * @property string $header_text
 * @property string $content_text
 * @property string $linked_file
 * @property int $menu_visibility
 * @property string $main_image
 * @property boolean $custom_main_image
 * */
class PageLocalizations extends ModelClass {

    const ID = 'id';
    const URL = 'url';
    const LANGUAGE = 'language';
    const MENU_NAME = 'menuname';
    const HEADER_TEXT = 'header_text';
    const CONTENT_TEXT = 'content_text';
    const LINKED_FILE = 'linked_file';
    const MENU_VISIBILITY = 'menu_visibility';
    const MAIN_IMAGE = 'main_image';
    const CUSTOM_MAIN_IMAGE = 'custom_main_image';

    /**
     * @return string
     */
    public static function getTableName() {
        return 'page_localization';
    }

    /**
     * @return string[]
     */
    public static function getColumns() {
        return [
            PageLocalizations::ID,
            PageLocalizations::URL,
            PageLocalizations::LANGUAGE,
            PageLocalizations::MENU_NAME,
            PageLocalizations::HEADER_TEXT,
            PageLocalizations::CONTENT_TEXT,
            PageLocalizations::LINKED_FILE,
            PageLocalizations::MENU_VISIBILITY,
            PageLocalizations::MAIN_IMAGE,
            PageLocalizations::CUSTOM_MAIN_IMAGE
        ];
    }
}