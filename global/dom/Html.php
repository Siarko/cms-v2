<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 03.03.2018
 * Time: 15:07
 * @property string charset
 * @property string name
 * @property string ref
 * @property string src
 * @property string href
 * @property string content
 * @property string type
 * @property string rel
 * @property string class
 */

class Html {

    public function element($name, $value = null) {
        return new HtmlElement($name, $value);
    }

    public function getRenderer() {
        return new Renderer();
    }

    public function toDomDocument($string){
        $prefix = '<body>';
        $xml = '<?xml encoding="utf-8" ?>'.$prefix.$string.'</body>';
        $document = new DOMDocument();
        $document->registerNodeClass('DOMElement', 'JSLikeHTMLElement');
        $document->formatOutput = false;
        $document->preserveWhiteSpace = false;
        $trap = new DomErrorTrap([$document, 'loadHTML']);
        /*echo($xml);
        exit();*/
        try {
            $trap->call($xml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        } catch (Exception $e) {
            echo($e->getMessage());
        }
        if(!$trap->ok()){
            Hub::logIf("XML PARSER ERRORS OCCURED: ".count($trap->errors()), LogLevel::ERR, Constants::LOG_FILTERS);
            foreach ($trap->errors() as $error) {
                Hub::logIf($error, LogLevel::ERR, Constants::LOG_FILTERS);
            }
        }
        return $document;
    }

    /* @var DOMDocument $domDocument*/
    public function domToHTML($domDocument){
        $prefix = '<body>';
        $content = $domDocument->saveHTML($domDocument->documentElement);
        return substr($content, strlen($prefix), strlen($content)-(strlen($prefix)*2+1));
    }

}