<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 16.03.2018
 * Time: 00:05
 */

class RoutingTable {

    /* @var Rest $rest */
    public function registerRoutes($rest) {
        $this->registerMenuRoutes($rest);
        $this->registerFileUploads($rest);
        $this->registerUserRoutes($rest);
        $this->registerFilterRoutes($rest);
        $this->registerPageRoutes($rest);
        $this->registerTemplateRoutes($rest);

        $this->registerRuntimeRoutes($rest);

        $this->registerFileMethods($rest);
        $this->registerMiscRoutes($rest);

        $this->registerDefaultRoute($rest);
    }

    /* @var Rest $rest */
    private function registerMenuRoutes($rest) {
        $rest->get('#api/menuBelt/get#', function () {
            Hub::get()->DomBuilder->jsonMode(true);
            Hub::get()->DomBuilder->body(Hub::get()->menu->getJSONStructure());
        });
    }

    /* @var Rest $rest */
    private function registerUserRoutes($rest) {
        $rest->post('#api/user/create#', function () {
            if (isset($_POST['name'], $_POST['pass'], $_POST['perm'])) {
                $user = Hub::get()->User->getNew();
                $user->create($_POST['name'], $_POST['pass'], $_POST['perm']);
                $result = $user->createInDb();
                Hub::get()->Ajax->success($result);
            }
        });

        $rest->get('#api/user/delete/(?<userid>.*)#', function ($userId) {
            $userId = $userId['userid'];
            if (Hub::get()->Action->validateAction(Actions::CAN_DELETE_USERS)) {
                $result = Hub::get()->User->delete($userId);

                Hub::get()->Ajax->success($result);
                //Hub::get()->Ajax->success(AjaxResponse::SUCCESS);
            } else {
                Hub::get()->Ajax->getErrorJSON(AjaxResponse::ACCESS_DENIED);
            }
        });

        $rest->post('#api/user/login#', function () {
            /* @var User $user */
            $user = Hub::get()->User->getNew();
            $user->create(@$_POST['name'], @$_POST['pass']);
            $user->login();
        });

        $rest->get('#api/user/logout#', function () {
            Hub::get()->User->logout();
            Hub::get()->Request->forwardBack();
        });

        $rest->get('#api/user/getall#', function () {
            if (Hub::get()->Action->validateAction(Actions::CAN_LIST_USERS)) {
                $users = Hub::get()->User->getAllUsers();
                Hub::get()->Ajax->success($users);
            } else {
                Hub::get()->Ajax->getErrorJSON(AjaxResponse::ACCESS_DENIED);
            }
        });

        $rest->get('#api/auth/get-avaiable-perm-levels#', function () {
            if (Hub::get()->Action->validateAction(Actions::CAN_LIST_PERM_LEVELS)) {
                $users = Hub::get()->PermLevels->listAll();
                Hub::get()->Ajax->success($users);
            } else {
                Hub::get()->Ajax->getErrorJSON(AjaxResponse::ACCESS_DENIED);
            }
        });

        $rest->post('#api/user/modify/(?<userid>.*)#', function ($userId) {
            if (Hub::get()->Action->validateAction(Actions::CAN_MODIFY_USERS)) {
                $userId = $userId['userid'];
                $result = Hub::get()->User->modify($userId);
                Hub::get()->Ajax->success($result);
            } else {
                Hub::get()->Ajax->getErrorJSON(AjaxResponse::ACCESS_DENIED);
            }
        });
    }

    /* @var Rest $rest*/
    private function registerFilterRoutes($rest){
        $rest->get('#api/filters/all#', function(){
            $list = Hub::get()->CascadeFilterController->getFilterList();
            Hub::get()->Ajax->success($list);
        });

        $rest->post('#api/filters/settings/update#', function(){
            $data = Hub::get()->Request->requirePost(['id', 'settings']);
            if($data->isComplete()){
                $data = $data->getData();
                $result = Hub::get()->CascadeFilterController->updateSettings(
                    $data->id,
                    $data->settings
                );

                Hub::get()->Ajax->success($result);
            }else{
                Hub::get()->Ajax->paramsMissingError($data->getMissing());
            }
        });

        Hub::get()->CascadeFilterController->registerFilterRoutes('api/filters/own/', $rest);



    }

