ViewNewUser = {
    name: 'ViewNewUser',
    parent: View
};
Class(ViewNewUser, function ($this, $self) {
    $this.refresh = async function(){
        Messages.refreshDone();
        $this.update();
    };

    $this.render = async function () {
        let permLevels = await Users.getAvaiablePermlevels();
        let select = $('#newUserPerm');
        let s = Ui.select({
            options: Ui.transformListForSelect(permLevels)
        });
        select.html(s.innerHTML);
    }
});

