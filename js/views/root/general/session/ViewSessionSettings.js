ViewSessionSettings = {
    name: 'ViewSessionSettings',
    parent: View
};
Class(ViewSessionSettings, function ($this, $self) {

    $this.refresh = function(){
        $this.update();
    };

    $this.render = async function () {
        console.log("Render session settings");

        let consoleState = await ApiQuery.simpleGetPromise('api/debug/console/state');
        let consoleSwitchRow = $('#consoleSwitch');
        let label = Ui.element('label', 'Konsola');
        let sw = Switch.create({
            value: consoleState.body,
            onChange: function(state){
                ApiQuery.simplePost('api/debug/console/state', {
                    state: state
                },function (result){
                    new jBox('Notice',{
                        color: 'yellow',
                        content: 'Zmiana będzie widoczna po odświeżeniu!'
                    });
                });
            }
        });

        consoleSwitchRow.html('');
        consoleSwitchRow.append(label);
        consoleSwitchRow.append(sw);
    }
});

