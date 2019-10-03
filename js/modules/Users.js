

let _Users = function(){

    let $scope = this;
    this.users = null;
    this.permLevels = null;

    this.update = function(callback){
        callback = callback || function(){};
        ApiQuery.simpleGet('api/user/getall', function(response){
            if(ApiQuery.isStatusSuccess(response)){
                $scope.users = response.body;
                callback();
            }else{
                Messages.cannotLoadUserList();
            }
        });
    };

    this.getUsers = function(){
        return $scope.users;
    };

    this.getUserById = function(userId){
        let result = null;
        $.each($scope.users, function(k, user){
            if(user.name === userId){
                result = user;
            }
        });
        return result;
    };

    this.fetchAvaiablePermlevels = async function(){
        return new Promise(function(resolve){
            ApiQuery.query({
                url: 'api/auth/get-avaiable-perm-levels',
                type: 'GET'
            }, function(response){
                if(ApiQuery.isStatusSuccess(response)){
                    $scope.permLevels = response.body;
                }else{
                    Messages.cannotLoadAvaiablePermlevels();
                }
                resolve();
            });
        });

    };

    this.getAvaiablePermlevels = async function(){
        if(!$scope.permLevels){
            await $scope.fetchAvaiablePermlevels();
        }
        return $scope.permLevels;
    };

    this.new = function(login, password, perm){
        ApiQuery.simplePost('api/user/create', {
            name: login,
            pass: password,
            perm: perm
        }, function(result){
            Messages.standardInfo(result.body, "Utworzono użytkownika "+login, "Nie można utworzyć użytkownika");
        })
    };

    return this;
};

window.Users = new _Users();
