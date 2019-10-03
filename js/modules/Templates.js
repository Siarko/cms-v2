let TemplateStorage = {
    content: {},
    put: function(id, content){
        this.content[id] = content;
    },
    exists: function(id){
        return this.content.hasOwnProperty(id);
    },
    get: function(id){
        return this.content[id];
    }
};

let Template = function(templateName, bindParams, afterAction){
    let $this = this;

    let logicParse = function(html){
        let injectors = $(html).find("*[data-inject]");
        $.each(injectors, function(k,v){
            v = $(v);
            let expression = v.data('inject');
            let result = evalInContext(expression, bindParams);
            v.html(result);
        });

    };

    let parse = function(text){
        let html = $(text).get(0);
        logicParse(html);
        return html;
    };

    let constructPath = function(name){
        return "js/htmlTemplates/"+name+".html";
    };
    this.execute = function(){
        if(TemplateStorage.exists(templateName)){
            let html = parse(TemplateStorage.get(templateName));
            afterAction(html);
            return;
        }
        let xhrObj = new XMLHttpRequest();
        xhrObj.open('GET', constructPath(templateName));
        xhrObj.onreadystatechange = function(){
            if(xhrObj.readyState !== 4){return;}
            if(xhrObj.status !== 200){
                console.log("Error while loading template: "+templateName);
                return;
            }
            TemplateStorage.put(templateName, xhrObj.responseText);
            let html = parse(xhrObj.responseText);
            afterAction(html);
        };
        xhrObj.send(null);
    }
};
function template(name, bind, action){
    let template = new Template(name, bind, action);
    template.execute();
    return template;
}