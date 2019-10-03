<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 14.05.2019
 * Time: 12:39
 */

namespace hub\hubUtil\dbStructure;


class Users extends TableCreator {

    public function getTableStructure() {
        return [
            'name' => [
                'key' => 'PRIMARY',
                'type' => 'varchar(30)',
                'null' => false
            ],
            'passhash' => [
                'type' => 'varchar(255)',
                'null' => false
            ],
            'permissions' => [
                'type' => 'int',
                'default' => 6,
                'null' => false
            ],
            'date' => [
                'type' => 'timestamp',
                'default' => 'CURRENT_TIMESTAMP',
                'null' => false
            ],
            'banned' => [
                'type' => 'tinyint(1)',
                'default' => 0,
                'null' => false
            ],
            'deleted' => [
                'type' => 'tinyint(1)',
                'default' => 0,
                'null' => false
            ],
            'mail' => [
                'type' => 'varchar(255)',
                'null' => true
            ],
        ];
    }

    public function getTableValues() {
        return [
            [
                'siarko',
                '$2y$10$ML.K.60//DaUIJfz1bBob.J7kTVpcfw07aa9RMCRdMX27v4vaPyHS',
                1, '2018-03-17 01:04:35', 0, 0, null
            ]
        ];
    }


}