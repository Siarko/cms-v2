/**
 * Created by SiarkoWodór on 08.08.2017.
 */
let Menu = {

    toggleDarkMode: function(){
        DARKMODE = !DARKMODE;
        let styles = $('link[rel="stylesheet"]');
        $.each(styles, function(k,v){
            let e = $(v);
            let ch = e.attr('href');
            if(ch.indexOf('.colors.css') !== -1){
                e.attr('href', ch.replace('.colors.css', '.colors.dark.css'));
            }else if(ch.indexOf('.colors.dark.css') !== -1){
                e.attr('href', ch.replace('.colors.dark.css', '.colors.css'));
            }
        });
        ApiQuery.simplePost('api/debug/darkmode/state', {
            state: DARKMODE
        });
    },

    getModal: function (title, text, onClose) {
        return new jBox('modal', {
            title: title,
            content: text,
            closeButton: true,
            draggable: 'title',
            onClose: onClose,
            repositionOnOpen: true,
            fixed: true
        });
    },

    getLine: function (desc, controll) {
        let text = document.createElement("div");
        text.innerHTML = desc;
        text.classList.add("optionText");
        let controllBox = $(document.createElement("div"));
        controllBox.addClass("optionControll");
        controllBox.append(controll);
        let box = $(document.createElement("div"));
        box.addClass("optionContainer");
        box.append(text);
        box.append(controllBox);
        return box;
    },

    getModifyContent: async function (pageId) {
        let content = $('<div></div>');
        content.append(Menu.getLine("Widoczność w menu", Switch.create({
            value: true,
            onChange: function(state){
                ApiQuery.simplePost('api/page/menuvisibility/'+pageId+'/'+LANGUAGE, {
                    state: state
                }, function(res){
                    console.log(res);
                    if(res.body){
                        Messages.pageVisibilityChanged(true);
                    }else{
                        Messages.pageVisibilityChanged(false);
                    }
                });
            }
        })));
        content.append(Menu.getLine(
            "Etykieta",
            await Ui.requestInput({
                url: 'api/page/menuname/' + pageId + '/'+ LANGUAGE,
                name: 'page_menu_name',
                value: function () {
                    return new Promise(function (success, fail) {
                        ApiQuery.simpleGet('api/page/single/'+pageId, function (result) {
                            if(result.body !== null){
                                success(result.body.localization.menuname);
                            }else{
                                Messages.InternalError();
                            }
                        })
                    })
                },
                callback: function (value) {
                    if(value.body){
                        Messages.pageNameChanged(true);
                    }else{
                        Messages.pageNameChanged(false);
                    }
                }
            })
        ));
        return content;
    },

    modify: async function (pageId) {
        let content = await Menu.getModifyContent(pageId);
        let modal = Menu.getModal("Ustawienia " + pageId, content, function () {
            console.log("Zamykanie");
        });
        modal.open();
    },

    add: async function () {
        let list = await PageService.getTemplateList().then(function (data) {
            return Ui.transformListForSelect(data.body, 'htmlfile');
        });

        let langList = await Languages.getLanguageList().then(function(data){
            data.push({code: '', name: 'Brak'});
            return Ui.transformListForSelect(data, 'name', 'code');
        });

        console.log(list, langList);

        let form = Ui.formBegin('api/page/add', 'POST');
        form.appendChild(Ui.labeledInput({
            label: 'URL',
            name: 'page_url',
            required: true
        }));
        form.appendChild(Ui.labeledInput({
            label: 'Nazwa w menu',
            name: 'page_name',
            required: true
        }));
        form.appendChild(Ui.labeledInput({
            type: 'select',
            label: 'Język',
            name: 'language',
            required: false,
            select: langList,
            value: ''
        }));
        form.appendChild(Ui.labeledInput({
            type: 'select',
            label: 'Szablon',
            name: 'page_template',
            required: true,
            select: list,
        }));
        form.appendChild(Ui.submitAjax({
            text: 'Dodaj',
            cssClass: ['fullWidth'],
            callback: function (response) {
                console.log(response);
                if (response.body !== null) {
                    Messages.pageCreated(response.body);
                } else {
                    Messages.pageNotCreated();
                }
            }
        }));
        let modal = Menu.getModal("Dodawanie strony", form.outerHTML);
        modal.open();
    }
};
require([
    'services/PageService',
    'modules/Languages'
]);


/*getFunctionArgs(Menu.construct).forEach(function (element) {
    let spos = element.indexOf('Service');
    if (spos !== -1) { //service
        let objName = element.substr(0, spos);
        let path = 'services/' + objName;
        console.log("Required: " + path);
        require([{
            url: path,
            onload: function () {
                window[objName + 'Service'] = new window[objName]();
            }
        }]);
    }
});*/
