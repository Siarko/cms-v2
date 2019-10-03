<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 03.03.2018
 * Time: 14:22
 */

class ResourceType{
    const JS = 0;
    const CSS = 1;
}

class Assets {

    public function loadBundle($assetLocation){
        $jsLocation = Constants::ROOT_DIR.Constants::SLASH.Constants::JS_DIR.Constants::SLASH;
        $cssLocation = Constants::ROOT_DIR.Constants::SLASH.Constants::CSS_DIR.Constants::SLASH;
        $this->loadFiles($jsLocation, $assetLocation['js'], ResourceType::JS);
        $this->loadFiles($cssLocation, $assetLocation['css'], ResourceType::CSS);
    }

    private function loadFiles($location, $assetDir, $resourceType){
        if(!file_exists($location)){return;}
        $location .= $assetDir;
        $iterator = scandir($location);
        $baseUrl = Hub::get()->Url->getPrefix();
        foreach ($iterator as $k => $entry){
            if(!is_dir($location.Constants::SLASH.$entry)){
                switch ($resourceType){
                    case ResourceType::JS:{
                        Hub::get()->DomBuilder->addScript(substr($assetDir.$entry, 0, -3));
                        break;
                    }
                    case ResourceType::CSS:{
                        if(strripos($entry, '.colors.') != false){continue 2;}
                        Hub::get()->DomBuilder->addStyle(substr($assetDir.$entry, 0, -4));
                        break;
                    }
                    default:{
                        Hub::get()->log("LOADING UNKNOWN ASSET TYPE", LogLevel::ERR);
                    }
                }
            }
        }
    }
}