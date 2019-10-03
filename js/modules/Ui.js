function _UI(){

    let $scope = this;

    this.byId = function(id){
        return document.getElementById(id);
    };

    this.loadingScreen = function(){

    };

    this.transformListForSelect = function (list, labelName = 'name', valueName = 'id') {
        let result = [];
        $.each(list, function (k, v) {
            result.push({
                label: ((labelName!==null)?v[labelName]:v),
                value: ((labelName!==null)?v[valueName]:v)
            });
        });

        return result;
    };

    this.tabHighlighter = function(){
      this.a = 'hello';

      return this;
    };

    this.element = function(name, content){
        let e = document.createElement(name);
        e.innerHTML = content || '';
        return e;
    };

    this.formBegin = function(url, method){
        form = $scope.element('form');
        form.action = url;
        form.method = method;
        return form;
    };

    this.submit = function(name){
        let button = $scope.element('input', name);
        button.type = 'submit';
        return button;
    };

    this.validateForm = function (form) {
        let flag = true;
        let count = 0;
        $('[required]').each(function(k,v){
            if(v.value.trim().length === 0 ){
                count++;
                flag = false;
            }
        });
        if(!flag){
            Messages.fieldIsRequired(count);
        }
        return flag;
    };

    this.sendForm = function(form, sendingElement, callback){
        if(!$scope.validateForm(form)){
            return;
        }
        let formData = new FormData(form);
        ApiQuery.sendForm(form.action, formData, function(response){
            callback(response);
        });
    };

    this.submitAjax = function(options){
        callback = options.callback || function(){};
        let button = $scope.element('button', options.text);
        if(options.cssClass){
            $.each(options.cssClass, function(k,v){
                button.classList.add(v);
            });
        }
        button.setAttribute('onclick', "Ui.sendForm(this.form, this, "+callback+")");
        button.type = "button";
        return button;
    };

    /*url,text,callback*/
    this.requestButton = function(options){
        let onClick = options.click || function(resolve){resolve();};
        let button = $scope.element('button');
        if(options.cssClass){
            $.each(options.cssClass, function(k,v){
                button.classList.add(v);
            });
        }
        button.type = "button";
        $(button).html(options.text);
        button.addEventListener('click', function(){
            onClick(function(data = null){
                if(data == null){
                    ApiQuery.simpleGet(options.url,options.callback);
                }else{
                    ApiQuery.simplePost(options.url, data, options.callback);
                }
            });
        });

        return button;
    };

    this.requestInput = async function(options){
        let def = {
            url: null,
            type: 'text', //input type
            name: '', //name in POST
            value: '', //default input value
            replace: null, //replace input by return of function
            cssClass: 'requestInput',
            callback: function(){}
        };
        options = $.extend({}, def, options);
        let elem = Ui.element('span');
        let input = Ui.element('input');
        input._container = elem;
        input.type = options.type;
        if(options.value instanceof Function){
            input.value = await options.value();
        }else{
            input.value = options.value;
        }
        let button = Ui.element('span');
        let icon = Ui.element('i');
        icon.classList.add('fas','fa-check');
        button.appendChild(icon);

        button.addEventListener('click', function(){
            let data = {};
            data[options.name] = input.value;
            ApiQuery.simplePost(options.url, data, function(result){
                if(options.replace !== null){
                    elem.parentElement.innerHTML = options.replace(result);
                }
                options.callback(result);
            });
        });
        $(elem).addClass(options.cssClass);
        elem.appendChild(input);
        elem.appendChild(button);
        return elem;
    };

    this.requestSelect = async function(options){
        let def = {
            url: null,
            name: '', //name in POST
            values: {},
            default: '', //default input value
            cssClass: 'requestInput',
            callback: function(){}
        };
        options = $.extend({}, def, options);
        let select = document.createElement('select');

        if(options.values instanceof Function){
            options.values = await options.values();
        }

        $.each(options.values, function(objectKey, index) {
            let option = document.createElement('option');
            option.value = index.value;
            option.innerText = index.label;
            if(index.value === options.default){
                option.setAttribute('selected', 'selected');
            }
            select.appendChild(option);
        });

        $(select).on('change', function(){
            let data = {};
            data[options.name] = select.value;
            ApiQuery.simplePost(options.url, data, function(result){
                options.callback(result, select.value);
            });
        });

        return select;
    };

    this.autocomplete = function(options){
        let constructOptionList = function(options, id){
            if(id === null){
                console.error("No id specified for autocomplete");
            }
            let result = [];
            $.each(options, function(k,v){
                result.push({label: v[id], value: v})
            });
            return result;
        };
        let filter = function(searchString){
            let res = [];
            $.each(options.options, function(k,v){
                if(v.label.toLowerCase().indexOf(searchString.toLowerCase()) !== -1){
                    res.push(v);
                }
            });
            return res;
        };
        let def = {
            options: [],
            id: null,
            onSelect: function(obj){},
        };
        options = $.extend({}, def, options);
        options.options = constructOptionList(options.options, options.id);

        let container = $("<div class='autocomplete'></div>");
        let input = $("<input type='text'/>");
        container.val = function(r){
            if(r === undefined){
                return input.val();
            }else{
                input.val(r);
            }
        };
        container.on = function(a,b,c,d){
            return input.on(a,b,c,d);
        };
        let list = $("<ul></ul>");
        input.on('input', function(){
            if(input.val().length > 1){
                let items = filter(input.val());
                list.html('');
                $.each(items, function(k,v){
                    let item = $("<li></li>");
                    item.click(function(e){
                        options.onSelect(v.value, input);
                        list.removeClass('active');
                        e.stopPropagation();
                    });
                    item.html(v.label);
                    list.append(item)
                });
                $(list.children()[0]).addClass('hover');
                list.attr('data-index', 0);
                list.addClass('active');
            }else{
                list.removeClass('active');
            }
        });
        input.on('keydown', function(event){
            if(list.children().length === 0){return;}
            let selected = parseInt(list.attr('data-index'));
            if(selected === undefined){
                selected = 0;
            }
            if(event.key === 'Escape' || event.key === 'Tab'){
                list.removeClass('active');
            }
            if(event.key === 'Enter'){
                $(list.children()[selected]).click();
                return;
            }
            if(event.key === 'ArrowUp'){
                event.preventDefault();
                if(selected > 0){
                    $(list.children()[selected]).removeClass('hover');
                    selected--;
                    $(list.children()[selected]).addClass('hover');
                }
            }
            if(event.key === 'ArrowDown'){
                event.preventDefault();
                let max = list.children().length-1;
                if(selected < max){
                    $(list.children()[selected]).removeClass('hover');
                    selected++;
                    $(list.children()[selected]).addClass('hover');
                }
            }
            list.attr('data-index', selected);
        });
        $('body').click(function(){
            list.removeClass('active')
        });
        container.append(input);
        container.append(list);

        return container;
    };

    this.select = function(options){
        let select = $scope.element('select');
        select.name = options.name;
        select.classList.add(options.inputClass);
        $.each(options.options, function (k, v) {
            let option = $scope.element('option');
            option.value = v.value;
            if(v.value === options.value && option.value !== undefined){
                option.setAttribute('selected', 'selected');
            }
            option.innerHTML = v.label;
            select.appendChild(option);
        });
        return select;
    };

    this.labeledInput = function(options){
        let defaults = {
            label: 'No label',
            name: 'input_no_name',
            value: null,
            type: 'text',
            containerClass: 'inputWithLabel',
            required: false,
            labelClass: 'inputLabel',
            inputClass: 'inputInput',
            select:[{value:'value_not_set',label:'label_not_set'}],
        };
        options = $.extend({}, defaults, options);
        let label = $scope.element('span');
        label.innerHTML = options.label;
        label.classList.add(options.labelClass);
        let input = null;
        if(options.type !== 'select'){
            input = $scope.element('input');
            input.name = options.name;
            input.type = options.type;
            input.classList.add(options.inputClass);
            input.value = options.value || '';
        }else{
            input = $scope.select({
                name: options.name,
                inputClass: 'custom-select',
                options: options.select,
                value: options.value,
            });
        }
        if(options.required){
            input.setAttribute('required', 'required');
        }

        let container = $scope.element('div');
        container.classList.add(options.containerClass);
        container.appendChild(label);
        container.appendChild(input);
        return container;

    };

    this.table = function(data){
        let table = $('<table></table>');
        $.each(data, function(k, v){
            let row = $('<tr></tr>');
            $.each(v, function(a, b){
                let cell = $('<td></td>');
                if(typeof b === 'string'){
                    cell.html(b);
                }else{
                    cell.append(b);
                }
                row.append(cell);
            });
            table.append(row);
        });
        return table;
    };

    /*
    * data:
    * {
    *   data: [tabela z obiektami],
    *   className: 'klasa', <- klasa css dla tabeli
    *   rowWrapperWithTr: bool, <- czy zawrzeć tr
    *   rowWrapper: function(rowData, rowHtml){}
    *   columns: [
    *       {
    *        key: id w obiekcie,
    *        label: nazwa kolumny
    *        },
    *        {
    *         key: ..
    *         label: ..
    *         type: 'text'|'dynamic'
    *         onRender: function(row){}
    *         wrapper: function(content){}
    *        }
    *       ...
    *   ]
    * }
    * type - jeżeli mają być używane onRender to dynamic
    * onRender - zwraca wygenerowaną zawartość dla komórki w danej tabeli
    * rowWrapper dostaje dane całego wiersza i zwraca jego zawartość
    * */
    this.HtmlTable = function(data){
        let def = {
            className: ''
        };
        $.extend({}, def, data);

        let getCellElem = function(row, col){
            let cell = document.createElement('td');
            if('type' in col){
                if(col.type === 'text'){
                    cell.innerHTML = row[col.key];
                }
                if(col.type === 'dynamic'){
                    let result = col.onRender(row);
                    if(typeof result === 'object'){
                        cell.innerHTML = '';
                        cell.appendChild(result);
                    }else{
                        cell.innerHTML = col.onRender(row);
                    }
                }
            }else{
                cell.innerHTML = row[col.key];
            }
            return cell;
        };

        let getEmptyTableContent = function(){
            let tr = Ui.element('tr');
            let td = Ui.element('td');
            td.colSpan = data.columns.length;
            td.innerHTML = "Brak danych";
            tr.appendChild(td);
            return tr;
        };

        let table = document.createElement('table');
        if(data.className !== null && data.className !== undefined){
            table.classList.add(data.className);
        }
        if(data.data.length !== 0){
            let header = document.createElement('tr');
            $.each(data.columns, function(k, column){
                let cell = document.createElement('th');
                cell.innerHTML = column.label;
                header.appendChild(cell);
            });
            table.appendChild(header);
            $.each(data.data, function(k, row){
                let rowElem = document.createElement('tr');
                $.each(data.columns, function(k, col){
                    rowElem.appendChild(getCellElem(row, col));
                });
                if(data.rowWrapper && !data.rowWrapperWithTr){
                    let nodes = rowElem.childNodes;
                    rowElem.innerHTML = '';
                    rowElem.appendChild(data.rowWrapper(row, nodes));
                }
                if(data.rowWrapper && data.rowWrapperWithTr){
                    rowElem = data.rowWrapper(row, rowElem);
                }
                table.appendChild(rowElem);
            });
        }else{
            table.appendChild(getEmptyTableContent());
        }

        return table;
    }
}
window.Ui = new _UI();