<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 21.10.2017
 * Time: 14:50
 */

class ImageManipulator {

    const DIR_CW = 1; //cloclwise
    const DIR_CCW = -1; //counter clockwise

    private function getImageResource($filename){ //create resource from jpg, jpeg, png, bmp
        $type = exif_imagetype($filename); // [] if you don't have exif you could use getImageSize()
        $allowedTypes = array(
            1,  // [] gif
            2,  // [] jpg
            3,  // [] png
        );
        if (!in_array($type, $allowedTypes)) {
            return false;
        }
        $im = null;
        switch ($type) {
            case 1 :
                $im = imageCreateFromGif($filename);
                break;
            case 2 :
                $im = imageCreateFromJpeg($filename);
                break;
            case 3 :
                $im = imageCreateFromPng($filename);
                break;
        }
        return $im;
    }

    private function saveImage($filename, $imageResource){
        $type = pathinfo($filename, PATHINFO_EXTENSION);
        switch (strtolower($type)){
            case 'jpg':
            case 'jpeg':
                imagejpeg($imageResource, $filename);
                break;
            case 'png':
                imagepng($imageResource, $filename);
                break;
        }
    }

    public function rotateImage($filename, $direction){
        $image = $this->getImageResource($filename);
        $image = imagerotate($image, 90*$direction, 0);
        $this->saveImage($filename, $image);
        imagedestroy($image);
    }

    public function resizeProportionally($filename, $maxW, $maxH, $return = true){
        $image = $this->getImageResource($filename);
        $info = getimagesize($filename);
        $ratio = $info[0]/$info[1]; //width/height
        if($ratio > 1){ //height greater than width
            $w = $maxW;
            $h = $maxW/$ratio;
        }else{ //width greater than height
            $h = $maxH;
            $w = $maxH/$ratio;
        }
        $dst = imagecreatetruecolor($w, $h);
        imagecopyresampled($dst, $image, 0,0,0,0,$w, $h, $info[0], $info[1]);
        if($return){return $dst;}
        $this->saveImage($filename, $dst);
        imagedestroy($image);
        imagedestroy($dst);
    }

    /**
     * modifies image given in $filename for ContentTools
     * crop positions aren't pixels, they are proportions; 0,0 -> left top corner, 1,1 -> right bottom
     * @param $filename string
     * @param $xp1 float (0-1)
     * @param $yp1 float (0-1)
     * @param $xp2 float (0-1)
     * @param $yp2 float (0-1)
     * @param $overwrite bool Save modified image to disk?
     */
    public function cropImage($filename, $cropCoords, $overwrite = false){
        $size = getimagesize($filename);
        $rectangle = [
            'x' => $size[0]*$cropCoords[1], 'y' => $size[1]*$cropCoords[0],
            'width' => ($size[0]*$cropCoords[3])-($size[0]*$cropCoords[1]),
            'height' => ($size[1]*$cropCoords[2])-($size[1]*$cropCoords[0])
        ];
        $image = $this->getImageResource($filename);
        $image = imagecrop($image, $rectangle);
        if($overwrite){
            $this->saveImage($filename, $image);
            imagedestroy($image);
        }
        return $image;
    }
}