    /* @var Rest $rest */
    private function registerPageRoutes($rest) {
        $rest->post('#api/page/content/change#', function () {
            if (Hub::get()->Action->validateAction(Actions::CHANGE_PAGE_CONTENT)) {
                Hub::get()->Ajax->changePageContent();
            } else {
                Hub::get()->Ajax->getErrorJSON(AjaxResponse::ACCESS_DENIED);
            }

        });

        $rest->post('#api/page/add/simple#', function () {
            if (Hub::get()->Action->validateAction(Actions::ADD_NEW_PAGE)) {
                $data = Hub::get()->Request->requirePost(['page_url', 'page_template']);
                if ($data->isComplete()) {
                    $data = $data->getData();
                    $result = Hub::get()->Page->addNew($data->page_url, null, $data->page_template);
                    //Hub::get()->Ajax->success($data->getData());
                    Hub::get()->Ajax->success($result);
                } else {
                    Hub::get()->Ajax->paramsMissingError($data->getMissing());
                }
            } else {
                Hub::get()->Ajax->getErrorJSON(AjaxResponse::ACCESS_DENIED);
            }
        });
        $rest->post('#api/page/add#', function () {
            if (Hub::get()->Action->validateAction(Actions::ADD_NEW_PAGE)) {
                $data = Hub::get()->Request->requirePost(['page_url', 'page_name', 'page_template', 'language']);
                if ($data->isComplete()) {
                    $data = $data->getData();
                    $result = Hub::get()->Page->addNew($data->page_url, $data->page_name, $data->page_template, $data->language);
                    //Hub::get()->Ajax->success($data->getData());
                    Hub::get()->Ajax->success($result);
                } else {
                    Hub::get()->Ajax->paramsMissingError($data->getMissing());
                }
            } else {
                Hub::get()->Ajax->getErrorJSON(AjaxResponse::ACCESS_DENIED);
            }
        });

        $rest->get('#api/page/delete/localization/(?<pageId>.*)/(?<lang>.*)#', function($params){
            $result = Hub::get()->Page->deleteLocalization($params['pageId'], $params['lang']);
            Hub::get()->Ajax->success($result);
        });

        $rest->get('#api/page/delete/(?<pageId>.*)#', function($id){
            $id = $id['pageId'];
            $result = Hub::get()->Page->delete($id);
            Hub::get()->Ajax->success($result);
        });



        $rest->post('#api/page/menuname/(?<pageId>.*)/(?<lang>.*)#', function ($params) {
            $id = $params['pageId'];
            $lang = $params['lang'];
            $data = Hub::get()->Request->requirePost(['page_menu_name']);
            if ($data->isComplete()) {
                $data = $data->getData();
                $newName = $data->page_menu_name;
                $result = Hub::get()->Page->changeMenuName($id, $lang, $newName);
                Hub::get()->Ajax->success($result);
            } else {
                Hub::get()->Ajax->paramsMissingError($data->getMissing());
            }
        });

        $rest->post('#api/page/template/(?<pageId>.*)#', function ($id) {
            $id = $id['pageId'];
            $data = Hub::get()->Request->requirePost(['template_id']);
            if ($data->isComplete()) {
                $data = $data->getData();
                $newTemplate = $data->template_id;
                $result = Hub::get()->Page->changePageTemplate($id, $newTemplate);
                Hub::get()->Ajax->success($result);
            } else {
                Hub::get()->Ajax->paramsMissingError($data->getMissing());
            }
        });

        $rest->post('#api/page/menuvisibility/(?<pageId>.*)/(?<lang>.*)#', function($params){
            $id = $params['pageId'];
            $lang = $params['lang'];
            $data = Hub::get()->Request->requirePost(['state']);
            if($data->isComplete()){
                $data = $data->getData()->state;
                /* @var \models\PageLocalizations $localization*/
                $localization = \models\PageLocalizations::find([
                    \models\PageLocalizations::URL => $id,
                    \models\PageLocalizations::LANGUAGE => $lang
                ])->one();
                $localization->menu_visibility = $data;
                Hub::get()->Ajax->success($localization->save());
            }else{
                Hub::get()->Ajax->paramsMissingError($data->getMissing());
            }
        });

        $rest->post('#api/page/permissions/(?<pageId>.*)#', function($params){
            $id = $params['pageId'];
            $data = Hub::get()->Request->requirePost(['level']);
            if($data->isComplete()){
                $data = $data->getData()->level;
                $result = Hub::get()->Page->change($id, 'permlevel', $data);
                Hub::get()->Ajax->success($result);
            }else{
                Hub::get()->Ajax->paramsMissingError($data->getMissing());
            }
        });

        $rest->post('#api/page/language/(?<pageId>.*)/(?<lang>.*)#', function($params){
            $id = $params['pageId'];
            $oldLang = $params['lang'];
            $data = Hub::get()->Request->requirePost(['language']);
            if($data->isComplete()){
                $data = $data->getData()->language;
                $result = Hub::get()->Page->setLanguage($id, $oldLang, $data);
                Hub::get()->Ajax->success($result);
            }else{
                Hub::get()->Ajax->paramsMissingError($data->getMissing());
            }
        });

        $rest->post('#api/page/linked_file/(?<pageId>.*)/(?<lang>.*)#', function($params){
            $data = Hub::get()->Request->requirePost(['file']);
            if($data->isComplete()){
                $data = $data->getData()->file;
                $result = Hub::get()->Page->setLinkedFile($params['pageId'], $params['lang'], $data);
                Hub::get()->Ajax->success($result);
            }else{
                Hub::get()->Ajax->paramsMissingError($data->getMissing());
            }
        });

        $rest->get('#api/page/linked_files#', function(){
            $list = Hub::get()->Resources->getLinkedFileList();
            Hub::get()->Ajax->success($list);
        });

        $rest->get('#api/page/single/unlocalized/(?<pageId>.*)#', function ($id) {
            $id = $id['pageId'];
            $list = Hub::get()->Page->getPageData($id, false);
            Hub::get()->Ajax->success($list);
        });

        $rest->get('#api/page/by_language/(?<code>.*)#', function ($code){
            $code = $code['code'];
            $pages = \models\PageLocalizations::find([\models\PageLocalizations::LANGUAGE => $code])->asArray();
            Hub::get()->Ajax->success($pages);
        });

        $rest->get('#api/page/single/(?<pageId>.*)#', function ($id) {
            $id = $id['pageId'];
            $list = Hub::get()->Page->getPageData($id);
            Hub::get()->Ajax->success($list);
        });

        $rest->post('#api/page/localization/(?<url>.*)#', function($params){
            $data = Hub::get()->Request->requirePost(['lang']);
            if($data->isComplete()){
                $data = $data->getData();
                $result = Hub::get()->Page->addLocalization($params['url'], $data->lang);
                Hub::get()->Ajax->success($result);
            }else{
                Hub::get()->Ajax->paramsMissingError($data->getMissing());
            }
        });

        $rest->get('#api/page/localization/(?<pageId>.*)/(?<lang>.*)#', function ($params) {
            $list = \models\PageLocalizations::find([
                \models\PageLocalizations::URL => $params['pageId'],
                \models\PageLocalizations::LANGUAGE => $params['lang']
            ])->one();
            Hub::get()->Ajax->success($list);
        });


        $rest->get('#api/page/all#', function () {
            $list = Hub::get()->Page->getPageList();
            Hub::get()->Ajax->success($list);
        });

        $rest->get('#api/page/test#', function () {
            $list = \models\Pages::find('pl/inny1')->asArray();
            Hub::get()->Ajax->success($list);
        });

        /*LANGUAGE*/
        $rest->post('#api/language/active/(?<code>.*)#', function($code){
            $code = $code['code'];
            $data = Hub::get()->Request->requirePost(['state']);
            if($data->isComplete()){
                $data = $data->getData()->state;
                $result = Hub::get()->LanguageController->change($code, 'active', $data);
                Hub::get()->Ajax->success($result);
            }else{
                Hub::get()->Ajax->paramsMissingError($data->getMissing());
            }
        });
        $rest->post('#/api/language/label/(?<code>.*)#', function($code){
            $code = $code['code'];
            $data = Hub::get()->Request->requirePost(['label']);
            if($data->isComplete()){
                $data = $data->getData()->label;
                $result = Hub::get()->LanguageController->change($code, 'name', $data);
                Hub::get()->Ajax->success($result);
            }else{
                Hub::get()->Ajax->paramsMissingError($data->getMissing());
            }
        });
        $rest->post('#/api/language/state/(?<code>.*)#', function($code){
            $code = $code['code'];
            $data = Hub::get()->Request->requirePost(['state']);
            if($data->isComplete()){
                $data = $data->getData()->state;
                $result = Hub::get()->LanguageController->change($code, 'active', $data);
                Hub::get()->Ajax->success($result);
            }else{
                Hub::get()->Ajax->paramsMissingError($data->getMissing());
            }
        });
        $rest->post('#/api/language/flag/(?<code>.*)#', function($code){
            $code = $code['code'];
            $data = Hub::get()->Request->requirePost(['flag']);
            if($data->isComplete()){
                $data = $data->getData()->flag;
                $data = explode('/', $data);
                $data = $data[count($data)-1];
                $result = Hub::get()->LanguageController->change($code, 'image_path', $data);
                Hub::get()->Ajax->success($result);
            }else{
                Hub::get()->Ajax->paramsMissingError($data->getMissing());
            }
        });

        $rest->post('#api/language/add#', function(){
            $data = Hub::get()->Request->requirePost(['code', 'name', 'image_path']);
            if($data->isComplete()){
                $data = $data->getData();
                $result = Hub::get()->LanguageController->add($data->code, $data->name, $data->image_path);
                Hub::get()->Ajax->success($result);
            }else{
                Hub::get()->Ajax->paramsMissingError($data->getMissing());
            }
        });

        $rest->get('#api/language/all/free/(?<url>.*)#', function($data){
            $list = Hub::get()->LanguageController->getLanguageList($data['url']);
            Hub::get()->Ajax->success($list);
        });
        $rest->get('#api/language/all#', function () {
            $list = Hub::get()->LanguageController->getLanguageList();
            Hub::get()->Ajax->success($list);
        });
        $rest->get('#api/language/delete/(?<code>.*)#', function($code){
            $code = $code['code'];
            $result = Hub::get()->LanguageController->delete($code);
            Hub::get()->Ajax->success($result);
        });

        $rest->get('#api/language/flag/all#', function(){
            $list = Hub::get()->LanguageController->getFlagList();
            Hub::get()->Ajax->success($list);
        });
    }

