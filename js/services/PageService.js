let PageService = {
    name: 'PageService'
};
Class(PageService, function($this, $self){}, function($self){
    $self.templateData = null;

    $self.getTemplateList = function(){
        return new Promise(function(resolve, fail){
            ApiQuery.simpleGet('api/templates/all', resolve);
        });
    };

    $self.requestTemplateData = async function(){
        $self.templateData = {};
        return new Promise(function(success){
            ApiQuery.simpleGet('api/templates/all', function(result){
                $.each(result.body, function(key, value){
                    $self.templateData[value.id] = value.htmlfile;
                });
                success();
            });
        })
    };

    $self.getTemplateName = function(id){
        if($self.templateData === null){
            $self.requestTemplateData();
        }
        return $self.templateData[id];
    }
});

