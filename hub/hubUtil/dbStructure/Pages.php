<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 14.05.2019
 * Time: 12:33
 */

namespace hub\hubUtil\dbStructure;


class Pages extends TableCreator {

    public function getTableStructure() {
        return [
            'id' => [
                'key' => 'PRIMARY',
                'type' => 'varchar(255)',
                'null' => false
            ],
            'templateid' => [
                'type' => 'int',
                'null' => false,
                'default' => 1
            ],
            'permLevel' => [
                'type' => 'int',
                'null' => false,
                'default' => 1
            ],
            'customCss' => [
                'type' => 'varchar(128)',
                'null' => true
            ],
            'deleteable' => [
                'type' => 'tinyint(1)',
                'null' => false,
                'default' => 1
            ],
            'editable' => [
                'type' => 'tinyint(1)',
                'null' => false,
                'default' => 1
            ],
        ];
    }

    public function getTableValues() {
        return [
            ['404', 1, 6, null, 0, 1],
            ['home', 1, 6, null, 0, 1],
            ['login', 1, 6, 'login', 0, 0],
            ['root', 1, 1, 'root', 0, 0],
        ];
    }


}