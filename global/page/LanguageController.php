<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 11.03.2019
 * Time: 16:21
 */

class LanguageController {

    const LANG_SOURCE_COOKIE = 'COOKIE';
    const LANG_SOURCE_NAVIGATOR = 'NAVIGATOR';
    const LANG_SOURCE_DEFAULT = 'DEFAULT';
    const LANG_SOURCE_FAKE = 'DEV_FAKE';

    private $langSource;
    private $preferredLang;

    function __construct() {
        $this->fetchPreferredLanguage();
    }


    public function isSupportedLanguage($code){
        $lang = \models\Languages::find([\models\Languages::$_CODE => $code])->one();
        return ($lang != null);
    }

    public function setLanguage($code){
        if($this->isSupportedLanguage($code)){
            setcookie('lang', $code, 2147483647, '/');
            $this->fetchPreferredLanguage();
            return true;
        }
        return false;
    }

    private function fetchPreferredLanguage(){
        if(array_key_exists('fake_lang', $_GET)){
            $this->langSource = LanguageController::LANG_SOURCE_FAKE;
            $this->preferredLang = $_GET['fake_lang'];
            return;
        }
        $lang = Constants::DEFAULT_LANGUAGE;
        $contender = $lang;
        $defaultSource = LanguageController::LANG_SOURCE_DEFAULT;
        $source = $defaultSource;
        if(array_key_exists('lang', $_COOKIE)){
            $contender = $_COOKIE['lang'];
            $source = LanguageController::LANG_SOURCE_COOKIE;
        }elseif (array_key_exists('HTTP_ACCEPT_LANGUAGE', $_SERVER)){
            $l = trim($_SERVER['HTTP_ACCEPT_LANGUAGE']);
            if(strlen($l) > 0){
                $contender = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
                $source = LanguageController::LANG_SOURCE_NAVIGATOR;
            }

        }

        if($this->isSupportedLanguage($contender)){
            $lang = $contender;
            $defaultSource = $source;
        }

        $this->langSource = $defaultSource;
        $this->preferredLang = $lang;
    }

    public function getPreferredLanguage(){
        return $this->preferredLang;
    }

    public function getLanguageSource(){
        return $this->langSource;
    }


    private function getImagePath($imageName){
        return Hub::get()->Resources->getIcon('flags/'.$imageName, true);
    }

    public function getLanguageList($url = null){
        $result = [];
        $list = \models\Languages::find();
        $occupied = [];
        if($url != null){
            $occ = \models\PageLocalizations::find([\models\PageLocalizations::URL => $url])->all();
            /* @var \models\PageLocalizations $item*/
            foreach ($occ as $item) {
                $occupied[] = $item->language;
            }
        }
        foreach ($list as $language) {
            if(in_array($language->code, $occupied)){continue;}
            $result[] = [
                'code' => $language->code,
                'name' => $language->name,
                'imagePath' => $this->getImagePath($language->image_path),
                'active' => ($language->active==1)
            ];
        }

        return $result;
    }

    /**
     * @param $code string
     * @return \models\ModelClass|null
     */
    public function getByCode($code){
        $code = trim($code);
        if(strlen($code) == 0){
            return null;
        }
        return \models\Languages::find([\models\Languages::$_CODE => $code])->one();
    }

    public function getFlagList(){
        $list = Hub::get()->Resources->getResourceList('icons/flags');
        $result = [];
        foreach ($list as $item) {
            $result[] = [
                'filename' => $item,
                'code' => explode('.', $item)[0],
            ];
        }
        return $result;
    }

    public function change($id, $param, $value) {
        /* @var $result \models\Languages*/
        $result = \models\Languages::find(['code' => $id])->one();
        if($result){
            $result->setAttribute($param, $value);
            return $result->save();
        }else{
            return false;
        }

    }

    public function add($code, $name, $image){
        $row = new \models\Languages();
        $row->code = $code;
        $row->name = $name;
        $row->image_path = $image;
        $row->active = 0;
        return $row->save();
    }

    public function delete($code){
        $row = \models\Languages::find(['code' => $code])->one();
        $users = \models\PageLocalizations::find([\models\PageLocalizations::LANGUAGE => $code])->all();
        if($users){
            foreach ($users as $user) {
                $user->delete();
            }
        }
        return $row->delete();
    }

}