<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 20.04.2019
 * Time: 02:20
 */

namespace page\filters;

use database\sqlCreator\Condition;
use models\PageLocalizations;
use models\Pages;
use Page;
use page\CascadeFilter;
use Rest;

class CatalogFilter extends CascadeFilter {

    public static function getCssClass() {
        return 'subpageCatalogue';
    }

    public static function getDescription() {
        return 'Zmienia stronę na katalog podstron';
    }

    private function findAllLocalizations($url){
        $permLevel = \Hub::get()->Auth->getLogged()->getPerms();
        $list = \Hub::get()->StructureDb->getPageList([
            [Pages::ID => $url.'/%', Condition::LIKE],
            [Pages::PERMLEVEL => $permLevel, Condition::GREATER_OR_EQUAL]
        ]);
        return $list;
    }

    public function apply(Page $page, array $data) {
        $pages = $this->findAllLocalizations($page->getUrl());
        $renderer = $this->getRenderer();
        $renderer->pages = $pages;
        return $renderer->render();
    }


    public static function registerRoutes() {
        \Hub::get()->Rest->get('/get_possible', function (){
            \Hub::get()->DomBuilder->jsonMode(true);
        });
    }
}