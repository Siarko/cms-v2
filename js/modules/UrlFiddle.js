let UrlFiddle = {name: 'UrlFiddle'};
Class(UrlFiddle, function(){}, function($self){
    $self.base = document.head.baseURI;

    $self.setBasePage = function(url){
        $self.base += url;
    };

    $self.set = function(name){
        if(!name){name = '';}
        window.history.pushState('','',$self.base+'/'+name);
    };

    $self.isBase = function(){
        return (location.href === $self.base);
    };

    $self.getSubPage = function(){
        return location.href.replace($self.base, '').replace(/^\/|\/$/g, '');
    }
});