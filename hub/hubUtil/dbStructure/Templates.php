<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 14.05.2019
 * Time: 12:38
 */

namespace hub\hubUtil\dbStructure;


class Templates extends TableCreator {

    public function getTableStructure() {
        return [
            'id' => [
                'key' => 'PRIMARY',
                'type' => 'int',
                'ai' => true,
                'null' => false
            ],
            'htmlFile' => [
                'type' => 'varchar(50)',
                'null' => true
            ],
            'cssFile' => [
                'type' => 'varchar(255)',
                'null' => true
            ]
        ];
    }

    public function getTableValues() {
        return [
            [1,'default.php',null]
        ];
    }


}