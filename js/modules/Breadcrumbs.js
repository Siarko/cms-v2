window.Breadcrumbs = function(){

    let steps = [];
    let onback = function(viewName){};
    let onadd = function(viewName){};

    this.onBack = function(callback){
        onback = callback;
    };

    this.onAdd = function(callback){
        onadd = callback;
    };

    this.go = function(id){
        steps.splice(id+1);
        onadd(steps[id].data);
    };

    this.add = function(desc, data){
        steps.push({label: desc, data: data});
        onadd(data);
    };

    this.back = function( trigger = true){
        if(steps.length > 1){
            steps.pop();
            if(trigger){
                onback(steps[steps.length-1].data);
            }
        }
    };

    this.construct = function(){
        return steps;
    }
};
