ViewUserSettings = {
    name: 'ViewUserSettings',
    parent: View
};
Class(ViewUserSettings, function ($this, $self) {
    $this.render = async function () {
        let data = Root.provide($self);

        console.log("Render settings");

        if (!data.userId) {
            return;
        }
        let title = document.createElement('span');
        title.innerHTML = "Ustawienia: " + data.userId;
        let detailPane = data.container;
        if (!data.userId) {
            detailPane.innerHTML = "";
            return;
        }
        let managementPane = await $this.constructUserManagement(data.userId);
        if (!managementPane) {
            managementPane = Ui.element('p', 'Wystąpił nieoczekiwany błąd')
        }
        detailPane.innerHTML = "";
        detailPane.appendChild(title);
        detailPane.appendChild(managementPane);

    };

    $this.update = function(){
        Users.update(function(){
            $this.render();
        });
    };

    $this.close = function () {
        let data = Root.provide($self);
        data.container.innerHTML = '';
    };

    $this.constructUserManagement = async function (userid) {



        let permLevels = await Users.getAvaiablePermlevels();
        let user = Users.getUserById(userid);
        if (!user) {
            return null;
        }
        let pane = Ui.element('div');
        let form = Ui.formBegin('api/user/modify/' + userid, 'POST');
        form.appendChild(Ui.labeledInput({
            label: 'Nazwa',
            name: 'user_name',
            value: user.name
        }));
        form.appendChild(Ui.labeledInput({
            type: 'select',
            label: 'Poziom uprawnień',
            name: 'user_permlevel',
            value: user.permissions,
            select: Ui.transformListForSelect(permLevels)
        }));
        form.appendChild(Ui.submitAjax({
            text: 'Wyślij dane',
            cssClass: ['fullWidth'],
            callback: function (response) {
                Root.refresh('modify_users', response);
            }
        }));
        pane.appendChild(form);
        pane.appendChild(Ui.requestButton({
            url: 'api/user/delete/' + userid,
            text: 'Usuń użytkownika',
            cssClass: ['red', 'fullWidth'],
            callback: function (response) {
                Messages.userDeleted(ApiQuery.isSuccess(response.body));
                View.viewUserList.update();
                $this.close();
            }
        }));

        return pane;
    };
});
