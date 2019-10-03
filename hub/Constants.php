<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 22.06.2017
 * Time: 00:23
 */

class Constants {

    const DEV_MODE = true; //All dev settings :
    /*
     * Regen class dependency each request
     * */

    const SLASH = DIRECTORY_SEPARATOR;

    const ROOT_DIR = __DIR__.self::SLASH."..";

    const DEPLOYMENT_FILE = 'deployment.inf';

    const PAGES_DIR = 'pages';
    const PAGE_PART_DIR = self::PAGES_DIR.self::SLASH.'pageParts'; //page parts dir - previous all in db
    const TEMPLATES_DIR = self::PAGES_DIR.self::SLASH.'templates'; //dir with template files - previously all in db
    const PAGE_IN_FILE_PATH = self::PAGES_DIR.self::SLASH.'contentPages';

    const EXTERNAL_LIB_DIR = 'lib';
    const PLUGIN_DIR = "global/"; //directory containing modules loaded by HUB
    const FILTER_TEMPLATE_DIR = self::PLUGIN_DIR.'page/filters/templates';
    const EXTERNAL_LIB = []; //load external libraries in /lib
    const CSS_DIR ="css"; //no slash
    const CUSTOM_CSS_PAGE = "customs"; //localization of css files loaded page-specific
    const FILTERS_CSS = 'filters';
    const JS_DIR = "js"; //no slash

    const PAGE_PART_CSS_DIR = 'pagePartSpecific';
    const PAGE_PART_JS_DIR = 'pagePartSpecific';

    const ON_RESOURCE_NOT_FOUND = ''; //returned in case of missing resource(file)
    const MEDIA_DIR = "media";
    const RESOURCES_DIR = Constants::MEDIA_DIR."/resources";
    const ICONS_DIR = Constants::RESOURCES_DIR."/icons"; //dir containing icons
    const UPLOADED_DIR = Constants::MEDIA_DIR."/uploaded"; //dir containing files uploaded by user

    const PLUGIN_DISABLED = ['Error_handle']; //list of files with plugins witch shouldn't be loaded
    const SESSION_LOGGED_KEY = 'loggedAs'; //name of the key in SESSION witch contains logged user id

    const DB_RESTORE_FILE = 'dbRestore.sql'; //TODO będzie zawierać nazwę pliku z którego zostanie odtworzona baza

    /*Which logs should be displayed*/
    const PLUGIN_ORDER = false; //log the order of loading plugins
    const VARS_ON_NOT_FOUND = false; //log if template/page variable was not found
    const DETECTED_LANG = 1; //log Detected page language
    const NO_PAGE_LANG = 1; //log when page in preferred language was not found
    const PAGE_LOAD_INFO = 0; //overall page loading info (from file, custom js/css etc.)
    const LOG_CSS = 0;
    const LOG_JS = 0;
    const URL_UTILS = false; //show info about parsed url
    const LOGGED_USER_INFO = 1; //log info about user
    const FILE_INFO = 0; //logs about wrong path etc.
    const LOG_FILTERS = 1; //logs about content filters


    const DEFAULT_LANGUAGE = 'pl';

    /* Should Variable parser remove non-existing variable names from text? */
    public static $REMOVE_EMPTY_TEMPLATE_VAR = false;

    /* Path to directory containing files of pages not loaded from db
        Relative to index.html as every path. Ok, some are relative to Hub*/

    /* DB system tables names begin */

    /* Ustawienia aplikacji*/
    public static $SETTINGS_TABLE = 'settings';
    /* tabela z użytkownikami*/
    public static $USERS_TABLE = 'users';
    /* tabela z szablonami stron [moze zawierac zmienne]*/
    public static $TEMPLATES_TABLE = 'templates';
    /* tabela z zawartoscią storon*/
    public static $PAGES_TABLE = 'pages';
    /* tabela z dynamicznie ladowanymi czesciami strony, jeszcze nie wiem do konca po co*/
    public static $PAGEPARTS_TABLE = 'pageparts';

    /* DB system tables names end */

    private static $err = ""; //don't touch this

    /* DataBase Credentials*/
    public static $dbCredentials = [];

    /*Config files*/
    //class dependency config
    const CLASS_DEPENDENCY_ORDER = 'class_dependency.conf';

    public static function getDbHost(){return self::$dbCredentials['host'];}
    public static function getDbUserName(){return self::$dbCredentials['username'];}
    public static function getDbPassword(){return self::$dbCredentials['password'];}
    public static function getDbName(){return self::$dbCredentials['database'];}


    //Style for details of log
    private static $centralBlock = 'display:inline-block;padding:5px 40px;text-align:left;background:#545454;margin-top:20px';
    private static function formatLog($title, $desc, $level = 0){
        $color = (($level==0)?'yellow':'red'); //chose the pill
        return "<pre><h1 style='color: #9a9a9a'>".$title."</h1><h3 style='color: {$color}'>".$desc."</h3></pre>";
    }

    public static function formatBlock($content, $style = ''){
        return "<div style='{$style}'>".$content."</div>";
    }

    /*Assumes that DOM have not been built
        Prints nice message and ends execution
    */
    public static function logCrit($title, $desc, $details = null){ //use when something went VERY wrong
        $d = '';
        if($details != null){
            $details .= "<br/>Warning, dev_mode = ".boolToString(self::DEV_MODE);
            $d = Constants::formatBlock($details, Constants::$centralBlock);
        }
        echo("<html><head><meta charset='utf-8'/> </head><body style='background: #4b4b4b; text-align: center'>".
            self::formatLog($title, $desc."<br/>".$d, 1).
            "</body></html>");
        exit(1);
    }

}

Constants::$dbCredentials = require_once(__DIR__.DIRECTORY_SEPARATOR.'DB_ACCESS.php');

function printr($mess){
    echo("<pre>");
    if(gettype($mess) == 'boolean'){
        echo('BOOLEAN: '.($mess?'TRUE':'FALSE'));
    }else{
        $text = print_r($mess, true);
        echo(htmlspecialchars($text));
    }
    echo('</pre>');

}

/**
 * Null coalescence
 * @param $var
 * @param $alternative
 */
function nc($var, $alternative){
    return (($var == null)?$alternative:$var);
}

function logJS($message){
    Hub::get()->DomBuilder->generateInfoScript("console.log('".$message."')");
}

function boolToString($bool){
    return $bool?"TRUE":"FALSE";
}

