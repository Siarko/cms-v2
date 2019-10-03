this.render = async function(data){
    let list = await Languages.fetchLanguageList();
    let cont = $('<div></div>');
    let table = $("<table></table>");
    $.each(list, function(k,v){
        let active = $('<td></td>');
        if(v.active){
            active.append($('<i class="fas fa-eye"></i>'));
        }
        table.append($('<tr></tr>')
            .append($('<td></td>').text(v.name))
            .append(active)
            .append($('<td></td>').append(getButton('LanguageSettings', {lang:v.code}, v.name))));
    });
    await cont.append(await getAddButtonTable({
        addUrl: 'api/language/add',
        addResolver: function(resolve){
            breadcrumbs.add('Nowy język', {
                id: 'AddLanguage',
                data:{resolver: resolve}
            });
        },
        onAdd: function(result){
            Messages.standardInfo(result.body, 'Dodano nowy język', 'Wystąpił błąd podczas dodawania języka');
            if(result.body){
                breadcrumbs.back();
            }
        }
    }));
    cont.append(table);
    return cont;
};