<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 14.05.2019
 * Time: 12:34
 */

namespace hub\hubUtil\dbStructure;


class PageLocalization extends TableCreator {

    public function getTableStructure() {
        return [
            'id' => [
                'key' => 'PRIMARY',
                'ai' => true,
                'type' => 'int',
                'null' => false
            ],
            'url' => [
                'type' => 'varchar(255)',
                'null' => false
            ],
            'language' => [
                'type' => 'varchar(4)',
                'null' => true
            ],
            'menuname' => [
                'type' => 'varchar(30)',
                'null' => true
            ],
            'header_text' => [
                'type' => 'varchar(255)',
                'null' => true
            ],
            'content_text' => [
                'type' => 'text',
                'null' => true
            ],
            'linked_file' => [
                'type' => 'varchar(255)',
                'null' => true
            ],
            'menu_visibility' => [
                'type' => 'tinyint(1)',
                'null' => false
            ],
            'main_image' => [
                'type' => 'text',
                'default' => null,
                'null' => true
            ],
            'custom_main_image' => [
                'type' => 'tinyint(1)',
                'default' => 0
            ]
        ];
    }

    public function getTableValues() {
        return [
            [null, '404', 'pl', null, '<h1>404</h1>', '<p>Strona nie została odnaleziona</p>', null, 0, null, 0],
            [null, 'home', 'pl', 'Home', '<p>Home page</p>', '<p>Witaj! CMS READY</p>', null, 0, null, 0],
            [null, 'login', 'pl', null, null, null, 'login.php', 0, null, 0],
            [null, 'root', 'pl', 'Root', null, null, 'root.php', 1, null, 0],
        ];
    }


}