<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 10.08.2018
 * Time: 18:35
 */
class ClassLoader{
    private $directoryMap = [];

    function __construct() {
        spl_autoload_register(self::class.'::loadClass');

        $this->directoryMap = $this->createDirectoryMap(__DIR__.'/../../'.Constants::PLUGIN_DIR);
    }

    public function loadClass($data){
        $path = str_replace('\\', DIRECTORY_SEPARATOR, $data);
        if(explode(DIRECTORY_SEPARATOR, $path)[0] == 'hub'){
            $path2 = $path.'.php';
        }else{
            $path2 = Constants::PLUGIN_DIR.$path.'.php';
        }
        if(file_exists($path2)){
            require_once ($path2);
        }else{
            $p = explode(DIRECTORY_SEPARATOR, $path);
            $name = end($p);
            $this->loadFromMap($name);
        }
    }

    private function createDirectoryMap($root = __DIR__) {
        $dirMap = [];
        $dir = new RecursiveDirectoryIterator($root,RecursiveDirectoryIterator::SKIP_DOTS);
        /** @var RecursiveDirectoryIterator $file */
        foreach (new RecursiveIteratorIterator($dir) as $file) {
            if ($file->isDir()) {
                continue;
            }
            if ($file->getExtension() == "php") {
                $name = $file->getBasename(".php");
                $path = substr($file->getPath(), strlen($root));
                if(!key_exists($name, $dirMap)){
                    $dirMap[$name] = $path;
                }
            }
        }
        return $dirMap;
    }

    private function loadFromMap($name) {
        if(array_key_exists($name, $this->directoryMap)){
            $path = __DIR__.'/../../'.Constants::PLUGIN_DIR.$this->directoryMap[$name].DIRECTORY_SEPARATOR.$name.'.php';
            require_once($path);
        }
    }
}