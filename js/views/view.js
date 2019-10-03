View = {name: 'View'}; //tylko do autopodpowiedzi
Class(View, function ($this, $self) {
    $this.children = [];

    $this.render = function () {
        console.log("Native view render");
        return null;
    };

    $this.addChild = function (child) {
        $this.children.push(child);
    };

    //propagateUp - true - propagates view update up to parents
    $this.update = function () {
        $this.render();
        /*$.each($this.children, function(k,v){
            v.update();
        });*/
    };
}, function ($self) {
    $self.register = function (views, callback) {
        callback = callback || function(){};
        let list = [];
        $.each(views, function (k, v) {
            let path = "views/" + v;
            let className = v.split('/').pop();
            let objectName = className.charAt(0).toLowerCase() + className.slice(1);

            list.push({
                url: path,
                onload: function () {
                    $self[objectName] = new window[className];
                    callback();
                }
            });
        });

        require(list);
    }
});