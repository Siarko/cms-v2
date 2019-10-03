function toggleClass(element, classname1, classname2 = null){
    if(typeof element === 'string'){
        element = document.getElementById(element);
    }
    if(classname2 !== null){ //toggle between 2 classes
        if(element.classList.contains(classname2)){
            element.classList.remove(classname2);
            element.classList.add(classname1);
        }else{
            element.classList.remove(classname1);
            element.classList.add(classname2);
        }
    }else{ //toggle single class
        element.classList.toggle(classname1);
    }
}

function unescapeHTML(escapedHTML) {
    return escapedHTML.replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&amp;/g,'&');
}

function toggleText(element, text1, text2){
    if(typeof element === 'string'){
        element = document.getElementById(element);
    }
    if(element.innerHTML === text2){
        console.log(element.innerHTML+"==="+text1);
        element.innerHTML = text1;
    }else{
        console.log(element.innerHTML+"==="+text2);

        element.innerHTML = text2;
    }
}

/*TODO toggleIcon - zmiana ikony wewnątrz elementu*/