ViewUserList = {
    name: 'ViewUserList',
    parent: View
};
Class(ViewUserList, function ($this, $self) {
    $this.refresh = async function(){

        Messages.refreshDone();
        $this.update();
    };

    $this.render = function () {
        console.log("Render list");

        let data = Root.provide($self);
        let container = data.container;
        Users.fetchAvaiablePermlevels();
        Users.update(function () {
            container.innerHTML = '';
            container.appendChild(Ui.HtmlTable({
                data: Users.getUsers(),
                columns: [
                    {
                        key: 'name',
                        label: 'Nazwa'
                    },
                    {
                        key: 'permname',
                        label: 'Poziom uprawnień'
                    },
                    {
                        key: 'date',
                        label: 'Data utworzenia'
                    },
                    {
                        label: 'Zarządzanie',
                        type: 'dynamic',
                        onRender: function (row) {
                            if (row.permissions === "0") {
                                return "-";
                            } else {
                                let button = document.createElement('button');
                                button.innerHTML = "Zarządzaj";
                                button.classList.add('manageUser');
                                button.setAttribute('onclick', "Root.manage('" + row.name + "')");
                                return button.outerHTML;
                            }
                        }
                    }
                ]
            }));
        });
    }
});

