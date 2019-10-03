ViewPagesList = {
    name: 'ViewPagesList',
    parent: View
};
Class(ViewPagesList, function ($this, $self) {

    $this.refresh = async function () {
        await Pages.refreshPagesData();
        Messages.refreshDone();
        $this.update();
    };

    $this.render = async function () {
        let data = Root.provide($self);
        let container = data.container;
        let pagesList = await Pages.getPagesData();
        await PageService.requestTemplateData();
        await Languages.fetchLanguageList();
        container.innerHTML = Ui.HtmlTable({
            data: pagesList,
            columns: [
                {
                    label: 'Url',
                    type: 'dynamic',
                    onRender: function(row){
                        return row.id.split('_')[0];
                    }
                },
                {
                    label: 'Poziom',
                    type: 'dynamic',
                    onRender: function(row){
                        return row.permlevel;
                    }
                },
                {
                    label: '',
                    type: 'dynamic',
                    onRender: function(row){
                        if(row.id === 'root'){return '-'}
                        let button = document.createElement('button');
                        button.innerHTML = "Zarządzaj";
                        button.setAttribute('onclick', "Pages.manage('" + row.id + "')");
                        return button.outerHTML;
                    }
                }
            ]
        }).outerHTML;
    }
});