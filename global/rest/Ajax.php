<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 20.10.2017
 * Time: 20:19
 */

class Ajax {
    public function getErrorJSON($code){
        $response = new AjaxResponse($code);
        $response->printPage(Hub::get()->DomBuilder);
    }

    public function success($body = null){
        $response = new AjaxResponse();
        $response->setContent($body);
        $response->printPage(Hub::get()->DomBuilder);
    }

    public function fail($body = null){
        $response = new AjaxResponse();
        $response->setStatus(false);
        $response->setContent($body);
        $response->printPage(Hub::get()->DomBuilder);
    }

    public function processFileUpload(){
        $processingResult = Hub::get()->Resources->processUpload();
        $response = new AjaxResponse(
            $processingResult['status'],
            $processingResult['content']
        );
        $response->printPage(Hub::get()->DomBuilder);
    }

    public function processCTUpload(){ //content tools needs special JSON response
        $processingResult = Hub::get()->Resources->processUpload(true);
        $response = new AjaxResponse(
            $processingResult['status'],
            $processingResult['content']
        );
        $response->printPage(Hub::get()->DomBuilder);
    }

    public function processCTInsert(){ //TODO dodanie wyjątków, np przy usuwaniu plików itp, optymalizacja
        $draftFilename = explode('?',$_POST['url'])[0];
        $cropCoords = explode(',', $_POST['crop']);
        $filename = Hub::get()->Resources->getFromDraft($draftFilename);
        $imageLink = Hub::get()->Url->getPrefix()."/".$filename;
        unlink($draftFilename);
        Hub::get()->ImageManipulator->cropImage($filename, $cropCoords, true);
        $newSize = getimagesize($filename);
        $params = [
            'url' => $imageLink,
            'size' => [$newSize[0], $newSize[1]],
            'atl' => 'image',
            'width' => 500
        ];
        Hub::get()->DomBuilder->jsonMode(true);
        $response = new AjaxResponse(
            AjaxResponse::SUCCESS,
            $params
        );
        $response->printPage(Hub::get()->DomBuilder);
    }

    public function processCTRotate(){
        Hub::get()->DomBuilder->jsonMode(true);
        $direction = (($_POST['direction'] == '1')?-1:1);
        $filename = Constants::UPLOADED_DIR."/".pathinfo($_POST['url'], PATHINFO_BASENAME);
        $originalName = Constants::UPLOADED_DIR."/".str_replace("_draft", '', pathinfo($_POST['url'], PATHINFO_BASENAME));
        Hub::get()->ImageManipulator->rotateImage($filename, $direction);
        Hub::get()->ImageManipulator->rotateImage($originalName, $direction);
        $size = getimagesize($originalName);
        $response = new AjaxResponse(
            AjaxResponse::SUCCESS,
            ['size' => [$size[0],$size[1]],
                'url' => pathinfo($_POST['url'], PATHINFO_BASENAME)]
        );
        $response->printPage(Hub::get()->DomBuilder);
    }

    public function processCTCrop(){
        Hub::get()->DomBuilder->jsonMode(true);
        $filename = Constants::UPLOADED_DIR."/".pathinfo($_POST['url'], PATHINFO_FILENAME);
    }

    public function getUploadedFileList(){
        $body = [
            'url' => Hub::get()->Url->getPrefix().Constants::UPLOADED_DIR,
            'files' => Hub::get()->Resources->getUploadedList()
        ];
        $response = new AjaxResponse(AjaxResponse::SUCCESS, $body);
        $response->printPage(Hub::get()->DomBuilder);
    }

    public function changePageContent(){
        $response = new AjaxResponse(AjaxResponse::SUCCESS);
        $header = @$_POST['header'];
        $content = @$_POST['content'];
        $page = null;
        if(isset($header) or isset($content)){
            $page = Hub::get()->Page->getInstance(Hub::get()->Url->getAjaxSource());
        }
        if(isset($header)){
            $page->changeHeader($header);
            $response->setContent('header', ['status' => AjaxResponse::SUCCESS]);
        }
        if(isset($content)){
            $result = $page->changeContent($content);
            $response->setContent('content', ['status' => $result]);
        }

        $response->printPage(Hub::get()->DomBuilder);
    }

    public function paramsMissingError($getMissing) {
        $this->fail([
            'cause' => 'params missing',
            'missing' => $getMissing
        ]);
    }
}