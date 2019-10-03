this.render = async function(data){
    let levelSelect = await Ui.requestSelect({
        url: 'api/page/permissions/'+data.id,
        name: 'level',
        default: data.permlevel,
        values: async function(){
            let options = {};
            await new Promise(function(success){
                ApiQuery.simpleGet('api/auth/get-avaiable-perm-levels', function (result) {
                    options = Ui.transformListForSelect(result.body, 'name', 'id');
                    success();
                });
            });
            return options;
        },
        callback: function(result){
            Messages.pagePermissionsChanged(result.body);
        }
    });
    levelSelect = (new CustomSelect()).replace($(levelSelect));
    console.log(data);

    let table = $('<table></table>');

    table.append(getRow('Dostęp'));
    let row = $('<tr></tr>')
        .append($('<td></td>').text('Wymagany poziom'))
        .append($('<td></td>').append(levelSelect).css({'min-width': 150}));
    table.append(row);
    return table;
};