<?php
/*Depends: SettingsDb*/
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 22.06.2017
 * Time: 00:01
 */

class Url {

    private $URL;
    private $prev_url;
    /* czy nastąpi przekierowanie klienta przez nagłówek
        will client be redirected using header*/
    private $realRedirect;

    private function sanitize($url){
        return explode('?', $url)[0];
    }

    public function init(){
        $this->realRedirect = (bool)Hub::get()->SettingsDb->getSetting('realRedirect');
        $redirect = Hub::get()->SettingsDb->getSetting('redirectOnEmpty');
        $this->prev_url = ((array_key_exists('prev_url', $_SESSION))?$_SESSION['prev_url']:$redirect);
        if($redirect != "" and trim($this->getReal(),"/") == "" and $this->realRedirect){
            $this->redirectTo($redirect);
        }
    }

    public function getInstance(){
        return new Url();
    }

    /*Is $text current relative url*/
    public function isCurrent($url){
        return (trim($this->get(), '/') == $url);
    }

    public function setArtificial($url){
        $subFolder = strlen($this->getPrefix())-1;
        $this->URL = substr($url, $subFolder);
    }

    public function getAjaxSource(){
        $page = $this->getInstance();
        $page->setArtificial($this->sanitize($_SERVER['HTTP_REFERER']));
        return $page->get();
    }

    public function getPrefix(){
        $domain = "http://".$_SERVER['HTTP_HOST'];
        $subFolder = substr($_SERVER['SCRIPT_NAME'], 0, strlen($_SERVER['SCRIPT_NAME'])-9);
        return $domain.$subFolder;
    }

    /**
     * @param $insideLink
     * Redirects to given 'inside' adress.
     */
    public function redirectTo($insideLink){
        $newLocation = $this->getPrefix().trim($insideLink, "/");
        header("Location: ".$newLocation);
    }

    public function getReal(){
        $subFolder = strlen(substr($_SERVER['SCRIPT_NAME'], 0, strlen($_SERVER['SCRIPT_NAME'])-10));
        return substr($this->sanitize($_SERVER['REQUEST_URI']), $subFolder);
    }

    public function get(){
        if(!isset($this->URL)){
            $this->URL = $this->getReal();
        }
        if($this->realRedirect or $this->URL !== "/"){
            Hub::get()->logIf(
                Messages::format(Messages::$RETURNING_URL, $this->URL),
                LogLevel::INFO, Constants::URL_UTILS
            );
            return $this->URL;
        }else{
            Hub::get()->logIf(
                Messages::format(Messages::$RETURN_ON_EMPTY_URL, $this->URL),
                LogLevel::INFO, Constants::URL_UTILS
            );
            return Hub::get()->SettingsDb->getSetting('redirectOnEmpty');
        }
    }

    public function setCurrentLocation($url){
        $_SESSION['prev_url'] = $url;
    }

    public function getPreviousLocation(){
        return $this->prev_url;
    }

}