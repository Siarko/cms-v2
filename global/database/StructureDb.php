<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 28.01.2018
 * Time: 21:40
 */

class StructureDb extends Db {

    /**
     * @param null|array $condition
     * @return array
     */
    public function getPageList($condition = null){
        if($condition == null){
            $list = \models\Pages::findAll()->all();
        }else{
            $list = \models\Pages::find($condition)->all();
        }
        $result = [];
        /* @var $item \models\Pages*/
        foreach ($list as $k => $item) {
            $localization = $item->findLocalization(
                Hub::get()->LanguageController->getPreferredLanguage(),
                Constants::DEFAULT_LANGUAGE
            );
            $row = $item->getColumnAssoc();
            $row['localization'] = (($localization != null)?$localization->getColumnAssoc():null);
            $result[] = $row;
        }
        return $result;
    }

}