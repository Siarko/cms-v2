<?php
/*Depends: StructureDb, Action, TemplateUtils, Variables*/
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 28.01.2018
 * Time: 17:19
 */

class Menu {
    

    /* @var PagePart $pagePart*/
    public function process($pagePart){

        $this->loadAssets($pagePart);
        $renderer = Hub::get()->Html->getRenderer();
        $renderer->menuStructure = $this->getStructure();
        $renderer->importVariables(Hub::get()->Variables);
        /*$parser = Hub::get()->Html->getParser();
        $parser->addVariables(Hub::get()->Variables->getLoaded());
        $parser->addVariable("MENULINK", $this->formatStructure($this->getStructure()));
        $parser->setText($pagePart->getContent());*/


        return $renderer->render($pagePart->getFilePath());
    }


    private function formatStructure($structure){
        foreach ($structure as $k => $v){
            if($v['menuvisibility'] == 0){
                unset($structure[$k]);
            }
        }
        return $structure;
    }

    public function getStructure($ignorePerms = false){
        $structure = Hub::get()->StructureDb->getPageList();
        if($ignorePerms){return $structure;}
        foreach ($structure as $k => &$v ){
            if(!Hub::get()->Action->validate($v['permlevel'])){
                unset($structure[$k]);
            }
        }
        return $structure;
    }

    public function getJSONStructure(){
        $structure = $this->getStructure();
        return json_encode(['status' => 1, 'content' => $structure], JSON_PRETTY_PRINT);
    }

    /**
     * @param $pagePart PagePart
     */
    private function loadAssets($pagePart){
        Hub::get()->Assets->loadBundle($pagePart->getAssetLocation());
    }
}