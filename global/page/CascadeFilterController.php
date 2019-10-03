<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 20.04.2019
 * Time: 02:28
 */

class CascadeFilterController {
    public function getFilterList(){
        $dir = __DIR__.DIRECTORY_SEPARATOR.'filters';
        $list = Hub::get()->File->getFilesInDir($dir);
        $res = [];
        foreach ($list as $item) {
            /* @var \page\filters\CatalogFilter $class*/
            $class = '\\page\\filters\\'.$item['filename'];
            $filterData['filename'] = $class;
            $filterData['cssClass'] = $class::getCssClass();
            $filterData['description'] = $class::getDescription();
            $res[] = $filterData;
        }
        return $res;
    }

    public function getHandlers(){
        $list = $this->getFilterList();
        $result = [];
        foreach ($list as $item) {
            $result[$item['cssClass']] = $item['filename'];
        }
        return $result;
    }

    public function updateSettings($id, $data, $url = null){
        if($url == null){
            $url = trim(Hub::get()->Url->getAjaxSource(), '/\\');
        }
        $page = \models\Pages::find($url)->one();
        if(!$page){return null;}
        $localization = \models\PageLocalizations::find([
            \models\PageLocalizations::URL => $url,
            \models\PageLocalizations::LANGUAGE => Hub::get()->LanguageController->getPreferredLanguage()
            ])->one();
        $content = $localization->content_text;
        $document = Hub::get()->Html->toDomDocument($content);
        $query = new DOMXPath($document);
        $nodes = $query->query('//x-dynamic['.($id+1).']');
        if($nodes->length == 1){
            $node = $nodes->item(0);
            foreach ($data as $name => $value) {
                $node->setAttribute('data-'.$name, $value);
            }
            $localization->content_text = Hub::get()->Html->domToHTML($document);
            if($localization->save()){
                return Hub::get()->CascadeFilterController->generateFilter($url, $data['type'], $data);
            }else{
                return false;
            }
        }
        return false;


    }

    public function generateFilter($url, $type, $data){
        $handlers = $this->getHandlers();
        $handler = $handlers[$type];
        /* @var $filter \page\CascadeFilter*/
        $filter = new $handler();
        return $filter->applyWrapper(Hub::get()->Page->getInstance($url), $data);

    }

    public function registerFilterRoutes(String $prefix, Rest $rest) {
        $handlers = $this->getHandlers();
        /* @var \page\CascadeFilter $handler*/
        foreach ($handlers as $name => $handler) {
            $n = strtolower(preg_replace('/([A-Z]+)/', "_$1", $name));
            $p = $prefix.$n;
            Hub::get()->Rest->setPrefix($p);
            $handler::registerRoutes();
            Hub::get()->Rest->popPrefix();
        }
    }

}