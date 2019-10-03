let ViewLocalizationSettings = {
    name: 'ViewLocalizationSettings',
    parent: View
};
Class(ViewLocalizationSettings, function ($this, $self) {

    $this.refresh = function(){
        $this.update();
    };

    $this.close = function () {
        let data = Root.provide($self);
        data.container.innerHTML = '';
    };

    $this.render = async function(){
        let container = Root.provide($self).container;
        let localization = await Pages.getLocalizationData(
            Root.provide($self).pageId,
            Root.provide($self).lang
            );

        let context = {
            url: localization.url,
            language: localization.language,
            languageInput: await Ui.requestSelect({
                url: 'api/page/language/'+localization.url+"/"+localization.language,
                name: 'language',
                values: await function(){
                    return Languages.getLanguageList().then(function(values){
                        return Ui.transformListForSelect(values, 'name', 'code');
                    });
                },
                default: localization.language,
                callback: function(result){
                    View.viewPageSettings.refresh();
                    $this.close();
                }
            }),
            menuNameInput: await Ui.requestInput({
                url: 'api/page/menuname/'+localization.url+"/"+localization.language,
                name: 'page_menu_name',
                value: localization.menuname,
                callback: function(result){
                    $this.refresh();
                    View.viewPageSettings.refresh();
                }
            }),
            menuVisibility: Switch.create({
                value: (localization.menu_visibility === "1"),
                onChange: function (state) {
                    ApiQuery.simplePost('api/page/menuvisibility/'+localization.url+"/"+localization.language, {
                        state: state
                    }, function(result){
                        View.viewPageSettings.refresh();
                        console.log(result);
                    })
                }
            }),
            connectedFile: await Ui.requestInput({
                url: '',
                name: '',
                value: localization.linked_file,
                callback: function(result){

                }
            }),
            headerText: await Ui.requestInput({
                url: '',
                name: '',
                value: localization.header_text,
                callback: function(result){

                }
            })

        };

        container.innerHTML = '';
        template('rootConsole/pages/localizationSettings', context, async function(html){
            container.appendChild(html);
        });

    }
});