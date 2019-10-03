this.render = function (data) {
    let table = $("<table></table>");
    table.append(getRow('Struktura stron', getButton('PageList')));
    table.append(getRow('Języki', getButton('LanguageList',{},$('<i class="fas fa-flag"></i>'))));
    return table;
};