<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 10.08.2018
 * Time: 21:28
 */

namespace models;


/**
 * @property string id
 * @property string templateid
 * @property int permlevel
 * @property string customcss
 * @property int deleteable
 * @property int editable
 * @property PageLocalizations[] $language_versions
 * */
class Pages extends ModelClass{

    const ID = 'id';

    const TEMPLATEID = 'templateid';
    const PERMLEVEL = 'permlevel';
    const CUSTOMCMS = 'customcss';

    const DELETEABLE = 'deleteable';
    const EDITABLE = 'editable';

    /**
     * @return string
     */
    public static function getTableName() {
        return 'pages';
    }

    /**
     * @return array
     */
    public static function getColumns() {
        return [
            self::ID,
            self::TEMPLATEID,
            self::PERMLEVEL,
            self::CUSTOMCMS,
            self::DELETEABLE,
            self::EDITABLE
        ];
    }

    public function onGetLanguageVersions(){
        return PageLocalizations::find(['url' => $this->id])->all();
    }

    public function onGetDeleteable($value){
        return ($value=="1");
    }

    public function onGetEditable($value){
        return ($value=="1");
    }

    public function onSetLanguageVersions($value){}


    /**
     * @param $primaryLang string
     * @param $secondaryLang string
     * @return PageLocalizations
     */
    public function findLocalization($primaryLang = null, $secondaryLang = \Constants::DEFAULT_LANGUAGE){
        if($primaryLang == null){
            $primaryLang = \Hub::get()->LanguageController->getPreferredLanguage();
        }
        $sec = null;
        foreach ($this->language_versions as $language_version) {
            if($language_version->language == $primaryLang){
                return $language_version;
            }elseif($language_version->language == $secondaryLang){
                $sec = $language_version;
            }
        }
        if($sec == null){
            return $this->language_versions[0];
        }
        return $sec;
    }

}