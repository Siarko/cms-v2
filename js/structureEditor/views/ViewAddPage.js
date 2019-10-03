this.render = async function(data){
    let templates = await Pages.getTemplates();
    let pages = await Pages.getPagesData();
    let options = Ui.transformListForSelect(templates, 'htmlfile', 'id');
    let defaultTemplate = (options.length > 0 ? options[0].value : undefined);
    let select = $(Ui.select({
        name: 'template',
        options: options,
        value: defaultTemplate
    }));
    let autocomplete = Ui.autocomplete({
        options: pages,
        id: 'id',
        onSelect: function(option, i){
            i.val(option.id+'/');
        }
    });
    autocomplete.on('input', function(event){
        autocomplete.val(autocomplete.val().replace(/[^a-z0-9/]/gi, '_').toLowerCase());
    });
    let saveButton = $('<button class="button fullWidth"></button>').text('Zapisz').click(function(){
        data.resolver({
            page_url: autocomplete.val(),
            page_template: select.val()
        });
    });
    let tab = $("<table></table>").addClass('fullWidth');
    tab.append(getRow("Link", autocomplete));
    tab.append(getRow("Szablon", (new CustomSelect()).replace(select)));
    tab.append(getRow(saveButton));
    return tab;
};