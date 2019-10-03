<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 14.05.2019
 * Time: 12:36
 */

namespace hub\hubUtil\dbStructure;


class Settings extends TableCreator {

    public function getTableStructure() {
        return [
            'name' => [
                'key' => 'PRIMARY',
                'type' => 'varchar(64)',
                'null' => false
            ],
            'value' => [
                'type' => 'varchar(255)',
                'null' => false
            ]
        ];
    }

    public function getTableValues() {
        return [
            ['404pageUrl','404'],
            ['anonymousUserPerm','6'],
            ['apiPrefix','api'],
            ['homePageUrl','home'],
            ['pageHomeButton','SiarkoCMS'],
            ['realRedirect','0'],
            ['redirectOnEmpty','home'],
            ['rootConsoleLink','root'],
        ];
    }


}