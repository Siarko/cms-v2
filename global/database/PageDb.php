<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 29.06.2017
 * Time: 21:53
 */

class PageDb extends Db{

    const SUCCESS = 0;
    const PAGE_NOT_EXISTS = 1;
    const UNKNOWN_ERROR = 2;

    public function init(){
        if(!$this->tableExists(Constants::$PAGES_TABLE)){
            Hub::get()->log("Table Pages does not exist!", LogLevel::ERR);
        }
    }

    public function exists($id , $a = null, $b = null){
        $id = $this->filter($id);
        $res = $this->query("SELECT EXISTS(SELECT * FROM pages WHERE id='".$id."') as 'exists'");
        return $res->fetch_assoc()['exists'];
    }

    /**
     * @param $page Page
     */
    public function loadPage($page){
        $url = $this->filter($page->getUrl());
        /* @var \models\Pages $p*/
        $p = \models\Pages::find($url)->one();
        //$ret = $this->query("SELECT header, content, templateId, permLevel, customCss, linkedFile
        //                         FROM pages WHERE id='".$url."'");
        if($p !== null){
            $page->setTemplateId($p->templateid);
            $page->setPermLevel($p->permlevel);
            $page->setCustomCss($p->customcss);
            $localization = $p->findLocalization(
                Hub::get()->LanguageController->getPreferredLanguage(),
                Constants::DEFAULT_LANGUAGE
            );
            if($localization != null){
                if($localization->linked_file == null){
                    $page->setHeader(nc($localization->header_text, ''));
                    $page->setContent($localization->content_text);
                    $page->filterContent();
                    return null;
                }else{
                    return Constants::PAGE_IN_FILE_PATH.Constants::SLASH.$localization->linked_file; //return path to file
                }
            }else{
                $page->setHeader('not av');
                $page->setContent('not av content');
            }

        }
        return null;
    }

    /**
     * @param $page Page
     */
    public function changeHeader($page){
        $header = $this->filter($page->getHeader());
        if(!$page->exists()){return PageDb::PAGE_NOT_EXISTS;}
        $url = $page->getUrl();
        /* @var \models\Pages $resource*/
        $resource = \models\Pages::find($url)->one();
        $localization = $resource->findLocalization();
        $localization->header_text = $header;
        return (($localization->save())?PageDb::SUCCESS:PageDb::UNKNOWN_ERROR);
    }

    private function findFirstImg($content){
        $prefix = '<body>';
        $xml = '<?xml encoding="utf-8" ?>'.$prefix.$content.'</body>';
        $document = new DOMDocument();
        $document->registerNodeClass('DOMElement', 'JSLikeHTMLElement');
        $document->formatOutput = false;
        $document->preserveWhiteSpace = false;
        $trap = new DomErrorTrap([$document, 'loadHTML']);

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

        $finder = new DomXPath($document);
        $img = $finder->query("//img[1]/@src");
        if($img->length == 1){
            /* @var $attr DOMAttr*/
            return str_replace('\"', '', $img->item(0)->nodeValue);
        }
        return null;
    }

    private function filterContent($content){
        $prefix = '<body>';
        $xml = '<?xml encoding="utf-8" ?>'.$prefix.$content.'</body>';
        $document = new DOMDocument();
        $document->registerNodeClass('DOMElement', 'JSLikeHTMLElement');
        $document->formatOutput = false;
        $document->preserveWhiteSpace = false;
        $trap = new DomErrorTrap([$document, 'loadHTML']);
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

        $xp = new DOMXPath($document);
        $nodes = $xp->query('//x-dynamic');
        /* @var DOMNode $node*/
        foreach ($nodes as $node) {
            $node->textContent = '';
        }

        $content = $document->saveHTML($document->documentElement);
        return substr($content, strlen($prefix), strlen($content)-(strlen($prefix)*2+1));
    }

    /**
     * @param $page Page
     */
    public function changeContent($page){
        $content = $page->getContent();
        if(!$page->exists()){return PageDb::PAGE_NOT_EXISTS;}
        $url = $page->getUrl();
        /* @var \models\Pages $resource*/
        $resource = \models\Pages::find($url)->one();
        $localization = $resource->findLocalization();
        $src = $this->findFirstImg($content);
        if(!$localization->custom_main_image){
            $localization->main_image = $src;
        }
        $localization->content_text = $this->filter($this->filterContent($content));

        return(($localization->save())?PageDb::SUCCESS:PageDb::UNKNOWN_ERROR);

    }
} 