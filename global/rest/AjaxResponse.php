<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 15.08.2018
 * Time: 16:35
 */

class AjaxResponse{

    const SUCCESS = 0;
    const PAGE_NOT_FOUND = 1;
    const ACCESS_DENIED = 3;
    const UNKNOWN_ERROR = 4;
    const USER_NOT_FOUND = 5;
    const MYSQL_ERROR = 6;

    const STATUS_NAMES = [
        "SUCCESS" => AjaxResponse::SUCCESS,
        "PAGE_NOT_FOUND" => AjaxResponse::PAGE_NOT_FOUND,
        "ACCESS_DENIED" => AjaxResponse::ACCESS_DENIED,
        "UNKNOWN_ERROR" => AjaxResponse::UNKNOWN_ERROR,
        "USER_NOT_FOUND" => AjaxResponse::USER_NOT_FOUND,
        "MYSQL_ERROR" => AjaxResponse::MYSQL_ERROR
    ];

    private $status;
    private $content;

    function __construct($status = AjaxResponse::SUCCESS, $content = null) {
        $this->status = $status;
        $this->content = $content;
    }

    public function setStatus($status){
        $this->status = $status;
    }

    public function setContent($content, $value = null) {
        if($value == null){
            $this->content = $content;
        }else{
            $this->content[$content] = $value;
        }
    }

    public function get(){
        return json_encode([
            'status' => $this->status,
            'body' => $this->content
        ]);
    }

    /**
     * @param $domBuilder DomBuilder
     */
    public function printPage($domBuilder){
        $domBuilder->jsonMode(true);
        $domBuilder->body($this->get());
    }
}