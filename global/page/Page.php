<?php
/*Depends: PageDb, Template, VariableLoader*/
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 01.07.2017
 * Time: 12:55
 */
class Page {

    const ROOT_PAGE_PERMS = 1;
    const ADMIN_PAGE_PERMS = 2;

    private $url;
    private $content;
    private $header;
    private $permLevel;
    private $templateId;
    private $customCss;
    /* @var Template $template*/
    private $template;
    private $exists;
    private $inFile;

    /* @var \models\PageLocalizations $localized*/
    public $localized;

    public static $current = null;

    /**
     * PageService constructor.
     * @param $hub Hub
     * @param $url [string]
     */
    function __construct($hub, $url = null) {
        if ($url != null) {
            $url = trim($url, "/");
            $hub->logIf(
                Messages::format(Messages::$DETECTED_LANG,Hub::get()->LanguageController->getPreferredLanguage()),
                LogLevel::INFO,
                Constants::DETECTED_LANG);

            $hub->logIf(
                Messages::format(Messages::$LANG_SOURCE, Hub::get()->LanguageController->getLanguageSource()),
                LogLevel::INFO,
                Constants::DETECTED_LANG
            );

            $this->loadIfExists($hub, $url);
        }
    }

    public function getInstance($url = null) {
        return new Page(Hub::get(), $url);
    }

    /**
     * @param $hub Hub
     * @param $url string
     * @return bool
     */
    private function loadIfExists($hub, $url){
        $this->exists = $hub->PageDb->exists($url);
        if ($this->exists) {
            $this->url = $url;
            $path = $hub->PageDb->loadPage($this);
            if($path !== null){
                $this->template = $hub->Template->getInstance(null, $path); //artificial template(probably page from file)
            }else{
                $this->template = $hub->Template->getInstance($this->templateId); //normal template, from DB
            }
            return true; //loaded
        }else{
            return false; //not loaded
        }
    }

    public function hasCustomJs() {
        $flag = Hub::jsFileExists('custom/'.$this->url);
        if($flag){return true;}
        Hub::get()->logIf(
            Messages::format(Messages::$CUSTOM_JS_NOT_FOUND, $this->url.".js"),
            LogLevel::WARN, Constants::LOG_JS
        );
        return false;
    }

    public function getCustomJsFile() {
        return 'custom/'.$this->url;
    }

    /**
     * @param $lang string(pl|en itp)
     * Changes cookie that contains preferred language
     */
    /*private function setPreferedLang($lang){
        setcookie('lang', $lang, 2147483647); //long lasting cookie
    }*/

    //SETTER GETTER
    public function exists(){
        return $this->exists;
    }
    /**
     * @return mixed
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * Creates full page, loads variables and parses template
     * @return string template+content
     */
    public function bake(){
        $this->loadVariables();
        return $this->template->parse();
    }

    public function loadVariables(){
        Hub::get()->Variables->content = $this->getContent();
        Hub::get()->Variables->pageHeader = $this->getHeader();
    }

    /**
     * @param mixed $content
     */
    public function setContent($content) {
        $this->content = $content;
    }

    public function setCustomCss($fileName){
        $this->customCss = $fileName;
    }

    public function hasCustomCss(){
        return !empty($this->customCss);
    }

    public function getCustomCssFile(){
        return Constants::CUSTOM_CSS_PAGE."/".$this->customCss;
    }

    public function isInFile(){
        return $this->inFile;
    }

    public function setInFile($flag){
        $this->inFile = $flag;
    }

    /**
     * @return mixed
     */
    public function getPermLevel() {
        return $this->permLevel;
    }

    /**
     * @param mixed $permLevel
     */
    public function setPermLevel($permLevel) {
        $this->permLevel = $permLevel;
    }

    /**
     * @return mixed
     */
    public function getTemplateId() {
        return $this->templateId;
    }

    /**
     * @param mixed $templateId
     */
    public function setTemplateId($templateId) {
        $this->templateId = $templateId;
    }

