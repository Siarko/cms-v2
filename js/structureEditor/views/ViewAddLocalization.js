this.render = async function(data){
    let freeLangs = await Languages.getFreeForUrl(data.url);
    let select = $(Ui.select({
        name: 'language',
        options: Ui.transformListForSelect(freeLangs, 'name', 'code')
    }));
    let saveButton = $('<button class="button fullWidth"></button>').text('Zapisz').click(function(){
        data.resolver({
            lang: select.val()
        });
    });
    let tab = $("<table></table>").addClass('fullWidth');
    tab.append(getRow("Język", (new CustomSelect()).replace(select)));
    tab.append(getRow(saveButton));
    return tab;
};