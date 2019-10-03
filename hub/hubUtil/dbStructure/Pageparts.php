<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 14.05.2019
 * Time: 12:31
 */

namespace hub\hubUtil\dbStructure;

class Pageparts extends TableCreator {

    public function getTableStructure() {
        return [
            'id' => [
                'key' => 'PRIMARY',
                'type' => 'varchar(255)',
                'null' => false
            ],
            'htmlFile' => [
                'type' => 'varchar(50)',
                'null' => true
            ],
            'permlevel' => [
                'type' => 'int',
                'null' => false,
                'default' => 4
            ]
        ];
    }

    public function getTableValues() {
        return [
            ['footerContent',null,6],
            ['menuBelt',null,6]
        ];
    }


}