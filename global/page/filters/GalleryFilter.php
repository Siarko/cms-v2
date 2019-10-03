<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 08.05.2019
 * Time: 23:30
 */

namespace page\filters;


use DOMNode;
use Page;
use page\CascadeFilter;

class GalleryFilter extends CascadeFilter {

    public static function getCssClass() {
        return 'galleryFilter';
    }

    public static function getDescription() {
        return 'Tworzenie galerii zdjęć';
    }

    public function apply(Page $page, array $data) {
        return '';
    }

    public static function registerRoutes() {
        // TODO: Implement registerRoutes() method.
    }
}