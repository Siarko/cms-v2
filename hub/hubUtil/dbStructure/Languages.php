<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 14.05.2019
 * Time: 12:28
 */

namespace hub\hubUtil\dbStructure;

class Languages extends TableCreator {

    public function getTableStructure() {
        return [
            'id' => [
                'key' => 'PRIMARY',
                'ai' => true,
                'type' => 'int',
                'null' => false
            ],
            'code' => [
                'type' => 'varchar(3)',
                'null' => false
            ],
            'name' => [
                'type' => 'varchar(20)',
                'null' => false
            ],
            'image_path' => [
                'type' => 'varchar(100)',
                'null' => false
            ],
            'active' => [
                'type' => 'tinyint(1)',
                'null' => false,
                'default' => 0
            ]
        ];
    }

    public function getTableValues() {
        return [
            [null, 'pl', 'Polski', 'pl.png', 1]
        ];
    }


}