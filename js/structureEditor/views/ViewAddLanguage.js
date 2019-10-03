this.render = async function(data){
    let table = $('<table></table>');
    let codeInput = $('<input type="text"/>');
    let labelInput = $('<input type="text"/>');
    let flagInput = $('<div></div>');
    let flagImg = $('<img/>');
    let flag = null;

    flagInput.append(flagImg);
    flagInput.append($('<button></button>').addClass('button fullWidth').text('Wybierz').click(async function(){
        Languages.selectFlag(function(flagData){
            flag = flagData.filename;
            flagImg.attr('src', flagData.url);
        })
    }));

    table.append(getRow('Kod języka', codeInput));
    table.append(getRow('Etykieta', labelInput));
    table.append(getRow('Flaga', flagInput));
    table.append(getRow($('<button></button>').addClass('button fullWidth').text('Zapisz').click(function(){
        data.resolver({
            code: codeInput.val(),
            name: labelInput.val(),
            image_path: flag
        });
    })));
    return table;
};