    /**
     * @return null
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * @param null $url
     */
    public function setUrl($url) {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getHeader() {
        return $this->header;
    }

    /**
     * @param mixed $header
     */
    public function setHeader($header) {
        $this->header = $header;
    }

    /* Methods for changing pages with REST*/

    public function changeHeader($text){
        $this->header = $text;
        Hub::get()->PageDb->changeHeader($this);
    }

    public function changeContent($text){
        $this->content = $text;
        return Hub::get()->PageDb->changeContent($this);
    }

    public function filterContent(){
        $prefix = '<body>';
        $xml = '<?xml encoding="utf-8" ?>'.$prefix.$this->content.'</body>';
        $document = new DOMDocument();
        $document->registerNodeClass('DOMElement', 'JSLikeHTMLElement');
        $document->formatOutput = false;
        $document->preserveWhiteSpace = false;
        $trap = new DomErrorTrap([$document, 'loadHTML']);
        /*echo($xml);
        exit();*/
        try {
            $trap->call($xml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        } catch (Exception $e) {
            echo($e->getMessage());
        }
        if(!$trap->ok()){
            Hub::logIf("XML PARSER ERRORS OCCURED: ".count($trap->errors()), LogLevel::ERR, Constants::LOG_FILTERS);
            foreach ($trap->errors() as $error) {
                Hub::logIf($error, LogLevel::ERR, Constants::LOG_FILTERS);
            }
        }
        foreach (Hub::get()->CascadeFilterController->getHandlers() as $cssClass => $handler) {
            $finder = new DomXPath($document);
            $spaner = $finder->query("//*[contains(@data-type, '".$cssClass."')]");
            Hub::logIf(
                "Found ".$spaner->length." elements for filter: ".$cssClass,
                LogLevel::INFO, Constants::LOG_FILTERS
            );
            if($spaner->length > 0){
                /* @var $item DOMNode*/
                foreach ($spaner as $item) {
                    $data = [];
                    /* @var DOMAttr $attribute*/
                    foreach ($item->attributes as $attribute) {
                        if(substr($attribute->name, 0, 5) == 'data-'){
                            $data[substr($attribute->name, 5)] = $attribute->value;
                        }
                    }
                    Hub::logIf("Filter element: ".print_r($item, true), LogLevel::INFO, Constants::LOG_FILTERS);
                    $item->innerHTML = Hub::get()->$handler->applyWrapper($this, $data);
                }
            }
        }

        \page\CascadeFilter::includeFilterCss();

        $content = $document->saveHTML($document->documentElement);
        $content = substr($content, strlen($prefix), strlen($content)-(strlen($prefix)*2+1));
        /*echo($content);
        exit();*/
        $this->content = $content;
    }

    public static function standardLoad($url){
        $page = Hub::get()->Page->getInstance($url);
        Page::$current = $url;
        Hub::get()->logIf(
            Messages::format(Messages::$SEARCHING_FOR_PAGE, $url),
            LogLevel::INFO, Constants::URL_UTILS);

        $user = Hub::get()->Auth->getLogged();

        $footer = Hub::get()->PagePart->get('footerContent')->getContent();
        Hub::get()->Variables->footer = $footer;
        Hub::get()->VariableLoader;


        $canAccess = $user->canAccess($page);
        if ($page->exists() and $canAccess) {
            if($page->hasCustomCss()){
                Hub::get()->DomBuilder->addStyle($page->getCustomCssFile());
            }
            if ($page->hasCustomJs()) {
                Hub::get()->DomBuilder->addScript($page->getCustomJsFile());
            }
            Hub::get()->DomBuilder->body($page->bake());
        } else {
            Hub::get()->log(Messages::format(Messages::$PAGE_NOT_FOUND, $url), LogLevel::ERR);
            $page = Hub::get()->Page->getInstance(Hub::get()->SettingsDb->getSetting('404pageUrl'));
            if (!$page->exists()) {
                Hub::get()->log("Something is VERY wrong. 404 page does not seem to exist!", LogLevel::ERR);
                echo(Messages::get(Messages::$SOMETHING_WENT_WRONG));
            } else {
                if (!$canAccess) {
                    Hub::get()->log(Messages::$CANNOT_ACCESS_PAGE, LogLevel::WARN);
                }
                Hub::get()->DomBuilder->body($page->bake());
            }
        }
    }

    public function addNew($url, $name, $templateId, $language = null) {
        $page = new \models\Pages();
        $page->id = $url;
        $page->templateid = $templateId;
        $page->deleteable = 1;
        $page->editable = 1;
        $page->permlevel = Hub::get()->Auth->getLogged()->getPerms();
        if($language != null){
            $localization = new \models\PageLocalizations();
            $localization->url = $url;
            $localization->menu_visibility = 1;
            $localization->menuname = $name;
            $localization->language = $language;
            $localization->header_text = Hub::get()->Html->element('h2', $name);
            $localization->save();
        }
        if($page->save()){
            return $url;
        }else{
            return null;
        }
    }

    public function delete($url){
        /* @var $page \models\Pages*/
        $page = \models\Pages::find($url)->one();
        return $page->delete();
    }

    public function getPageList() {
        $list = \models\Pages::find()->asArray();
        return $list;
    }

    public function changeMenuName($id, $langVersion, $newName) {
        $localization = \models\PageLocalizations::find([
            \models\PageLocalizations::URL => $id,
            \models\PageLocalizations::LANGUAGE => $langVersion
        ])->one();
        if($localization !== null){
            $localization->menuname = $newName;
            return $localization->save();
        }else{
            return null;
        }
    }

    public function changePageTemplate($id, $newTemplate) {
        $page = \models\Pages::find($id)->one();
        if($page !== null){
            /* @var $page \models\Pages*/
            $page->templateid = $newTemplate;
            return $page->save();
        }else{
            return false;
        }
    }

    public function addLocalization($url, $lang){
        $localization = new \models\PageLocalizations();
        $localization->url = $url;
        $localization->language = $lang;
        $localization->menu_visibility = 0;
        return ($localization->save())?$lang:false;
    }

    public function deleteLocalization($pageId, $lang) {
        $localization = \models\PageLocalizations::find([
            \models\PageLocalizations::URL => $pageId,
            \models\PageLocalizations::LANGUAGE => $lang
        ])->one();
        if($localization != null){
            return $localization->delete();
        }
        return null;
    }

    public function setLanguage($id, $oldLang, $newLang){
        $localization = \models\PageLocalizations::find([
            \models\PageLocalizations::URL => $id,
            \models\PageLocalizations::LANGUAGE => $oldLang
        ])->one();
        if($localization != null){
            $duplicate = \models\PageLocalizations::find([
                \models\PageLocalizations::URL => $id,
                \models\PageLocalizations::LANGUAGE => $newLang
            ])->one();
            if($duplicate == null){
                $localization->language = $newLang;
                return $localization->save();
            }
            return false;
        }
        return null;
    }

    public function setLinkedFile($id, $lang, $file){
        /* @var \models\PageLocalizations|null $localization*/
        $localization = \models\PageLocalizations::find([
            \models\PageLocalizations::URL => $id,
            \models\PageLocalizations::LANGUAGE => $lang
        ])->one();
        if($localization){
            $file = trim($file);
            $localization->linked_file = ((strlen($file) > 0)?$file:null);
            return $localization->save();
        }
        return null;
    }

    public function getPageData($id, $singleLocalization = true) {
        /* @var $result \models\Pages*/
        $result = \models\Pages::find($id)->one();
        if($singleLocalization){
            $localization = $result->findLocalization()->getColumnAssoc();
        }else{
            $localization = $result->language_versions;
        }
        $result = $result->getColumnAssoc();
        $result['localization'] = $localization;
        return $result;
    }

    public function change($id, $param, $value) {
        /* @var $result \models\Pages*/
        $result = \models\Pages::find($id)->one();
        if($result){
            $result->setAttribute($param, $value);
            return $result->save();
        }else{
            return false;
        }

    }
} 