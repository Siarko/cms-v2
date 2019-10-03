let ViewFileList = {
    name: 'ViewFileList',
    parent: View
};
Class(ViewFileList, function($this, $self){

    let fileList = [];

    let fileLocation = null;

    $this.refresh = function(){
        Messages.refreshDone();
        $this.render();
    };

    $this.modify = async function(name){
        name = name.trim();
        let input = await Ui.requestInput({
            url: 'api/files/uploaded/rename/'+name,
            value: name,
            name: 'file_new_name',
            callback: function (response) {
                if(ApiQuery.isSuccess(response.body.status)){
                    Messages.fileRenameSuccess();
                    View.viewFileList.refresh();
                }else{
                    Messages.unknownError();
                }
            }
        });
        let tableCell = $(document.getElementById('fname_'+name));
        tableCell.html('');
        tableCell.append(input);
    };

    $this.delete = function(name){
        ApiQuery.simpleGet('api/files/uploaded/delete/'+name, function(response){
            if(ApiQuery.isSuccess(response.body)){
                Messages.fileDeleteSuccess(name);
            }else{
                Messages.fileDeleteFail(name);
            }
            $this.render();
        });
    };

    $this.parseFileSizes = function(){
        fileList.some(function(element){
            let size = element.fileSize;
            if(size>=1000000){
                element.fileSize = Number((element.fileSize/1000000).toFixed())+" Mb";
            }else if(size>=1000){
                element.fileSize = Number((element.fileSize/1000).toFixed(1))+" Kb";
            }
        });
    };

    $this.refreshFiles = function(callback){
        callback = callback||function(){};
        ApiQuery.simpleGet('api/files/uploaded/link', function(response){
            if(ApiQuery.isStatusSuccess(response)){
                fileLocation = response.body;
            }else{
                Messages.InternalError();
            }
            ApiQuery.simpleGet('api/files/uploaded/all', function(response){
                if(ApiQuery.isStatusSuccess(response)){
                    fileList = response.body;
                    $this.parseFileSizes();
                    callback();
                }else{
                    Messages.cannotLoadFileList();
                }
            });
        });

    };

    $this.constructList = function(){
        return Ui.HtmlTable({
            data: fileList,
            className: 'fileList',
            columns:[
                {
                    label: 'Nazwa pliku',
                    type: 'dynamic',
                    onRender: function(row){
                        let span = Ui.element('span');
                        span.id = 'fname_'+row.fileName;
                        let link = Ui.element('a');
                        link.href = fileLocation+'/'+row.fileName;
                        link.innerHTML = row.fileName;
                        span.appendChild(link);
                        return span.outerHTML;
                    }
                },
                {
                    label: 'Rozmiar',
                    key: 'fileSize'
                },
                {
                    label: 'Opcje',
                    type: 'dynamic',
                    onRender: function(row){
                        let buttons = "<i onclick=\"View.viewFileList.modify(\'"+row.fileName+"\')\" class='fas fa-edit'></i>";
                        buttons += "<i onclick=\"View.viewFileList.delete(\'"+row.fileName+"\')\" class='fas fa-trash-alt'></i>";
                        //Zrobione przez onclick bo fontawesome nie ogarnia zdarzeń podczepionych pod elementy
                        return buttons;
                    }
                }
            ]
        });
    };

    $this.render = function(){
        let data = Root.provide($self);
        $this.refreshFiles(function(){
            data.container.innerHTML = '';
            data.container.appendChild($this.constructList());
        });
    }
});