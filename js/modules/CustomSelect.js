function CustomSelect() {

    let getWrapper = function(){
        return $('<div></div>').addClass('customSelect');
    };

    let generate = function(wrapper){
        let select = wrapper.find('select');
        let label = $('<div></div>').addClass('CS_label').text('Wybierz cos');
        let labC = label.clone();
        $(document.body).append(labC);
        let height = labC.height();
        labC.remove();
        label.css({height: height});
        wrapper.css({height: height});
        let list = $('<ul></ul>');
        $.each(select.find('option'), function(k,v){
            let op = $(v);
            let li = $('<li></li>');
            if(op.attr('selected')){
                label.text(op.text());
            }
            li.click(function(e){
                select.val(op.attr('value'));
                select.change();
                list.removeClass('active');
                e.stopPropagation();
            });
            list.append(li.text(op.text()));
        });
        select.change(function(){
            let val = select.val();
            label.text(select.find('option[value="'+val+'"]').text());
        });
        label.click(function(e){
            e.stopPropagation();
            list.toggleClass('active');
        });
        wrapper.append(label);
        wrapper.append(list);
        $('body').click(function(){
            list.removeClass('active');
        });
    };

    this.wrap = function(element){
        let wrapper = getWrapper();
        element.wrap(wrapper);
        generate(wrapper);
    };
    this.replace = function(element){
        let wrapper = getWrapper();
        wrapper.append(element);
        generate(wrapper);
        return wrapper;
    };

    return this;
}
