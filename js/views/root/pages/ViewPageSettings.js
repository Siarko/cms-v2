let ViewPageSettings = {
    name: 'ViewPageSettings',
    parent: View
};
Class(ViewPageSettings, function ($this, $self) {

    $this.refresh = function(){
        $this.update();
    };

    $this.close = function () {
        let data = Root.provide($self);
        data.container.innerHTML = '';
    };

    $this.render = async function(){
        let data = Root.provide($self);
        let container = data.container;

        container.innerHTML = '';
        let pageData = await Pages.getPageData(data.pageId, false);
        console.log(pageData);

        template('rootConsole/pages/pageSettings', {
            pageUrl: pageData.id
        }, async function(html){
            container.appendChild(html);
            await document.getElementById('pageTemplateInput').appendChild(
                await Ui.requestSelect({
                    url: 'api/page/template/'+pageData.id,
                    name: 'template_id',
                    default: pageData.templateid,
                    values: async function(){
                        let options = {};
                        await new Promise(function(success){
                            ApiQuery.simpleGet('api/templates/all', function (result) {
                                console.log(result);
                                options = Ui.transformListForSelect(result.body, 'htmlfile', 'id');
                                success();
                            });
                        });
                        return options;
                    },
                    callback: function(result){
                        Messages.pageTemplateChanged(result.body);
                        View.viewPagesList.refresh();
                    }
                })
            );

            await $('#pagePermissions').append(await Ui.requestSelect({
                url: 'api/page/permissions/'+pageData.id,
                name: 'level',
                default: pageData.permlevel,
                values: async function(){
                    let options = {};
                    await new Promise(function(success){
                        ApiQuery.simpleGet('api/auth/get-avaiable-perm-levels', function (result) {
                            options = Ui.transformListForSelect(result.body, 'name', 'id');
                            success();
                        });
                    });
                    return options;
                },
                callback: function(result){
                    Messages.pagePermissionsChanged(result.body);
                    View.viewPagesList.refresh();
                }
            }));

            await $('#pageDelete').append(Ui.requestButton({
                text: 'USUŃ WSZYSTKO',
                cssClass: [
                    'fullWidth',
                    'red'
                ],
                click: function(resolve){
                    new jBox('Confirm', {
                        title: 'Usuwanie strony',
                        content: 'Na pewno usunąć stronę "'+pageData.id+'" ?',
                        confirmButton: 'Usuń',
                        cancelButton: 'Anuluj',
                        closeButton: true,
                        draggable: 'title',
                        confirm: function(){
                            resolve();
                        },
                        repositionOnOpen: true,
                        fixed: true
                    }).open();
                },
                url: 'api/page/delete/'+pageData.id,
                callback: function(result){
                    Messages.pageDeleted(result.body);
                    View.viewPagesList.refresh();
                    $this.close();
                }
            }));

            await $('#localizationsList').append(Ui.HtmlTable({
                data: pageData.localization,
                columns: [
                    {
                        key: 'language',
                        label: 'Język'
                    },
                    {
                        key: 'menuname',
                        label: 'Nazwa w menu'
                    },
                    {
                        key: 'menu_visibility',
                        label: 'Widoczność w menu'
                    },
                    {
                        label: '',
                        type: 'dynamic',
                        onRender: function (row) {
                            let button = document.createElement('button');
                            button.innerHTML = "Zarządzaj";
                            button.addEventListener('click', function(){
                                Pages.manageLocalization(row.url, row.language);
                            });
                            return button;
                        }
                    }
                ]
            }));

        });

    }
});