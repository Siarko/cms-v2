this.render = async function(data){
    let langData = await Languages.getLanguageByCode(data.lang);
    let table = $('<table></table>');
    let input = await Ui.requestInput({
        name: 'label',
        value: langData.name,
        url: 'api/language/label/'+data.lang,
        cssClass: 'requestInput right halfWidth',
        callback: function(result){
            Messages.standardInfo(result.body, 'Etykieta została zmieniona', 'Błąd podczas zmiany etykiety')
        }
    });
    let sw = Switch.create({
        value: langData.active,
        onChange: async function(state){
            let r = await Languages.changeState(langData.code, state);
            Messages.standardInfo(r.body, 'Widoczność zmieniona', 'Wystąpił błąd')
        }
    });

    table.append(getRow('Etykieta', input));
    table.append(getRow('Widoczność', sw).css('text-align','right'));

    let img = $('<img/>').attr('src',langData.imagePath).css('width', '100%');
    let button = $('<div></div>').addClass('buttonSide').text('Zmień');
    button.click(function(){
        Languages.selectFlag(async function(result){
            let r = await Languages.changeFlag(langData.code, result.url);
            Messages.standardInfo(r.body, 'Flaga zmieniona', 'Nie można zmienić flagi');
            if(r.body){
                img.attr('src', result.url);
            }
        })
    });
    let imgContainer = $('<span></span>').append(img)
        .append(button);
    table.append(
        $('<tr></tr>').append($('<td></td>').text('Flaga'))
            .append($('<td></td>').append(imgContainer))
    );
    delButton = $('<button class="button fullWidth red"></button>').text("USUWANIE");
    let deleteMethod = function(){
        ApiQuery.simpleGet('api/language/delete/'+langData.code, async function(result){
            Messages.standardInfo(result.body, 'Język został usunięty', 'Bład podczas usuwania języka');
            breadcrumbs.back();
        })
    };
    delButton.click(async function(){
        let langUsers = (await Languages.getPagesByLanguage(langData.code)).body;
        if(langUsers.length > 0){
            let showList = $('<button></button>').text('('+langUsers.length+") pokaż listę").click(function(){
                let l = $('<ol></ol>');
                $.each(langUsers, function(k,v){
                    l.append($('<li></li>').text(v.url+" - "+v.menuname));
                });
                new jBox('Modal', {
                    title: 'Lista stron',
                    content: l,
                    closeButton: 'title',
                    draggable: 'title',
                    fixed: true
                }).open();
            });
            let cont = $('<div></div>').append(
                $('<p></p>').append(
                    $('<p></p>').text('Język jest używany przez strony: ')
                ).append(showList)
            ).append(
                $('<p></p>').text('W przypadku usunięcia, strony które używają tego języka także zostaną usunięte')
            );
            new jBox('Confirm', {
                title: 'Usuwanie języka',
                content: cont,
                confirmButton: 'Usuń',
                cancelButton: 'Anuluj',
                closeButton: true,
                draggable: 'title',
                confirm: async function(){
                    deleteMethod();
                },
                repositionOnOpen: true,
                fixed: true
            }).open();
        }else{
            deleteMethod();
        }
    });
    table.append($('<tr></tr>').append($('<td colspan="2"></td>').append(delButton)));
    return table;
};