    /**
     * @param $rest Rest
     */
    private function registerRuntimeRoutes($rest){
        $rest->get('#api/runtime/language/set/(?<code>.*)#', function($code){
            if(Hub::get()->LanguageController->setLanguage($code['code'])){
                $previousUrl = Hub::get()->Url->getPreviousLocation();
                Hub::get()->Url->redirectTo($previousUrl);
            }else{
                echo('lang not set');
            }
        });
    }

    /* @var Rest $rest */
    private function registerFileUploads($rest) {
        $rest->get('#api/upload/get#', function () { //return uploaded list
            if (Hub::get()->Action->validateAction(Actions::GET_UPLOADED_LIST)) {
                Hub::get()->Ajax->getUploadedFileList();
            } else {
                Hub::get()->Ajax->getErrorJSON(AjaxResponse::ACCESS_DENIED);
            }
        });

        $rest->post('#api/upload/contenttootls/insert#', function () { //handle uploaded files
            if (Hub::get()->Action->validateAction(Actions::UPLOAD_FILE)) {
                Hub::get()->Ajax->processCTInsert(); //process content tools insert
            } else {
                Hub::get()->Ajax->getErrorJSON(AjaxResponse::ACCESS_DENIED);
            }
        });

        $rest->post('#api/upload/contenttools/rotate#', function () {
            if (Hub::get()->Action->validateAction(Actions::UPLOAD_FILE)) {
                Hub::get()->Ajax->processCTRotate();
            } else {
                Hub::get()->Ajax->getErrorJSON(AjaxResponse::ACCESS_DENIED);
            }
        });

        $rest->post('#api/upload/contenttootls#', function () { //handle uploaded files
            if (Hub::get()->Action->validateAction(Actions::UPLOAD_FILE)) {
                Hub::get()->Ajax->processCTUpload();
            } else {
                Hub::get()->Ajax->getErrorJSON(AjaxResponse::ACCESS_DENIED);
            }
        });

        $rest->post('#api/upload#', function () { //handle uploaded files
            if (Hub::get()->Action->validateAction(Actions::UPLOAD_FILE)) {
                Hub::get()->Ajax->processFileUpload();
            } else {
                Hub::get()->Ajax->getErrorJSON(AjaxResponse::ACCESS_DENIED);
            }
        });
    }

