let _Root = function(){

    let $scope = this;

    let tabs = {
        users: null,
        logs: null,

        files: null
    };

    let state = {
        selectedUser: null,
    };

    this.provide = function(object){
        let result = {};
        let p = function(controllerName, action){
            if(object.className === controllerName){
                if(typeof action === 'function'){
                    result = action();
                }else{
                    result = action;
                }
            }
        };

        p('ViewUserList', function(){
            return {container: document.getElementById("userList")}
        });
        p('ViewUserSettings', function(){
            return{
                container: document.getElementById("userDetails"),
                userId: state.selectedUser
            }
        });
        p('ViewNewUser', function(){
            return{container: document.getElementById("newUser")}
        });
        p('ViewPagesList',function() {
            return {
                container: tabs.pages.getElementsByClassName('pageList')[0],
            }
        });
        p('ViewPageSettings', function(){
            return {
                container: tabs.pages.getElementsByClassName('pageSettings')[0],
                pageId: Pages.managePageId
            }
        });
        p('ViewLocalizationSettings', function(){
            return {
                container: tabs.pages.getElementsByClassName('localizationSettings')[0],
                pageId: Pages.managePageId,
                lang: Pages.managePageLang
            }
        });
        p('ViewFileList', function(){
            return {
                container: document.getElementById('fileList'),
            }
        });

        p('ViewLangList', function(){
            return {
                container: document.getElementById('langList'),
            }
        });
        p('ViewSessionSettings', function(){
            return {
            }
        });

        return result;
    };

    this.refresh = function(view, httpResponse){
        switch (view){
            case 'modify_users':{
                let body = httpResponse.body;
                let flag = true;
                if(!ApiQuery.isStatus(body.user_change_id, 'SUCCESS')){
                    Messages.QueryError('Zmiana id użytkownika');
                    flag = false;
                }else{
                    state.selectedUser = body.user_new_id;
                    View.viewUserList.update();
                }
                if(!ApiQuery.isStatus(body.user_change_perm_level, 'SUCCESS')){
                    Messages.QueryError('Zmiana poziomu uprawnień');
                    flag = false;
                }
                View.viewUserSettings.update();

                if(flag){
                    Messages.QuerryOk();
                }
                break;
            }
        }
    };

    this.manage = function(userId){
        state.selectedUser = userId;
        View.viewUserSettings.render();
    };

    this.tabUsers = function(tab){
        if(tab){
            tabs.users = tab;
        }
        if(!tabs.users){
            Messages.InternalError();
        }
        View.viewUserList.render();
        View.viewNewUser.render();
    };

    this.tabPages = function(tab){
        if(tab){
            tabs.pages = tab;
        }
        if(!tabs.pages){
            Messages.InternalError();
        }
        View.viewPagesList.render();
    };

    this.tabGeneral = function(tab){
        if(tab){
            tabs.general = tab;
        }
        if(!tabs.general){
            Messages.InternalError();
        }
        View.viewLangList.render();
        View.viewSessionSettings.render();
    };

    this.tabLogs = function(tab){
        if(tab){
            tabs.logs = tab;
        }
        if(!tabs.logs){
            Messages.InternalError();
        }
    };

    this.tabFiles = function(tab){
        if(tab){
            tabs.files = tab;
        }
        if(!tabs.files){
            Messages.InternalError();
        }
        View.viewFileList.render();
    };

    return this;
};
window.Root = new _Root();