<?php
/*Depends: Url, Util*/
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 14.09.2017
 * Time: 15:24
 */

class Resources {

    const NO_FILE_SUBMITTED = 1;
    const FILE_EXISTS = 2;

    /* @var Hub $hub*/
    private $hub;

    function __construct($hub) {
        $this->hub = $hub;
    }

    /**
     * @param $name string icon name containing extension
     * @return string, returns same path if everything is ok
     */
    public function getIcon($name, $publicUrl = false){
        return $this->get(Constants::ICONS_DIR."/".$name, $publicUrl);
    }

    public function getUploadedFile($name){
        return $this->get(Constants::UPLOADED_DIR."/".$name);
    }

    public function getLinkedFileList(){
        return $this->getFilesInDir(Constants::PAGE_IN_FILE_PATH);
    }

    private function get($path, $publicUrl = false){
        if(file_exists($path)){
            if(!$publicUrl){
                return $path;
            }else{
                return Hub::get()->Url->getPrefix().$path;
            }
        }else{
            $this->hub->logIf(
                Messages::format(Messages::$RESOURCE_NOT_FOUND, $path), LogLevel::WARN, Constants::FILE_INFO);
            return Constants::ON_RESOURCE_NOT_FOUND;
        }
    }

    private function getFilesInDir($dir){
        $files = scandir($dir);
        $lim = sizeof($files); //more effective than check every iteration in loop
        for($i = 0; $i < $lim; $i++){
            if($files[$i] == '.' or $files[$i] == '..'){
                array_splice($files, $i, 1);
                $i--;
                $lim--;
            }
        }
        return $files;
    }

    public function getUploadedList(){
        return $this->getFilesInDir(Constants::UPLOADED_DIR);
    }

    public function getExtendedFileList(){
        $files = $this->getUploadedList();
        $filesC = [];
        foreach ($files as $k => $v){
            if(substr($v, 0, 1) != '.'){
                $path = Constants::UPLOADED_DIR.Constants::SLASH.$v;
                $size = filesize($path);
                $filesC[] = [
                    'fileName' => $v,
                    'fileSize' => $size
                ];
            }
        }
        return $filesC;
    }

    public function getResourceList($dir = ''){
        $dir = trim($dir, '/');
        if(strlen($dir) > 0){
            $dir = '/'.$dir;
        }
        return $this->getFilesInDir(Constants::RESOURCES_DIR.$dir);
    }

    public function renameFile($file, $newName){
        $path = Constants::UPLOADED_DIR.Constants::SLASH;
        if(rename($path.$file, $path.$newName)){
            return [
                'status' => AjaxResponse::SUCCESS,
                'file_new_name' => $newName
            ];
        }
        return [
            'status'=> AjaxResponse::UNKNOWN_ERROR
        ];

    }

    public function deleteFile($fileName){
        $path = Constants::UPLOADED_DIR.Constants::SLASH.$fileName;
        Hub::get()->muteWarnings();
        if(unlink($path)){
            return AjaxResponse::SUCCESS;
        }else{
            return AjaxResponse::UNKNOWN_ERROR;
        }
    }

    private function createDraft($baseName){
        $base = pathinfo($baseName, PATHINFO_FILENAME);
        $ext = pathinfo($baseName, PATHINFO_EXTENSION);
        $newName = Constants::UPLOADED_DIR."/".$base."_draft.".$ext;
        copy($baseName, $newName);
        Hub::get()->ImageManipulator->resizeProportionally($newName, 400, 200, false);
        return $newName;
    }

    /**
     * @param $draftName string Filename with _draft in it
     * @return string Filename of original image
     */
    public function getFromDraft($draftName){
        $name = str_replace('_draft', '', pathinfo($draftName, PATHINFO_FILENAME));
        $ext = pathinfo($draftName, PATHINFO_EXTENSION);
        return Constants::UPLOADED_DIR."/".$name.".".$ext;
    }

    private function getUploadErrCause($err){
        switch( $err ) {
            case UPLOAD_ERR_OK:
                return 'OK';
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return 'File too large (limit of '.$this->file_upload_max_size().' bytes - '.($this->file_upload_max_size()/1048576).'MB).';
                break;
            case UPLOAD_ERR_PARTIAL:
                return 'File upload was not completed.';
                break;
            case UPLOAD_ERR_NO_FILE:
                return 'Zero-length file uploaded.';
                break;
            default:
                return 'Internal error #'.$_FILES['newfile']['error'];
                break;
        }
    }

    public function processUpload($createDraft = false){
        $output = [];
        if(sizeof($_FILES) == 0){
            $output['status'] = Resources::NO_FILE_SUBMITTED;
            return $output;
        }
        $output['status'] = AjaxResponse::SUCCESS;
        foreach ($_FILES as $fileKey => $file){ //for each uploaded file
            $randomSeed = Hub::get()->Util->generateRandomString();
            $savedName = Constants::UPLOADED_DIR."/".$randomSeed.'_'.basename($file["name"]);
            if(file_exists($savedName)){ //does file already exist
                $output['content'][$fileKey]['status'] = Resources::FILE_EXISTS; //throw error for this file
                $output['content'][$fileKey]['filename'] = basename($file["name"]);
                $output['status'] = Resources::FILE_EXISTS;
            }else{
                $moveResult = move_uploaded_file($file['tmp_name'], $savedName); //save uploaded file
                if(!$moveResult){
                    $output['status'] = AjaxResponse::UNKNOWN_ERROR;
                    $output['content'] = [
                        'cause' => 'Cannot move uploaded file',
                        'message' => $this->getUploadErrCause($file['error']),
                        'FILES' => $_FILES,
                        'details' => [
                            'TMP_NAME' => $file['tmp_name'],
                            'TARGET' => $savedName,
                        ]
                    ];
                    return $output;
                }
                if($createDraft){
                    $output['content'][$fileKey]['draft'] = $this->createDraft($savedName);
                }
                $imgSize = getimagesize($savedName);
                $imgSize = [$imgSize[0], $imgSize[1]];
                $output['content'][$fileKey]['status'] = AjaxResponse::SUCCESS; //return success code
                $output['content'][$fileKey]['url'] = $this->hub->Url->getPrefix().$savedName; //return url to file
                $output['content'][$fileKey]['size'] = $imgSize;
            }
        }
        return $output;
    }

    private function file_upload_max_size() {
        static $max_size = -1;

        if ($max_size < 0) {
            // Start with post_max_size.
            $post_max_size = $this->parse_size(ini_get('post_max_size'));
            if ($post_max_size > 0) {
                $max_size = $post_max_size;
            }

            // If upload_max_size is less, then reduce. Except if upload_max_size is
            // zero, which indicates no limit.
            $upload_max = $this->parse_size(ini_get('upload_max_filesize'));
            if ($upload_max > 0 && $upload_max < $max_size) {
                $max_size = $upload_max;
            }
        }
        return $max_size;
    }

    private function parse_size($size) {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
        $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
        if ($unit) {
            // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        }
        else {
            return round($size);
        }
    }
}