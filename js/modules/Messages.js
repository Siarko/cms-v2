window.Messages = {
    cannotLoadUserList: function(){
        new jBox('Notice',{
            color: 'red',
            content: 'Nie udało się załadować listy użytkowników!'
        });
    },
    cannotLoadAvaiablePermlevels: function () {
        new jBox('Notice',{
            color: 'red',
            content: 'Nie udało się załadować listy poziomów uprawnień!'
        });
    },
    InternalError: function(){
        new jBox('Notice',{
            color: 'red',
            content: 'Wystąpił wewnętrzny błąd działania aplikacji!<br/>Programista o czymś zapomniał...'
        });
    },
    QueryError: function(name){
        new jBox('Notice',{
            color: 'red',
            content: 'Wystąpił błąd!<br/>Opcja "'+name+'" nie została wprowadzona'
        });
    },
    QuerryOk: function(){
        new jBox('Notice',{
            color: 'green',
            content: 'Zmiany zostały pomyślnie wprowadzone'
        });
    },
    appLoaded: function(){
        new jBox('Notice',{
            color: 'green',
            content: 'Aplikacja załadowana'
        });
    },
    userDeleted: function(state){
        if(state){
            message = "Użytkownik został usunięty";
            color = 'green';
        }else{
            message = "Coś poszło nie tak";
            color = 'red';
        }
        new jBox('Notice', {
            color: color,
            content: message
        });
    },
    refreshDone: function(){
        new jBox('Notice',{
            color: 'green',
            content: 'Odświeżanie zakończone'
        });
    },
    cannotLoadFileList: function(){
        new jBox('Notice',{
            color: 'red',
            content: 'Nie można pobrać listy plików'
        });
    },
    unknownError: function(){
        new jBox('Notice',{
            color: 'red',
            content: 'Wystąpił nieznany bład'
        });
    },
    fileRenameSuccess: function(){
        new jBox('Notice',{
            color: 'green',
            content: 'Zmieniono nazwę pliku'
        });
    },
    fileDeleteSuccess: function(name){
        new jBox('Notice',{
            color: 'green',
            content: 'Usunięto plik '+name
        });
    },
    fileDeleteFail: function(name){
        new jBox('Notice',{
            color: 'red',
            content: 'Bład podczas usuwania pliku<br/>'+name
        });
    },

    fieldIsRequired: function(count = 1){
        new jBox('Notice',{
            color: 'red',
            content: 'Nie zostały wypełnione wszystkie wymagane pola '+((count!==0)?'( '+count+' )':'')
        });
    },

    pageCreated: function(url){
        new jBox('Notice',{
            color: 'green',
            content: 'Strona '+url+' została utworzona'
        });
    },

    pageNotCreated: function(){
        new jBox('Notice',{
            color: 'red',
            content: 'Strona nie mogła zostać utworzona'
        });
    },

    standardInfo: function(state, infoOk, infoErr){
        if(infoErr === undefined){
            infoErr = 'Błąd';
        }
        let msg = infoOk;
        if(!state){
            msg = infoErr;
        }
        new jBox('Notice',{
            color: ((state)?'green':'red'),
            content: msg
        });
    },
    pageDeleted: function(state){
        Messages.standardInfo(state, 'Strona została usunięta', 'Nie udało się usunąć strony')
    },
    pageNameChanged: function(state){
        Messages.standardInfo(state, 'Nazwa strony została zmieniona', 'Nie można zmienić nazwy');
    },
    pageTemplateChanged: function(state){
        Messages.standardInfo(state, 'Zmieniono szablon strony', 'Nie można zmienić szablonu');
    },
    pageVisibilityChanged: function(state){
        Messages.standardInfo(state, 'Widoczność strony w menu została zmieniona', 'Nie można zmienić widoczności');
    },
    pagePermissionsChanged: function(state){
        Messages.standardInfo(state, 'Uprawnienia strony zmienione', 'Nie można zmienić uprawnień');
    },
    languageStateChanged: function(state){
        Messages.standardInfo(state, 'Stan języka zmieniony', 'Nie można zmienić stanu języka');
    },
    languageAdded: function(state){
        Messages.standardInfo(state, 'Nowy język został dodany', 'Nie dodać języka');
    }
};