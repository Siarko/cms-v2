this.render = async function(data){
    let loc = await Pages.getLocalizationData(data.url, data.lang);
    let lang = await Languages.getLanguageList();
    let linkedFiles = (await Pages.getLinkedFilesList()).body;
    linkedFiles = Ui.transformListForSelect(linkedFiles, null);
    linkedFiles.unshift({label: 'Brak', value: ''});
    let languageSelect = await Ui.requestSelect({
        url: 'api/page/language/'+data.url+"/"+data.lang,
        name: 'language',
        values: Ui.transformListForSelect(lang, 'name', 'code'),
        default: loc.language,
        callback: function(result, chosen){
            if(result.body){
                breadcrumbs.back(false);
                breadcrumbs.add(chosen, {
                    id:'LocalizationSettings',
                    data:{url:loc.url, lang: chosen}
                });
            }else{
                Messages.standardInfo(false,'','Bład zmiany języka');
                $this.view('LocalizationSettings', {
                    url: loc.url,
                    lang: loc.language
                });
            }
        }
    });
    let linkedFileSelect = await Ui.requestSelect({
        url: 'api/page/linked_file/'+loc.url+"/"+loc.language,
        name: 'file',
        values: linkedFiles,
        default: loc.linked_file,
        callback: function(result, chosen){
            console.log(result, chosen);
        }
    });

    let container = $('<table></table>').addClass('inputTable');
    let menuVisSwitch = Switch.create({
        value: (loc.menu_visibility==="1"),
        onChange: function(state){
            ApiQuery.simplePost('api/page/menuvisibility/'+loc.url+"/"+loc.language, {
                state: state
            }, function(result){
                Messages.standardInfo(result.body, "Widoczność zmieniona", "Nie można zmienić");
            })
        }
    });
    let menuNameInput = await Ui.requestInput({
        url: 'api/page/menuname/'+loc.url+"/"+loc.language,
        name: 'page_menu_name',
        value: loc.menuname,
        callback: function(result){
            Messages.standardInfo(result.body, "Nazwa w menu zmieniona", "Nie można zmienić");
        }
    });

    let pageLink = $('<span></span>').addClass('STR_go_to_page').click(function(){
        location.href = CMS_HOME + loc.url+"?fake_lang="+loc.language;
    });
    pageLink.append($('<i></i>').addClass('fas fa-arrow-alt-circle-right'));

    let deleteButton = await Ui.requestButton({
        text: 'USUWANIE',
        cssClass: [
            'button',
            'fullWidth',
            'red'
        ],
        click: function(resolve){
            new jBox('Confirm', {
                title: 'Usuwanie lokalizacji',
                content: 'Na pewno usunąć lokalizację "'+loc.language+'"?',
                confirmButton: 'Usuń',
                cancelButton: 'Anuluj',
                closeButton: true,
                draggable: 'title',
                confirm: function(){
                    resolve();
                },
                repositionOnOpen: true,
                fixed: true
            }).open();
        },
        url: 'api/page/delete/localization/'+loc.url+"/"+loc.language,
        callback: function(result){
            if(result.body){
                breadcrumbs.back();
            }
            Messages.standardInfo(result.body,
                'Lokalizacja została usunięta',
                'Nie można usunąć lokalizacji'
            );
        }
    });

    container.append(getRow('Przejdż do strony', pageLink));
    container.append(getRow('=== Lokalizacja'));
    container.append(getRow('Język', (new CustomSelect()).replace(languageSelect)));
    container.append(getRow('=== Menu'));
    container.append(getRow('Widoczność', menuVisSwitch));
    container.append(getRow('Nazwa', menuNameInput));
    container.append(getRow('=== Treść'));
    container.append(getRow('Podłączony plik', (new CustomSelect()).replace(linkedFileSelect)));
    container.append(getRow(deleteButton));
    return container;
};