    /* @var Rest $rest */
    private function registerFileMethods($rest) {

        $rest->get('#api/urls/flags#', function(){
            $url = Hub::get()->Url->getPrefix().Constants::ICONS_DIR.'/flags';
            Hub::get()->Ajax->success($url);
        });

        $rest->get('#api/files/uploaded/all#', function () {
            Hub::get()->DomBuilder->jsonMode(true);
            $list = Hub::get()->Resources->getExtendedFileList();
            Hub::get()->Ajax->success($list);
        });

        $rest->post('#api/files/uploaded/rename/(?<filename>.*)#', function ($fileName) {
            Hub::get()->DomBuilder->jsonMode(true);

            if (Hub::get()->Action->validateAction(Actions::RENAME_FILES)) {
                $fileName = $fileName['filename'];
                $result = [];
                $post = Hub::get()->Request->getPost();
                $post->ifContains('file_new_name', function ($value) use ($fileName, &$result) {
                    $result = Hub::get()->Resources->renameFile($fileName, $value);
                });
                Hub::get()->Ajax->success($result);
            } else {
                Hub::get()->Ajax->getErrorJSON(AjaxResponse::ACCESS_DENIED);
            }

        });

        $rest->get('#api/files/uploaded/delete/(?<filename>.*)#', function ($fileName) {
            $fileName = $fileName['filename'];
            Hub::get()->DomBuilder->jsonMode(true);
            if (Hub::get()->Action->validateAction(Actions::DELETE_FILES)) {
                $result = Hub::get()->Resources->deleteFile($fileName);
                Hub::get()->Ajax->success($result);
            } else {
                Hub::get()->Ajax->getErrorJSON(AjaxResponse::ACCESS_DENIED);
            }
        });

        $rest->get('#api/files/uploaded/link#', function () {
            Hub::get()->DomBuilder->jsonMode(true);
            Hub::get()->Ajax->success(Constants::UPLOADED_DIR);
        });
    }

