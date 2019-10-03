let _DynamicControls = function(){
    let $this = this;
    const CSS_CLASS = 'DC';

    let updateSettings = function(id, element){
        let settings = {};
        $(element).each(function() {
            $.each(this.attributes, function() {
                if(this.specified && this.name.substring(0, 5) === 'data-') {
                    settings[this.name.substring(5)] = this.value;
                }
            });
        });
        let data = {id: id, settings: settings};
         ApiQuery.simplePost('api/filters/settings/update', data, function(result){
             Messages.standardInfo(result.body, 'Ustawienia filtra zaaktualizowane', 'Wystąpił błąd');
             let trash = element.children(':not(.DC_controls)');
             trash.remove();
             element.append(result.body);
         })
    };

    let loadedForms = [];
    let loadForm = async function(name, settings){
        if(loadedForms.indexOf(name) === -1){
            await requirePromise('contentFilters/forms/'+name);
            loadedForms.push(name);
        }
        let nameFull = 'CF_'+capitalizeFirstLetter(name);
        return window[nameFull](settings);
    };

    let getSettings = function(element){
        element = element.get(0).attributes;
        let result = [];
        $.each(element, function(k,v){
            let name = v.nodeName;
            if(name.indexOf("data-setting-") !== -1){
                let settingName = name.substring(13);
                result[settingName] = v.nodeValue;
            }
        });
        return result;
    };

    let constructPanel = async function(id, element, panel){
        let filters = (await ApiQuery.simpleGetPromise('api/filters/all')).body;
        let type = element.attr('data-type');
        let settings = getSettings(element);
        let select = Ui.select({
            options: Ui.transformListForSelect(filters, 'cssClass', 'cssClass'),
            value: type
        });
        $(select).change(function(){
            let v = $(select).val();
            element.attr('data-type', v);
            updateSettings(id, element);
        });
        /*let objectName = await loadForm(type, settings);*/

        let table = Ui.table([
            ['Rodzaj filtra', (new CustomSelect()).replace(select)],
        ]);
        panel.append(table);
    };

    let attachControls = async function(id, element){
        let container = $('<div class="'+CSS_CLASS+'_controls"></div>');
        let panel = $('<div class="'+CSS_CLASS+'_panel"></div>');
        let cog = $('<div class="cog"><i class="fa fa-cog"></i></div>');
        constructPanel(id, element, panel);

        container.append(cog);
        container.append(panel);
        element.prepend(container);
        cog.click(function(){
            panel.toggleClass('open');
        });
    };
    //scan for new x-dynamic elements
    this.scan = function(){
        let elements = $('x-dynamic');
        $.each(elements, function(k,v){
            v = $(v);
            let controls = v.find('.'+CSS_CLASS+'_controls');
            if(controls.length === 0){
                attachControls(k, v);
            }
        });
    };

    return this;
};
window.DynamicControls = new _DynamicControls();