let Switch = {
    create: function(options){
        let def = {
            value: false,
            onChange: function(){}
        };
        options = $.extend({}, def, options);

        let label = $(document.createElement("label"));
        label.addClass("switch");
        let element = $(document.createElement("input"));
        element.attr('type',"checkbox");
        let slider = $(document.createElement("span"));
        slider.addClass("slider");

        if(options.value){
            element.attr('checked', 'checked');
        }

        slider.click(function(){
            options.onChange((!element.is(":checked"))?'1':'0');
        });


        label.append(element);
        label.append(slider);
        return label;
    }
};