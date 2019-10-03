<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 14.05.2019
 * Time: 12:40
 */

namespace hub\hubUtil\dbStructure;


class UserPermLevels extends TableCreator {

    public function getTableStructure() {
        return [
            'id' => [
                'key' => 'PRIMARY',
                'type' => 'int',
                'null' => false,
                'ai' => true
            ],
            'name' => [
                'type' => 'varchar(30)',
                'null' => false
            ]
        ];
    }

    public function getTableValues() {
        return [
            [1, 'ROOT'],
            [2, 'Admin'],
            [3, 'Moderator'],
            [4, 'Aktywny użytkownik'],
            [5, 'Nieaktywny użytkownik'],
            [6, 'Anonim'],
        ];
    }


}