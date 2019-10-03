window.Element = {
    get: function(name, params){
        let element = document.createElement(name);
        $.each(params, function(k, v){
            element[k] = v;
        });
        return element;
    }
};