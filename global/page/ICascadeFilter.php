<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 20.04.2019
 * Time: 02:50
 */

interface ICascadeFilter {
    public static function getCssClass();
    public static function getDescription();

    public function apply(Page $page, array $data);
}