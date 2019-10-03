<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 31.08.2017
 * Time: 15:11
 */
class VariableLoader {

    /* @var Hub Hub::get()*/
   
    public function init(){

        Hub::get()->Variables->user = Hub::get()->Auth->getLogged();
        Hub::get()->Variables->logoLink = Hub::get()->Resources->getIcon('logo.png');
        Hub::get()->Variables->thisPageLink = trim(Hub::get()->Url->get(), '/');
        Hub::get()->Variables->apiLink = trim(Hub::get()->Url->getPrefix(),'/').'/api';

        Hub::get()->Variables->rootLink = Hub::get()->Url->getPrefix();
        Hub::get()->Variables->linkRootconsole = Hub::get()->Url->getPrefix().Hub::get()->SettingsDb->getSetting('rootConsoleLink');

        Hub::get()->Variables->homeButton = Hub::get()->SettingsDb->getSetting('pageHomeButton');
        Hub::get()->Variables->homeLink = Hub::get()->SettingsDb->getSetting('homePageUrl');

        Hub::get()->Variables->languages = Hub::get()->LanguageController->getLanguageList();
        Hub::get()->Variables->selectedLanguage = Hub::get()->LanguageController->getByCode(
            Hub::get()->LanguageController->getPreferredLanguage()
        );

        $menu = Hub::get()->Menu->process(Hub::get()->PagePart->get('menuBelt'));
        Hub::get()->Variables->menuBelt = $menu;
    }
}
