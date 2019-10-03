<?php

/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 05.09.2017
 * Time: 10:16
 */
class Messages {

    /*const SOMETHING_WENT_WRONG = 0;
    //Ladowanie stron
    const NO_PREFERRED_LANG_PAGE = 1;
    const NO_DEFAULT_LANG_PAGE = 2;
    const PAGE_NOT_FOUND = 3;
    const SEARCHING_FOR_PAGE = 4;
    const CANNOT_ACCESS_PAGE = 5;
    //Język
    const DETECTED_LANG = 6;
    //Url
    const RETURNING_URL = 7;
    const RETURN_ON_EMPTY_URL = 8;
    //Użytkownicy
    const LOGGED_AS_USER = 9;
    const LOGGED_AS_ANONYMOUS = 10;
    const TRYING_TO_LOAD_NON_E_USER = 11;
    const USER_ALREADY_EXISTS = 12;

    const ACCESSING_NON_LOADED_HUB = 13;

    const DEFAULT_LANG = 'en';

    const M = [
        'pl' => [
            Messages::SOMETHING_WENT_WRONG => "<h1 style='text-align: center'>Coś poszło nie tak :/</h1>",
            Messages::NO_PREFERRED_LANG_PAGE => "Nie znaleziono strony w preferowanym języku",
            Messages::NO_DEFAULT_LANG_PAGE => "Nie znaleziono strony w domyślnym języku",
            Messages::PAGE_NOT_FOUND => "Strona nie została odnaleziona! <br/>%s",
            Messages::SEARCHING_FOR_PAGE => "Wyszukiwanie strony: %s",
            Messages::CANNOT_ACCESS_PAGE => "Ten użytkownik nie może uzyskać dostępu do strony!",
            Messages::DETECTED_LANG => "Wykryty język: %s",
            Messages::RETURNING_URL => "Zwracanie URLa: %s",
            Messages::RETURN_ON_EMPTY_URL => "Zwracanie przy pustym URLu: %s",
            Messages::LOGGED_AS_USER => "Zalogowano jako >%s<",
            Messages::LOGGED_AS_ANONYMOUS => "Zalogowano jako anonim",
            Messages::TRYING_TO_LOAD_NON_E_USER => "Próba załadowania nie istniejącego usera!",
            Messages::USER_ALREADY_EXISTS => "Taki użytkownik już istnieje! <br/> &gt;%s",
            Messages::ACCESSING_NON_LOADED_HUB => "Próba uzyskania dostępu do niezaładowanego HUBa!"
        ],
        'en' => [
            Messages::SOMETHING_WENT_WRONG => "<h1 style='text-align: center'>Something went wrong :/</h1>",
            Messages::NO_PREFERRED_LANG_PAGE => "No preferred lang page found",
            Messages::NO_DEFAULT_LANG_PAGE => "No default lang page found",
            Messages::PAGE_NOT_FOUND => "PageService not found! <br/>%s",
            Messages::SEARCHING_FOR_PAGE => "Searching for page: %s",
            Messages::CANNOT_ACCESS_PAGE => "This user cannot access this page!",
            Messages::DETECTED_LANG => "Detected language: %s",
            Messages::RETURNING_URL => "Returning URL: %s",
            Messages::RETURN_ON_EMPTY_URL => "Returning on empty URL: %s",
            Messages::LOGGED_AS_USER => "Logged as >%s<",
            Messages::LOGGED_AS_ANONYMOUS => "Logged as anonymous user",
            Messages::TRYING_TO_LOAD_NON_E_USER => "Trying to load non-existing user!",
            Messages::USER_ALREADY_EXISTS => "That user already exists! <br/> &gt;%s",
            Messages::ACCESSING_NON_LOADED_HUB => "Trying to access non-loaded hub!"

        ]
    ];*/

    private static $COUNTER = 0;

    public static $SOMETHING_WENT_WRONG;

    public static $RESOURCE_NOT_FOUND;
    public static $CLASS_NON_EXISTING;
    //Ladowanie stron
    public static $NO_PREFERRED_LANG_PAGE;
    public static $NO_DEFAULT_LANG_PAGE;
    public static $PAGE_NOT_FOUND;
    public static $SEARCHING_FOR_PAGE;
    public static $CANNOT_ACCESS_PAGE;
    public static $CUSTOM_JS_NOT_FOUND;
    //Język
    public static $DETECTED_LANG;
    public static $LANG_SOURCE;
    //Url
    public static $RETURNING_URL;
    public static $RETURN_ON_EMPTY_URL;
    //Użytkownicy
    public static $LOGGED_AS_USER;
    public static $LOGGED_AS_ANONYMOUS ;
    public static $TRYING_TO_LOAD_NON_E_USER ;
    public static $USER_ALREADY_EXISTS ;

    public static $ACCESSING_NON_LOADED_HUB ;

    public static $CANNOT_INJECT_CLOSURE_TO_ARRAY;

    const DEFAULT_LANG = 'en';

    private static $M;

    static $LANG;
    static $MESSAGE_LANG;

