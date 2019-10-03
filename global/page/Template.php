<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 01.07.2017
 * Time: 14:34
 */

class Template {

    /* @var Renderer $renderer*/
    private $renderer;

    public function init(){
        $this->renderer = Hub::get()->Html->getRenderer();
    }

    /**
     * @param number $id Id of template in DataBase
     * @return Template New template instance loaded from DataBase
     */
    public function getInstance($id = null, $path = null){
        $ret = new Template();
        $ret->init();
        if($id !== null){
            $ret->loadFromDb($id);
        }elseif ($path !== null){
            $ret->renderer->setFilePath($path);
        }else{
            Hub::get()->log("Tried to load template, no ID given!", LogLevel::WARN);
        }
        return $ret;
    }

    public function getTemplateList(){
        return \models\Templates::find()->asArray();
    }

    /**
     * @param $id number Template Id in database
     * Loads template text from database
     */
    public function loadFromDb($id){
        $fileName = Hub::get()->TemplateDb->get($id);
        $path = $this->getTemplatesPath().$fileName;
        $this->renderer->setFilePath($path);
    }


    private function getTemplatesPath(){
        return Constants::ROOT_DIR.Constants::SLASH.Constants::TEMPLATES_DIR.Constants::SLASH;
    }

    /**
     * @return string
     * Parsuje wszystkie zmienne w tekście na podstawie wszystkich dostępnych zmiennych
     */
    public function parse(){
        $this->renderer->importVariables(Hub::get()->Variables);
        return $this->renderer->render();
    }
} 