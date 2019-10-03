<?php
/*Depends: PagePartDb */

/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 01.08.2017
 * Time: 10:07
 */

class PagePart {
    private $id;
    private $fileName;
    private $content;
    private $permLevel;
    private $hub;

    /* @var Hub $hub*/
    function __construct($hub) {
        $this->hub = $hub;
    }

    public function getFileName(){
        return $this->fileName;
    }

    public function getFilePath(){
        return Constants::ROOT_DIR.Constants::SLASH.Constants::PAGE_PART_DIR.Constants::SLASH.$this->getFileName();
    }

    public function getInstance($content = null, $permLevel = null, $id = null, $fileName = null){
        $part = new PagePart($this->hub);
        $part->content = $content;
        $part->permLevel = $permLevel;
        $part->id = $id;
        $part->fileName = $fileName;
        return $part;
    }

    public function setContent($text){
        $this->content = $text;
    }

    public function get($id){
        $ret = $this->hub->PagePartDb->getPart($id);
        $content = $this->loadFromFile($ret[0]);
        return $this->getInstance($content, $ret[1], $id, $ret[0]);
    }

    public function getAssetLocation(){
        return [
            "js" => Constants::PAGE_PART_JS_DIR.Constants::SLASH.$this->id.Constants::SLASH,
            "css" => Constants::PAGE_PART_CSS_DIR.Constants::SLASH.$this->id.Constants::SLASH
        ];
    }

    /**
     * @return string
     */
    public function getContent() {
        return $this->content;
    }

    private function loadFromFile($filename){
        return $this->hub->File->getPagePart($filename);
    }

    /**
     * @return number
     */
    public function getPermLevel() {
        return $this->permLevel;
    }

    public function bake(){
        return $this->hub->Variables->parse($this->getContent());
    }
} 