<?php
/*Depends: Url*/
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 24.06.2017
 * Time: 00:34
 */

class Request {

    private $type;
    private $url;

    public function init(){
        @$this->type = $_GET["_METHOD"] ?: $_POST["_METHOD"] ?: $_SERVER['REQUEST_METHOD'];
        $this->url = Hub::get()->Url->get();
    }

    public function getPost(){
        $abstract = Hub::get()->AbstractObject->getInstance();
        $abstract->fill($_POST);
        return $abstract;
    }

    /**
     * @param array $data
     */
    public function requirePost($data = []){
        $postData = $this->getPost();
        $missing = [];
        foreach ($data as $element) {
            if(!$postData->exists($element)){
                $missing[] = $element;
            }
        }
        return new FormData($postData, $missing);

    }

    public function getType(){
        return $this->type;
    }

    public function getUrl(){
        return $this->url;
    }

    public function getRestUrl(){
        $url = $this->getUrl();

        return $url;
    }

    public function getBody(){
        return file_get_contents("php://input");
    }

    public function forwardBack(){
        /*TODO Przekierowanie po wylogowaniu, Na pewno, tylko nie wiem gdzie.*/
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }

} 