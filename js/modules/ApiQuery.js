let _ApiQuery = function(){

    let statusNames = [];
    let $scope = this;

    this.query = function(options, onDone){
        onDone = onDone||function(){};
        $.ajax(options).done(function(response){
            if(!$scope.isStatusSuccess(response) && statusNames.length!==0){
                let name = getKeyByValue(statusNames, response.status);
                console.log("Error in answer from server: "+options.url+" m:"+options.type+" s:"+response.status+" - "+name);
            }
            onDone(response);
        });
    };

    this.sendForm = function(url, data, callback){
        $scope.query({
            url: url,
            type: 'POST',
            data: data,
            processData: false,
            contentType: false
        }, callback);
    };

    this.simpleGet = function(url, callback){
        $scope.query({
            url: url+"?fake_lang="+LANGUAGE,
            type: 'GET',
        }, callback);
    };

    this.simpleGetPromise = function(url){
        return new Promise(function(resolve){
            ApiQuery.simpleGet(url, function(result){
                resolve(result);
            });
        });
    };

    this.simplePost = function(url, data, callback){
        $scope.query({
            url: url+"?fake_lang="+LANGUAGE,
            type: 'POST',
            data: data
        }, callback);
    };

    let requestStatusNames = function(){
        $scope.simpleGet('api/legend/statusnames', function(response){
            statusNames = response.body;
        });
    };
    requestStatusNames();

    this.isStatus = function(value, statusName){
        return (value === statusNames[statusName]);
    };

    //Ogólnie, czy połączenie jest ok
    this.isStatusSuccess = function(response){
        return $scope.isSuccess(response.status);
    };

    //Czy state jest równy sukcesowi
    this.isSuccess = function(state){
        return (state === statusNames.SUCCESS);
    };



    return this;

};

window.ApiQuery = new _ApiQuery(); //tylko var może wprowadzić nową zmienną przez eval do głównego scope
