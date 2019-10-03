
let _Languages = function () {
    let $this = this;
    let languageList = null;
    let flagList = null;
    let flagUrl = null;

    $this.fetchLanguageList = async function(){
        return new Promise(function(success){
            ApiQuery.simpleGet('api/language/all', function(data){
                languageList = data.body;
                success(languageList);
            })
        });
    };
    $this.fetchFlagList = async function(){
        return new Promise(function(success){
            ApiQuery.simpleGet('api/language/flag/all', function(data){
                flagList = data.body;
                ApiQuery.simpleGet('api/urls/flags', function(result){
                    flagUrl = result.body;
                    success();
                });

            })
        });
    };
    $this.getFreeForUrl = async function(url){
        return new Promise(function(success){
            ApiQuery.simpleGet('api/language/all/free/'+url, function(result){
                success(result.body);
            })
        })
    };

    $this.getLanguageList = async function(){
        if(languageList === null){
            await $this.fetchLanguageList();
        }
        return languageList;
    };

    $this.getLanguageByCode = function(code){
        if(languageList === null){
            console.error("NIE POBRANO LISTY JĘZYKÓW");
        }
        let result = {};
        $.each(languageList, function(k, v){
            if(v.code === code){
                result = v;
            }
        });
        return result;
    };

    $this.getPagesByLanguage = async function(code){
        return new Promise(function(success){
            ApiQuery.simpleGet('api/page/by_language/'+code,function(result){
                success(result);
            })
        });
    };

    /*
    Modal do wybory flagi
    przyjmuje callback i zwraca w argumencie
    {
        filename - pełna nazwa pliku grafiki,
        code - kod (pl,gb,au),
        url - pełny url do pliku
    }
    */
    $this.selectFlag = async function(callback){

        if(flagList === null){
            await $this.fetchFlagList();
        }

        let createFlagGallery = function(flagData, modal, callback){
            let getFlagUrl = function(name){
                return flagUrl+'/'+name;
            };
            let getFlagBox = function(flagData){
                let box = $('<div class="flagBox"></div>').css({width: 100});
                box.append($('<img src="'+getFlagUrl(flagData.filename)+'"/>'));
                box.append($('<span></span>').text(flagData.code));
                return box;
            };

            let viewPort = {w: $(window).width(), h: $(window).height()};
            let container = $('<div class="flagGallery"></div>');
            container.css({width: viewPort.w*0.9, height: viewPort.h-100});

            $.each(flagData, function(k, v){
                let box = getFlagBox(v);
                box.click(function(){
                    modal.close();
                    callback({
                        filename: v.filename,
                        code: v.code,
                        url: flagUrl+"/"+v.filename
                    });
                });
                container.append(box);
            });

            return container;
        };

        let modal = new jBox('modal', {
            title: "Wybierz flagę",
            closeButton: true,
            draggable: 'title',
            repositionOnOpen: true,
            fixed: true
        });

        let gallery = createFlagGallery(flagList, modal, callback);
        modal.setContent(gallery);

        modal.open();
    };

    $this.add = function(code, name, imagePath){
        ApiQuery.simplePost('api/language/add', {
            code: code,
            name: name,
            image_path: imagePath
        }, function(result){
            View.viewLangList.refresh();
            Messages.languageAdded(result.body);
        });
    };

    $this.changeFlag = async function(code, url){
        return new Promise(function(success){
            ApiQuery.simplePost('api/language/flag/'+code, {flag: url},
                function(result){
                    success(result);
                }
            )
        });
    };
    $this.changeState = async function(code, state){
        return new Promise(function(success){
            ApiQuery.simplePost('api/language/state/'+code, {state: state},
                function(result){
                    success(result);
                }
            )
        });
    }
};

window.Languages = new _Languages();