<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 20.04.2019
 * Time: 02:20
 */

namespace page;

use Constants;
use LogLevel;
use Page;
use Rest;

abstract class CascadeFilter implements \ICascadeFilter {

    private static $usedFilters = [];

    public static function includeFilterCss(){
        foreach (self::$usedFilters as $item) {
            $path = Constants::FILTERS_CSS.DIRECTORY_SEPARATOR.$item;
            \Hub::get()->DomBuilder->addStyle($path);
        }
    }

    public function onContentRender($content){
        return $content;
    }

    protected function getUsableClassName(){
        $name = explode('\\', static::class);
        return $name[count($name)-1];
    }

    protected function getRenderer($templateName = null){
        if($templateName == null){
            $templateName = $this->getUsableClassName();
        }
        $templateName .= 'Template.php';
        \Hub::logIf("Loading template: ".$templateName, LogLevel::INFO, Constants::LOG_FILTERS);
        $renderer = \Hub::get()->Html->getRenderer();
        $renderer->setFilePath(Constants::FILTER_TEMPLATE_DIR.DIRECTORY_SEPARATOR.$templateName);
        return $renderer;
    }

    public function applyWrapper(Page $page, array $data){
        $name = lcfirst($this->getUsableClassName());
        if(!in_array($name, self::$usedFilters)){
            self::$usedFilters[] = $name;
        }
        \Hub::logIf("FILTER APPLIED: ".static::class, LogLevel::INFO, Constants::LOG_FILTERS);
        return $this->apply($page, $data);
    }

    public static abstract function registerRoutes();
}