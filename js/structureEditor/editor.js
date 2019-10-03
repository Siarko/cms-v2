(function(){
    let breadcrumbsContainer = null;
    let content = null;
    let $this = this;

    let breadcrumbs = null;

    let getAddButtonTable = async function(options){
        let def = {
            addUrl: '',
            addResolver: function(resolver){resolver()},
            onAdd: function(){},
        };
        options = $.extend({}, def, options);
        let addButton = await Ui.requestButton({
            text: $('<i class="fas fa-plus"></i>'),
            cssClass: [
                'add'
            ],
            click: function(resolve){
                options.addResolver(resolve);
            },
            url: options.addUrl,
            callback: options.onAdd
        });
        return $("<table class='fullWidth noPadding'></table>").append($('<tr></tr>')
            .append($('<td></td>').append(addButton)));
    };

    let getRow = function(label, controll = null){
        if(controll !== null){
            return $('<tr></tr>')
                .append($('<td></td>').text(label))
                .append($('<td></td>').append(controll))
        }else{
            return $('<tr></tr>')
                .append($('<td></td>').html(label).attr('colspan', 2).addClass('separator'))
        }

    };

    let getButton = function(id, data = {}, label = null){
        if(label === null){
            label = $('<i class="fas fa-align-left"></i>');
        }
        let button = $('<div></div>').addClass('buttonSide').html($('<i></i>').addClass('fa fa-caret-right'));
        button.click(function(){
            breadcrumbs.add(label, {
                id: id,
                data:data
            });
        });
        return button;
    };

    let views = {};
    let currentView = null;


    this.updateBreadcrumbs = function(){
        let steps = breadcrumbs.construct();
        breadcrumbsContainer.html('');
        $.each(steps, function(k,v){
            let sp = $('<span></span>');
            if(typeof v.label === 'object'){
                let is = $('<span></span>').text(((k<steps.length-1)?'/':''));
                sp.html(v.label);
                sp.append(is);
            }else{
                sp.html(v.label+((k<steps.length-1)?'/':''));
            }
            if(k === steps.length-1){
                sp.addClass('current');
            }else{
                sp.click(function(){
                    breadcrumbs.go(k);
                });
            }
            breadcrumbsContainer.append(sp);
        });
    };

    this.updateContent = async function(data){
        content.html(await currentView.render(data));
    };

    this.loadView = async function(viewName){
        console.log("[STR EDIT] Load view: "+viewName);
        let viewCode = await loadScriptCode('structureEditor/views/View'+viewName);
        try{
            let context = {
                $this: $this,
                getRow: getRow,
                getButton: getButton,
                getAddButtonTable: getAddButtonTable,
                breadcrumbs: breadcrumbs
            };
            evalInContext(viewCode, context);
            views[viewName] = context;
        }catch (e) {
            console.error("Error while executing view code: "+e);
        }
    };

    this.view = async function(name, data = {}){
        console.log('[STR EDIT] Change view: '+name);
        if(!views.hasOwnProperty(name)){
            await $this.loadView(name);
        }

        currentView = views[name];
        $this.updateContent(data);
        $this.updateBreadcrumbs(data);
    };

    let init = function(){

        template('structureEditor/editor', {
                breadcrumbs: '',
                content: ''
            },
            function(html){
                $(document.body).append(html);
                $('.STR_body .closeButton').first().click(function(){
                    $('.STR_body').toggleClass('show');
                });
                $('.STR_openButton').first().click(function(){
                    $('.STR_body').toggleClass('show');
                });
                $('.STR_breadcrumbs_back').click(function(){
                    breadcrumbs.back();
                });

                breadcrumbs.onBack(function(data){
                    $this.view(data.id, data.data);
                });
                breadcrumbs.onAdd(function(data){
                    $this.view(data.id, data.data);
                });

                breadcrumbsContainer = $('.STR_breadcrumbs_container').first();
                content = $('.STR_content').first();
                breadcrumbs.add($('<i class="fas fa-home"></i>'),{id: 'ServiceList',data:{}});
            });
    };

    require([
        'modules/cssUtil',
        'modules/Breadcrumbs',
        'modules/Templates',
        'modules/ApiQuery',
        'modules/Ui',
        'modules/Pages',
        'modules/Languages',
        {
            url: 'contentFilters/dynamicControls',
            onload: function(){
                DynamicControls.scan();
            }
        },
        {
            url: 'modules/Languages',
            onload: function(){
                breadcrumbs = new Breadcrumbs();
                $(document).ready(init);
                console.log("STRUCTURE EDITOR LOADED");
                window.STR = this;
            }
        }
    ]);
})();

