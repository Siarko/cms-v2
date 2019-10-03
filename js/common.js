/* creates class, #1 - nazwa klasy, #2 - funkcja z kodem, #3 klasa rozszerzająca
*   funkcja z kodem: function(this context, static context){}
* */
function Class(classData, codeDynamic, codeStatic){
    codeStatic = codeStatic || function(){};
    let name = classData.name;
    if(classData.parent){
        window[name] = function(){//deklaracja klasy
            classData.parent.call(this);//wywołanie konstruktora klasy parent
            codeDynamic(this, window[name]);
        };
        window[name].prototype = Object.create(classData.parent.prototype);//rozszerzenie
        window[name].prototype.constructor = window[name];//naprawa konstruktora
        window[name].parent = classData.parent;
        //Object.assign(window[name], classData.parent);
    }else{
        window[name] = function(){
            codeDynamic(this, window[name]);
        }//prosta deklaracja bez rozszerzenia
    }
    window[name].className = name;
    codeStatic(window[name]);

    //code(thisContext, window[name]);//ciało klasy z kontekstem dynamicznym i statycznym
}


function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function evalInContext(code, context){
    let c = '';
    $.each(context, function(k,v){
         c += 'let '+k+" = this."+k+";\n";
    });
    return function(){return eval(c+code);}.call(context);
}

function getKeyByValue(object, value) {
    return Object.keys(object).find(key => object[key] === value);
}

let $rootScope = window;

let loadScriptCode = async function(path){
    return new Promise(function(success){
        let fullPath = 'js/'+path+".js";
        let xhrObj = new XMLHttpRequest();
        xhrObj.open('GET', fullPath);
        xhrObj.onreadystatechange = function(){
            if(xhrObj.readyState !== 4){return;}
            if(xhrObj.status !== 200){
                console.error("Error while loading module: "+fullPath);
                return;
            }
            success(xhrObj.responseText);
        };
        xhrObj.send(null);
    });
};

async function requirePromise(name){
    return new Promise(function(success){
        let xhrObj = new XMLHttpRequest();
        let path = "js/"+name+".js";
        xhrObj.open('GET', path);
        xhrObj.onreadystatechange = function(){
            if(xhrObj.readyState !== 4){return;}
            if(xhrObj.status !== 200){
                console.log("Error while loading module: "+path);
                return;
            }
            try{
                console.log("Executing: "+path);
                window.eval(xhrObj.responseText);
            }catch(err){
                console.error("Error while executing: "+path);
                console.log(err);
            }
            success();
        };
        xhrObj.send(null);
    });
}

function require(scripts){ //load script
    let recurrentLoader = function(scripts){
        if(scripts.length === 0){return;}
        let name = null;
        let callback = function(){};
        let currentScript = scripts[0];
        if(currentScript instanceof Object){
            name = currentScript.url;
            if(currentScript.onload instanceof Function){
                callback = currentScript.onload;
            }
        }else{
            name = currentScript;
        }
        let xhrObj = new XMLHttpRequest();
        let path = "js/"+name+".js";
        xhrObj.open('GET', path);
        xhrObj.onreadystatechange = function(){
            if(xhrObj.readyState !== 4){return;}
            scripts.shift();
            if(xhrObj.status !== 200){
                console.log("Error while loading module: "+path);
                return;
            }
            try{
                console.log("Executing: "+path);
                window.eval(xhrObj.responseText);
            }catch(err){
                console.error("Error while executing: "+path);
                console.log(err);
            }
            callback();
            recurrentLoader(scripts);
        };
        xhrObj.send(null);
    };

    if(Array.isArray(scripts)){
        recurrentLoader(scripts);
    }else if(scripts instanceof Object && scripts !== null){
        recurrentLoader([scripts]);
    }
}

function getFunctionArgs(func) {
    return (func + '')
        .replace(/[/][/].*$/mg,'') // strip single-line comments
        .replace(/\s+/g, '') // strip white space
        .replace(/[/][*][^/*]*[*][/]/g, '') // strip multi-line comments
        .split('){', 1)[0].replace(/^[^(]*[(]/, '') // extract the parameters
        .replace(/=[^,]+/g, '') // strip any ES6 defaults
        .split(',').filter(Boolean); // split & filter [""]
}

$(document).ready(function(){
    console.log("SiarkoCMS loaded");

    require([
        'modules/Messages',
        'modules/Templates',
        {
            url: 'modules/UrlFiddle',
            onload: function () {
                UrlFiddle.setBasePage('root');
            }
        },
        'modules/ApiQuery',
        'modules/Ui'
    ]);
});
