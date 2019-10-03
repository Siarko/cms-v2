<?php
/*Depends: Url, Html*/
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 02.07.2017
 * Time: 20:54
 */

class DomBuilder {

    private $styles;
    private $scripts;
    private $inBodyScripts;
    private $body;
    private $head;
    private $htmlStyle;
    private $headStyle;
    private $bodyStyle;

    private $darkMode = false;

    private $jsonMode = false;

    function __construct() {
        $this->darkMode = (array_key_exists('darkmode', $_SESSION) and $_SESSION['darkmode'] == 1);
    }


    public function setCharset($charset){
        $element = Hub::get()->Html->element("meta");
        $element->charset = $charset;
        $this->head .= $element;
    }

    public function meta($name, $content){
        $element = Hub::get()->Html->element("meta");
        $element->name = $name;
        $element->content = $content;
        $this->head .= $element;
    }

    public function setHome($url){
        $element = Hub::get()->Html->element("base");
        $element->href = $url;
        $this->head .= $element;
    }

    public function addColorStylesheet($name){
        $sufix = '.colors';
        if($this->isDarkMode() and file_exists(Constants::CSS_DIR.'/'.$name.'.colors.dark.css')){
            $sufix = '.colors.dark';
        }
        $path = Constants::CSS_DIR.'/'.$name.'.colors.css';
        if(file_exists($path)){
            $this->addStyle($name.$sufix, false);
            Hub::logIf($name.".colors added", LogLevel::INFO, Constants::LOG_CSS);
        }else{
            Hub::logIf($name.".colors does not exist", LogLevel::WARN, Constants::LOG_CSS);
        }
    }

    public function addStyle($name, $addColors = true){

        if($addColors){
            $this->addColorStylesheet($name);
        }

        $element = Hub::get()->Html->element("link");
        $base = Hub::get()->Url->getPrefix();

        $element->rel = "stylesheet";
        $element->type = "text/css";
        $element->href = $base.Constants::CSS_DIR."/".$name.".css";
        $this->styles .= $element;
    }

    public function embedStyle($link){
        $element = Hub::get()->Html->element("link");
        $element->rel = "stylesheet";
        $element->href = $link;
        $this->styles .= $element;
    }

    public function getInfoScript(){
        $script = new JavaScript();
        $script->CMS_HOME = Hub::get()->Url->getPrefix();
        $script->LANGUAGE = Hub::get()->LanguageController->getPreferredLanguage();
        $script->DARKMODE = Hub::get()->DomBuilder->isDarkMode();
        return $script->parse();
    }

    public function generateInfoScript($script){
        $element = Hub::get()->Html->element("script");
        $element->type = "text/javascript";
        $element->value = $script;
        $this->scripts .= $element;
    }

    public function addScript($name, $inHead = true){
        $base = Hub::get()->Url->getPrefix();
        $element = Hub::get()->Html->element("script");
        $element->type = "text/javascript";
        $element->src = $base.Constants::JS_DIR."/".$name.".js";
        if($inHead){
            $this->scripts .= $element;
        }else{
            $this->inBodyScripts .= $element;
        }
    }

    public function embedScript($link, $inHead = true){
        $element = Hub::get()->Html->element("script");
        $element->type = "text/javascript";
        $element->src = $link;
        if($inHead){
            $this->scripts .= $element;
        }else{
            $this->inBodyScripts .= $element;
        }
    }

    public function body($content){
        if(gettype($content) !== "string"){
            $content = print_r($content, true);
        }
        $this->body .= $content;
    }

    public function setBody($value){
        $this->body = $value;
    }

    public function jsonMode($mode = true){
        $this->jsonMode = $mode;
    }

    public function inJsonMode(){
        return $this->jsonMode;
    }

    public function build($print = false){
        Hub::get()->logRunnigTime();
        Hub::get()->logMemUsage();
        if($this->jsonMode){
            $page = $this->body;
            header('Content-Type: application/json');
        }else{
            $page = Hub::get()->Html->element("html");
            $head = Hub::get()->Html->element("head");
            $body = Hub::get()->Html->element("body");

            if ($this->htmlStyle){$page->class = $this->htmlStyle;}
            if ($this->headStyle){$head->class = $this->headStyle;}
            if ($this->bodyStyle){$body->class = $this->bodyStyle;}

            $head->value = $this->head.$this->styles.$this->scripts;

            $body->value = $this->body;
            $body->value .= $this->inBodyScripts;

            $page->value = $head;
            $page->value .= $body;
        }
        if($print){
            echo($page);
        }else{
            return $page;
        }
    }

    /**
     * @return bool
     */
    public function isDarkMode(){
        return $this->darkMode;
    }

    /**
     * @param bool $darkMode
     */
    public function setDarkMode(bool $darkMode) {
        $this->darkMode = $darkMode;
    }
} 