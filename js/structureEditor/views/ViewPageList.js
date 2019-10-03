this.render = async function(data){
    let list = await Pages.getPagesData();
    let cont = $('<div></div>');
    let tab = $("<table></table>").addClass('fullWidth');
    cont.append(await getAddButtonTable({//add/delete buttons
        addUrl: 'api/page/add/simple',
        onAdd: async function(result){
            breadcrumbs.back();
            await Pages.getPagesData(true);
            breadcrumbs.add(result.body, {
                id: 'LocalizationList',
                data:{
                    url: result.body
                }
            });
        },
        addResolver: function(resolve){
            breadcrumbs.add('Nowa strona', {
                id: 'AddPage',
                data: {resolver: resolve}
            });
        }
    }));
    cont.append(tab);
    $.each(list, function(k,v){
        if(!v.editable && !v.deleteable){return;}
        let row = $('<tr></tr>');
        let button = $('<div></div>').addClass('buttonSide').html($('<i></i>').addClass('fa fa-caret-right'));
        button.click(function(){
            breadcrumbs.add(v.id, {
                id:'LocalizationList',
                data:{
                    url:v.id
                }
            });
        });
        row.append($('<td></td>').html($('<span class="pageUrl"></span>').text(v.id)));
        row.append($('<td></td>').html(button));
        let delButton = null;
        if(v.deleteable){
            delButton = $('<div class="buttonSide delete"></div>').html('<i class="fas fa-trash-alt"></i>');
            delButton.click(function(){
                new jBox('Confirm', {
                    title: 'Usuwanie strony',
                    content: 'Na pewno usunąć stronę "'+v.id+'" ?',
                    confirmButton: 'Usuń',
                    cancelButton: 'Anuluj',
                    closeButton: true,
                    draggable: 'title',
                    confirm: function(){
                        ApiQuery.simpleGet('api/page/delete/'+v.id, async function(result){
                            await Pages.getPagesData(true);
                            $this.view('PageList');
                            Messages.standardInfo(
                                (result.body===true),
                                'Strona została usunięta',
                                'Nie udało się usunąć strony'
                            );

                        })
                    },
                    repositionOnOpen: true,
                    fixed: true
                }).open();
            });
        }else{
            delButton = $('<span></span>')
        }
        row.append($('<td></td>').html(delButton));
        tab.append(row);
    });
    return cont;
};