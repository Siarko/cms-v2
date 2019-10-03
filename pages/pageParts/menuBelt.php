<div id="menu">
    <div class="linkContainer menuHomeButton">
        <a href="<?=$this->rootLink.$this->homeLink?>"><?=$this->homeButton?></a>
    </div>
    <?php foreach ($this->menuStructure as $menuObj):?>
        <?php if(!$menuObj['localization']['menu_visibility']){continue;}?>
        <div class="linkContainer">
            <a class="link" href="<?=$this->rootLink.$menuObj['id']?>">
                <?=(($menuObj['localization']['menuname'])?$menuObj['localization']['menuname']:$menuObj['id'])?>
            </a>

            <?php if(Hub::get()->Action->validate(Actions::MODIFY_MENU_LINK)):?>
            <a onclick="Menu.modify('<?=$menuObj['id']?>')" class="linkModify">Ustawienia</a>
            <?php endif;?>
        </div>
    <?php endforeach;?>
    <!--Przycisk nowej strony-->
    <?php if(Hub::get()->Action->validate(Actions::MODIFY_MENU_LINK)):?>
        <div class="linkContainer">
            <a href="javascript:void(0)" title="Dodaj stronę" class="link" onclick="Menu.add()">+</a>
        </div>
    <?php endif;?>
    <!--Języki-->


    <div class="linkContainer right">
        <span class="link" onclick="Menu.toggleDarkMode()">
            <i class="fas fa-adjust"></i>
        </span>
    </div>
    <div class="linkContainer languagePicker">
        <a class="link"><?=$this->selectedLanguage->name?></a>
        <div class="submenu">
            <?php foreach ($this->languages as $language): if(!$language['active']){continue;}?>
                <a class="link" href="<?=$this->apiLink?>/runtime/language/set/<?=$language['code']?>">
                    <img width="30" src="<?=$language['imagePath']?>">
                    <span><?=$language['name']?></span>
                </a>
            <?php endforeach;?>
        </div>
    </div>
</div>
<div id="menuPlaceholder"></div>
