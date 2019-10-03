<div class="sideMenuHolder">
    <div id="sidemenu">
        <ol class="menu">
            <li class="cmsLogo">
                <a href="<?= $this->linkRootconsole ?>">
                    <span class="icon cmsLogo"><img src="<?= $this->logoLink ?>"></span>
                    <span class="label">SiarkoCMS</span>
                </a>
            </li>
            <li class="menuPosition niceTooltip" id="button_tab_users" onclick="tabs.activate('tab_users', this)"
                title="Zarządzanie użytkownikami">
                <span class="icon"><i class="fa fa-user"></i></span>
                <span class="label">Użytkownicy</span>
            </li>
            <li class="menuPosition niceTooltip" onclick="tabs.activate('tab_logs', this)" title="Logi systemu">
                <span class="icon"><i class="fa fa-terminal"></i></span>
                <span class="label">Logi</span>
            </li>
            <li class="menuPosition niceTooltip" onclick="tabs.activate('tab_pages', this)"
                title="Zarządzanie stronami">
                <span class="icon"><i class="fas fa-file-alt"></i></span>
                <span class="label">Strony</span>
            </li>
            <li class="menuPosition niceTooltip" onclick="tabs.activate('tab_glob_settings', this)"
                title="Ustawienia globalne">
                <span class="icon"><i class="fa fa-cog"></i></span>
                <span class="label">Ustawienia glob.</span>
            </li>
            <li class="menuPosition niceTooltip" onclick="tabs.activate('tab_media', this)" title="Zarządzanie plikami">
                <span class="icon"><i class="fas fa-paperclip"></i></span>
                <span class="label">Media</span>
            </li>
            <li class="menuPosition niceTooltip" onclick="tabs.activate('tab_templates', this)"
                title="Zarządzanie szablonami">
                <span class="icon"><i class="fa fa-list-alt"></i></span>
                <span class="label">Szablony</span>
            </li>
            <li class="menuPosition" onclick='toggleClass("sidemenu","out"); toggleClass(body, "menuOut");'>
                <span class="icon"><i class="fa fa-caret-right" aria-hidden="true"></i></span>
                <span class="label"><i style="margin-right: 10px" class="fa fa-caret-left" aria-hidden="true"></i>Zwiń menu</span>
            </li>
        </ol>
    </div>
</div>
<div class="header">Konsola roota@<?= Hub::get()->Auth->getLogged()->getName() ?> ><span id="blinker">_</span></div>
<div class="content">
    <div id="tabContainer">
        <div class="tab noBg" data-url="users" id="tab_users" onload="Root.tabUsers(this)">
            <div class="row">
                <div class="box">
                    <div class="boxTitle">
                        Zarządzanie użytkownikami
                        <span class="button">
                        <i class="fas fa-sync-alt" onclick="View.viewUserList.refresh()" title="Odśwież listę języków"></i>
                    </span>
                    </div>
                    <div class="boxContent" id="userList">
                    </div>
                </div>
                <div class="box">
                    <div class="boxTitle">
                        Szczegóły
                    </div>
                    <div class="boxContent" id="userDetails">
                        Wybierz użytkownika
                    </div>
                </div>
                <div class="box">
                    <div class="boxTitle">
                        Nowy użytkownik
                    </div>
                    <div class="boxContent" id="newUser">
                        <div class="inputRow">
                            <label>Login</label>
                            <input type="text" id="newUserLogin" placeholder="Login"/>
                        </div>
                        <div class="inputRow">
                            <label>Hasło</label>
                            <input type="text" id="newUserPassword" placeholder="Hasło"/>
                        </div>
                        <div class="inputRow">
                            <label>Uprawnienia</label>
                            <select type="text" id="newUserPerm"></select>
                        </div>
                        <div class="fullWidth">
                            <button class="fullWidth" onclick="Users.new(
                                Ui.byId('newUserLogin').value,
                                Ui.byId('newUserPassword').value,
                                Ui.byId('newUserPerm').value)">
                                Zapisz
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab" data-url="logs" id="tab_logs" onload="Root.tabLogs(this)">
            Logi systemu, praca w toku.
        </div>
        <div class="tab" data-url="pages" id="tab_pages" onload="Root.tabPages(this)">
            <div class="title">Zarządzanie stronami
                <span class="button">
                    <i class="fas fa-sync-alt" onclick="View.viewPagesList.refresh()" title="Odśwież listę stron"></i>
                </span>
            </div>
            <div class="content">
                <div class="pageList"></div>
                <div class="pageSettings"></div>
                <div class="localizationSettings"></div>
            </div>
        </div>
        <div class="tab noBg" data-url="general" id="tab_glob_settings" onload="Root.tabGeneral(this)">
            <div class="row">
                <div class="box">
                    <div class="boxTitle">
                        Obsługiwane języki
                        <span class="button">
                        <i class="fas fa-sync-alt" onclick="View.viewLangList.refresh()" title="Odśwież listę języków"></i>
                    </span>
                    </div>
                    <div class="boxContent" id="langList">
                    </div>
                </div>
                <div class="box">
                    <div class="boxTitle">Dodawanie nowego języka</div>
                    <div class="boxContent">
                        <div class="inputRow">
                            <label>Kod języka</label>
                            <input disabled type="text" id="newLangCodeInput" placeholder="Wybierz flagę"/>
                        </div>
                        <div class="inputRow">
                            <label>Nazwa języka</label>
                            <input type="text" id="newLangNameInput" placeholder="Angielski, Niemiecki..."/>
                        </div>
                        <div class="inputRow">
                            <label>Flaga</label>
                            <img id="newLangFlagDisplay" style="height: 100%"/>
                            <input type="hidden" id="newLangFlagInput"/>
                            <button onclick="Languages.selectFlag(function(flagData){
                                Ui.byId('newLangCodeInput').value = flagData.code;
                                Ui.byId('newLangFlagInput').value = flagData.filename;
                                Ui.byId('newLangFlagDisplay').src = flagData.url;
                            })">Wybierz</button>
                        </div>
                        <div class="fullWidth">
                            <button class="fullWidth" onclick="Languages.add(
                                Ui.byId('newLangCodeInput').value,
                                Ui.byId('newLangNameInput').value,
                                Ui.byId('newLangFlagInput').value)">
                                Zapisz
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="box">
                    <div class="boxTitle">Ustawienia sesji</div>
                    <div class="boxContent">
                        <div class="inputRow" id="consoleSwitch"></div>
                    </div>
                </div>
            </div>

        </div>
        <div class="tab" data-url="media" id="tab_media" onload="Root.tabFiles(this)">
            <div class="title">Zarządzanie plikami
                <span class="button">
                    <i class="fas fa-sync-alt" onclick="View.viewFileList.refresh()" title="Odśwież listę plików"></i>
                </span>
            </div>
            <div id="fileList"></div>
        </div>
        <div class="tab" data-url="templates" id="tab_templates">
            Szablony...
        </div>
    </div>
</div>