    function __construct() {
        /* PL MESSAGES */
        self::ADD('pl', self::$SOMETHING_WENT_WRONG, "<h1 style='text-align: center'>Coś poszło nie tak :/</h1>");
        self::ADD('pl', self::$RESOURCE_NOT_FOUND, "Nie znaleziono rządanego zasobu: %s");
        self::ADD('pl', self::$NO_PREFERRED_LANG_PAGE, "Nie znaleziono strony w preferowanym języku");
        self::ADD('pl', self::$NO_DEFAULT_LANG_PAGE, "Nie znaleziono strony w domyślnym języku");
        self::ADD('pl', self::$PAGE_NOT_FOUND, "Strona nie została odnaleziona! <br/>%s");
        self::ADD('pl', self::$SEARCHING_FOR_PAGE, "Wyszukiwanie strony: %s");
        self::ADD('pl', self::$CANNOT_ACCESS_PAGE, "Ten użytkownik nie może uzyskać dostępu do strony!");
        self::ADD('pl', self::$DETECTED_LANG, "Wykryty język: %s");
        self::ADD('pl', self::$LANG_SOURCE, "Żródło wykrytego języka: %s");
        self::ADD('pl', self::$RETURNING_URL, "Zwracanie URLa: %s");
        self::ADD('pl', self::$RETURN_ON_EMPTY_URL, "Zwracanie przy pustym URLu: %s");
        self::ADD('pl', self::$LOGGED_AS_USER, "Zalogowano jako >%s<");
        self::ADD('pl', self::$LOGGED_AS_ANONYMOUS, "Zalogowano jako anonim");
        self::ADD('pl', self::$TRYING_TO_LOAD_NON_E_USER, "Próba załadowania nie istniejącego usera!");
        self::ADD('pl', self::$USER_ALREADY_EXISTS, "Taki użytkownik już istnieje! <br/> &gt;%s");
        self::ADD('pl', self::$ACCESSING_NON_LOADED_HUB, "Próba uzyskania dostępu do niezaładowanego HUBa!");
        self::ADD('pl', self::$CUSTOM_JS_NOT_FOUND, 'Indywidualny JS nie znaleziony: %s');
        self::ADD('pl', self::$CLASS_NON_EXISTING, 'Próba załadowania nieistniejącej klasy - %s');
        self::ADD('pl', self::$CANNOT_INJECT_CLOSURE_TO_ARRAY, 'Nie można wstrzyknąć zmiennej z klamerek do tablicy');

        /* EN MESSAGES */
        self::ADD('en', self::$SOMETHING_WENT_WRONG, "<h1 style='text-align: center'>Something went wrong :/</h1>");
        self::ADD('en', self::$RESOURCE_NOT_FOUND, "Couldn't locate requested resource: %s");
        self::ADD('en', self::$NO_PREFERRED_LANG_PAGE, "No preferred lang page found");
        self::ADD('en', self::$NO_DEFAULT_LANG_PAGE, "No default lang page found");
        self::ADD('en', self::$PAGE_NOT_FOUND, "PageService not found! <br/>%s");
        self::ADD('en', self::$SEARCHING_FOR_PAGE, "Searching for page: %s");
        self::ADD('en', self::$CANNOT_ACCESS_PAGE, "This user cannot access this page!");
        self::ADD('en', self::$DETECTED_LANG, "Detected language: %s");
        self::ADD('en', self::$LANG_SOURCE, "Detected language source: %s");
        self::ADD('en', self::$RETURNING_URL, "Returning URL: %s");
        self::ADD('en', self::$RETURN_ON_EMPTY_URL, "Returning on empty URL: %s");
        self::ADD('en', self::$LOGGED_AS_USER, "Logged as >%s<");
        self::ADD('en', self::$LOGGED_AS_ANONYMOUS, "Logged as anonymous user");
        self::ADD('en', self::$TRYING_TO_LOAD_NON_E_USER, "Trying to load non-existing user!");
        self::ADD('en', self::$USER_ALREADY_EXISTS, "That user already exists! <br/> &gt;%s");
        self::ADD('en', self::$ACCESSING_NON_LOADED_HUB, "Trying to access non-loaded hub!");
        self::ADD('en', self::$CUSTOM_JS_NOT_FOUND, 'Custom JS file not found: %s');
        self::ADD('en', self::$CLASS_NON_EXISTING, 'Trying to load non-existing class - %s');
        self::ADD('en', self::$CANNOT_INJECT_CLOSURE_TO_ARRAY, 'Cannot inject closure var to array!');


        $lang = self::getBrowserLanguage();
        Messages::setLang($lang);
    }

    private static function ADD($lang, &$ID, $message){
        if(empty($ID)){$ID = Messages::$COUNTER; Messages::$COUNTER++;}
        Messages::$M[$lang][$ID] = $message;
    }

    public static function format($messageId, ...$params){
        $message = Messages::get($messageId);
        return vsprintf($message, $params);
    }

    public static function getBrowserLanguage() {
        if(isset($_COOKIE['lang'])){
            return $_COOKIE['lang'];
        }
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            return substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        }
        return Messages::DEFAULT_LANG; //default lang; if browser lang was not found
    }

    public static function get($name){
        $m = Messages::$M[Messages::$MESSAGE_LANG][$name];
        if(empty($m)){return null;}
        return $m;
    }

    private static function isValidLang($lang){
        foreach (Messages::$M as $l => $k){
            if($lang == $l){
                return true;
            }
        }
        return false;
    }

    public static function setLang($lang){
        if(!Messages::isValidLang($lang)){
            Messages::$MESSAGE_LANG = Messages::DEFAULT_LANG;
            return false;
        } //language not on the list
        Messages::$MESSAGE_LANG = $lang; //set language
        return true; //set successful
    }
}