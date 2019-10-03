
let _Pages = function () {
    let $this = this;

    let fullData = null;

    this.managePageId = null;
    this.managePageLang = null;

    this.refreshPagesData = function(){
        return new Promise(function(success, fail){
            ApiQuery.simpleGet('api/page/all', function(data){
                console.log('Refresh pages list -> request');
                fullData = data.body;
                success();
            });
        });
    };

    this.getTemplates = async function(){
        return new Promise(function(success){
            ApiQuery.simpleGet('api/templates/all', function (result) {
                success(result.body);
            });
        });
    };

    this.getPageData = async function(id, localized = true){
        let fullData = {};
        let url = 'api/page/single/'+((!localized)?'unlocalized/':'');
        let wrapper = async function(){
            return new Promise(function(success, fail){
                ApiQuery.simpleGet(url+id, function(data){
                    fullData = data.body;
                    success();
                });
            });
        };
        await wrapper();
        return fullData;

    };

    this.getLocalizationData = async function(url, language){
        return new Promise(function(resolve){
            ApiQuery.simpleGet('api/page/localization/'+url+"/"+language, function(result){
                resolve(result.body);
            });
        });
    };

    this.getLinkedFilesList = async function(){
        return ApiQuery.simpleGetPromise('api/page/linked_files');
    };

    this.getPagesData = async function(renew = false){
        if(!fullData || renew){
            await $this.refreshPagesData();
        }
        return fullData;
    };

    this.manage = function(pageUrl){
        $this.managePageId = pageUrl;
        View.viewPageSettings.render();
    };

    this.manageLocalization = function(url, lang){
        $this.managePageId = url;
        $this.managePageLang = lang;
        View.viewLocalizationSettings.render();
    }
};

window.Pages = new _Pages();