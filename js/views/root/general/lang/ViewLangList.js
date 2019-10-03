ViewLangList = {
    name: 'ViewLangList',
    parent: View
};
Class(ViewLangList, function ($this, $self) {
    let clearForm = function(){
        Ui.byId('newLangCodeInput').value = '';
        Ui.byId('newLangNameInput').value = '';
        Ui.byId('newLangNameInput').value = '';
        Ui.byId('newLangFlagInput').value = '';
        Ui.byId('newLangFlagDisplay').src = '';
    };

    $this.refresh = async function(){
        clearForm();
        await Languages.fetchLanguageList();
        $this.update();
    };

    $this.render = async function () {
        console.log("Render lang list");

        let data = Root.provide($self);
        let container = data.container;
        container.innerHTML = '';
        let langData = await Languages.getLanguageList();
        container.appendChild(Ui.HtmlTable({
            data: langData,
            columns: [
                {
                    key: 'code',
                    label: 'Kod'
                },
                {
                    key: 'name',
                    label: 'Nazwa'
                },
                {
                    label: 'Flaga',
                    type: 'dynamic',
                    onRender: function(row){
                        if(row.imagePath.length === 0){
                            return 'brak';
                        }
                        let img = Ui.element('img');
                        img.src = row.imagePath;
                        img.style.width = 30;
                        return img.outerHTML;
                    }
                },
                {
                    label: 'Aktywny',
                    type: 'dynamic',
                    onRender: function(row){
                        return Switch.create({
                            value: row.active,
                            onChange: function(state){
                                ApiQuery.simplePost('api/language/active/'+row.code, {
                                    'state': state
                                }, function(result){
                                    Messages.languageStateChanged(result.body);
                                });
                            }
                        }).get(0);
                    }
                },
                {
                    label: '',
                    type: 'dynamic',
                    onRender: function(row){
                        return $('<button></button>').html($('<i></i>').attr('class', 'fas fa-trash-alt'))
                            .click(function(){
                            ApiQuery.simpleGet('api/language/delete/'+row.code, function(result){
                                console.log(result);
                            })
                        }).get(0);
                    }
                }
            ]
        }));


    }
});

