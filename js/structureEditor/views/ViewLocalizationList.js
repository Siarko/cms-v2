this.render = async function(data){
    let url = data.url;
    let list = await Pages.getPageData(url, false);
    let cont = $('<div></div>');
    let tab = $("<table></table>").addClass('fullWidth');
    let controlRow = $('<tr></tr>');
    controlRow.append($('<td></td>').append());
    controlRow.append($('<td></td>'));

    $.each(list.localization, function(k,v){
        let row = $('<tr></tr>');
        let button = $('<div></div>').addClass('buttonSide').html($('<i></i>').addClass('fa fa-caret-right'));
        button.click(function(){
            breadcrumbs.add(v.language, {
                id:'LocalizationSettings',
                data:{url:v.url, lang: v.language}
            });
        });
        row.append($('<td></td>').html($('<span class="pageUrl"></span>').text(v.language)));
        row.append($('<td></td>').html(button));
        tab.append(row);
    });
    cont.append(await getAddButtonTable({//add/delete buttons
        addUrl: 'api/page/localization/'+url,
        onAdd: function(result){
            breadcrumbs.back(false);
            breadcrumbs.add(result.body,{
                id: 'LocalizationSettings',
                data:{
                    url: url,
                    lang: result.body
                }
            })
        },
        addResolver: function(resolve){
            breadcrumbs.add('Nowa lokalizacja',{
                id: 'AddLocalization',
                data: {
                    url: url,
                    resolver: resolve
                }
            });
        }
    }));
    cont.append($('<table></table>').append($('<tr></tr>').append($('<td></td>').append(
        $('<button></button>').addClass('button fullWidth settings')
            .html($('<i class="fas fa-cog"></i>'))
            .click(function(){
                breadcrumbs.add('Ustawienia', {
                    id: 'PageSettings',
                    data: list
                });
            })
    ))));
    cont.append(tab);
    return cont;
};