    /* @var Rest $rest */
    private function registerMiscRoutes($rest) {
        $rest->get('#api/debug/console/state#', function () {
            /*TODO Przełączanie konsoli debugowania*/
            Hub::get()->DomBuilder->jsonMode(true);
            Hub::get()->Ajax->success($_SESSION['debug']);
        });
        $rest->post('#api/debug/console/state#', function(){
            $data = Hub::get()->Request->requirePost(['state']);
            if (Hub::get()->Action->validateAction(Actions::DEBUG_PAGE) and $data->isComplete()) {
                $_SESSION['debug'] = ($data->getData()->state=='1');
                Hub::get()->Ajax->success($_SESSION['debug']);
            } else {
                Hub::get()->Ajax->getErrorJSON(AjaxResponse::ACCESS_DENIED);
            }
        });

        $rest->post('#api/debug/darkmode/state#', function(){
            $data = Hub::get()->Request->requirePost(['state']);
            if ($data->isComplete()) {
                $_SESSION['darkmode'] = ($data->getData()->state=='true'?'1':0);
                Hub::get()->Ajax->success($_SESSION['darkmode']);
            } else {
                Hub::get()->Ajax->getErrorJSON(AjaxResponse::ACCESS_DENIED);
            }
        });

        $rest->get('#api/legend/statusnames#', function () {
            Hub::get()->DomBuilder->jsonMode(true);
            Hub::get()->Ajax->success(AjaxResponse::STATUS_NAMES);
        });

    }

    /* @var Rest $rest */
    private function registerTemplateRoutes($rest) {
        $rest->get('#api/templates/all#', function () {
            $list = Hub::get()->Template->getTemplateList();
            Hub::get()->Ajax->success($list);
        });
    }

    /* @var Rest $rest */
    private function registerDefaultRoute($rest) {
        $rest->get('#api/#', function () {
            Hub::get()->DomBuilder->jsonMode(true);
            Hub::get()->Ajax->getErrorJSON(AjaxResponse::PAGE_NOT_FOUND);
        });
        $rest->post('#api/#', function () {
            Hub::get()->DomBuilder->jsonMode(true);
            Hub::get()->Ajax->getErrorJSON(AjaxResponse::PAGE_NOT_FOUND);
        });
    }

}