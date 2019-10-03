<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 03.03.2018
 * Time: 13:25
 */

class File {
    private static function getFile($path, $fileName){
        if(!file_exists($path)){
            Hub::get()->log("PageService template file doesn't exist! file=".$fileName, LogLevel::ERR);
            return null;
        }
        return file_get_contents($path);
    }

    public function getPageTemplate($fileName){
        $path = Constants::ROOT_DIR.Constants::SLASH.Constants::TEMPLATES_DIR.Constants::SLASH.$fileName;
        return self::getFile($path, $fileName);
    }

    public function getPagePart($fileName){
        $path = Constants::ROOT_DIR.Constants::SLASH.Constants::PAGE_PART_DIR.Constants::SLASH.$fileName;
        return self::getFile($path, $fileName);
    }

    public function getFilesInDir($directory, $includeDirs = false){
        $files = scandir($directory);
        $lim = sizeof($files); //more effective than check every iteration in loop
        for($i = 0; $i < $lim; $i++){
            $fullPath = $directory.DIRECTORY_SEPARATOR.$files[$i];
            $isDir = is_dir($fullPath);
            if($files[$i] == '.' or $files[$i] == '..' or ($isDir and !$includeDirs)){
                array_splice($files, $i, 1);
                $i--;
                $lim--;
                continue;
            }
            $pi = pathinfo($fullPath);
            $files[$i] = [
                'basename' => $files[$i],
                'extension' => $pi['extension'],
                'filename' => $pi['filename'],
                'dir' => $isDir
            ];
        }
        return $files;
    }
}