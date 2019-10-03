<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 10.08.2018
 * Time: 20:40
 */


class Renderer {
    private $_filePath;

    public function setFilePath($filePath){
        $this->_filePath = $filePath;
    }

    public function render($filePath = null) {
        if($filePath == null){
            $filePath = $this->_filePath;
        }
        if(!file_exists($filePath)){
            Constants::logCrit("FILE NOT EXISTS", "Error while rendering file", "Path: ".$filePath);
        }
        ob_start();
        require $filePath;
        return ob_get_clean();
    }

    function __set($name, $value){
        $this->$name = $value;
    }

    function __get($name){
        if(Constants::DEV_MODE){
            return "VNS_Err - ".$name;
        }
        return '';
    }

    /**
     * @param $variables Variables
     */
    public function importVariables($variables) {
        $variables = $variables->_getVars();
        foreach ($variables as $k => $v){
            $this->$k = $v;
        }
    }
}