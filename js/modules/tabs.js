function Tab(options) {
    options = $.extend({
        container: null, //element
        classOnActive: null,
        classOnInactive: null,
        onShow: function () {
        }, //wykonane w momencie aktywacji karty
        onHide: function () {
        } //wykonane w momencie schowania karty
    }, options);

    if (typeof options.container === 'string') {
        options.container = document.getElementById(options.container);
    }

    this.getContainer = function () {
        return options.container;
    };
    this.getId = function () {
        return options.container.id;
    };
    this.getActiveClass = function () {
        return options.classOnActive;
    };
    this.getInactiveClass = function () {
        return options.classOnInactive;
    };
    this.getOnShow = function () {
        return options.onShow;
    };
    this.getOnHide = function () {
        return options.onHide;
    };
    return this;
}

function TabManager(options) {
    let THIS = this;
    options = $.extend({
        tabs: null, //klasa kart
        active: null, //id domyślnie aktywnej karty
        classOnActive: 'tabActive',
        classOnInactive: 'tabInactive',
        onShow: function () {
        },
        onHide: function () {
        },
        tabButtons: { //ustawienia przycisków powiązanych z tabami
            activeClass: '',
            inactiveClass: null
        }
    }, options);

    let linkedElements = [];

    let doOnLoad = function(c){
        if(c.hasAttribute("onload")){
            let onLoad = c.getAttribute("onload");
            evalInContext(onLoad, c); //loads onload in context of element
        }
    };

    let setUrl = function(c){
        url = c.getAttribute('data-url');
        if(url){
            UrlFiddle.set(url);
        }
    };

    let getTabByUrl = function(url){
        id = null;
        options.tabs.some(function(tab){
            let c = tab.getContainer();
            elemUrl = c.getAttribute('data-url');
            if(elemUrl === url){
                id = c.id;
                return true;
            }
        });
        return id;
    };

    let constructClasses = function (className) {
        let tabRet = [];
        let tabs = document.getElementsByClassName(className);
        for (i = 0; i < tabs.length; i++) {
            tabRet.push(new Tab({container: tabs[i]}));
        }
        return tabRet;
    };

    let hideInactive = function () {
        let active = options.active;
        if(!UrlFiddle.isBase()){
            active = getTabByUrl(UrlFiddle.getSubPage()) || active;
        }

        options.tabs.forEach(function (tab) {
            let c = tab.getContainer();
            let styleClass = tab.getInactiveClass() || options.classOnInactive;
            if (c.id !== active && !c.classList.contains(styleClass)) {
                c.classList.add(styleClass);
                tab.getOnHide()();
            }
            if (c.id === active) {
                c.classList.add(options.classOnActive);
                doOnLoad(c);
                setUrl(c);
            }
        });
    };

    if (typeof options.tabs === 'string') {
        options.tabs = constructClasses(options.tabs);
    }
    hideInactive();

    let buttonSetState = function(element, state){
        if(state){
            if(options.tabButtons.inactiveClass){
                element.classList.remove(options.tabButtons.inactiveClass);
            }
            element.classList.add(options.tabButtons.activeClass);
        }else{
            element.classList.remove(options.tabButtons.activeClass);
            if(options.tabButtons.inactiveClass){
                element.classList.add(options.tabButtons.inactiveClass);
            }
        }
    };

    /*
    * @param id Id taba który ma zostać aktywowany
    * @param buttonElement Element html który jest 'przyciskiem' aktywującym taba
    * */
    this.activate = function (id, buttonElement = null) {

        let alreadyLinked = function (element) { //check if element is already linked
            let flag = false;
            linkedElements.forEach(function(single){
                if(single.elem === element){
                    flag = true;
                    return true;
                }
            });
            return flag;
        };
        let chButtonState = function () {
            if (buttonElement !== null) {
                let candidate = {elem: buttonElement, id: id};
                if (!alreadyLinked(buttonElement)) {
                    linkedElements.push(candidate);
                }
                //ok, przycisk został przekazany więc trzeba go aktywować
                buttonSetState(buttonElement, true);
                // i dezaktywować wszystkie inne połączone przyciski
                linkedElements.forEach(function (element) {
                    if(element.elem === buttonElement){return true;} //czy to ten wciśnięty; pomiń go
                    buttonSetState(element.elem, false); //to nie ten - dezaktywuj
                })
            }
        };

        options.tabs.forEach(function (tab) {
            let classActive = tab.getActiveClass() || options.classOnActive;
            let classInactive = tab.getInactiveClass() || options.classOnInactive;
            let c = tab.getContainer();
            if (tab.getId() !== id && c.classList.contains(classActive)) {
                c.classList.remove(classActive);
                c.classList.add(classInactive);
                tab.getOnHide()();
            }
            if (tab.getId() === id) {
                c.classList.remove(classInactive);
                c.classList.add(classActive);
                tab.getOnShow()();

                doOnLoad(c);
                setUrl(c);

            }
        });
        chButtonState(); //update linked buttons
    };


    return this;
}

/*
* Stworzenie managera tabów:
* tabs = new TabManager({tabs:[obiekty tabów]});
* parametr tabs może zawierać tablicę z obiektami tabów lub string z nazwą klasy tabów
* */