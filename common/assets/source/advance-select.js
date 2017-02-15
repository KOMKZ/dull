(function($){
    var core_options = {
        pk : 'id',
        container : undefined,
        on_submit : undefined,
    }
    function AddSelect(custom_options){
        this.config = merge(core_options, custom_options);
        this.selections = [];
        this.selected = [];
        var init_seleted = null;
        this.set_init_selected = function(value){init_seleted = value.slice();};
        this.get_init_selected = function(value){return init_seleted;}
        this.init();
        this._bind();

    }
    AddSelect.prototype.init = function(){
        var $this = this;
        var container_class = this.config.container ? this.config.container + ' ' : '';
        $(container_class + '.ad-select-item').each(function(){
            $this.selections.push($(this).attr($this.get_pk_attr()));
        });
        $(container_class + '.ad-selected-item').each(function(){
            $this.selected.push($(this).attr($this.get_pk_attr()));
        });
        $this.set_init_selected($this.selected);
    }
    AddSelect.prototype.get_pk_attr = function(){
        return 'data-' + this.config.pk;
    }
    AddSelect.prototype.add = function(pk_values){
        if(pk_values.length > 0){
            var pk_attr = this.get_pk_attr();
            var selected_container = $('#ad-selected-item-container');
            var item_class = '';
            var new_item = null;
            for(i in pk_values){
                item_class = '.ad-select-item['+ pk_attr +'=\''+ pk_values[i] +'\']';
                new_item = $(item_class).clone();
                new_item.find('input').prop('checked', false);
                new_item.removeClass('ad-select-item').addClass('ad-selected-item');
                selected_container.append(new_item);
                this.selected.push(pk_values[i]);
            }
        }
    }
    AddSelect.prototype.remove = function(pk_values){
        var pk_attr = this.get_pk_attr();
        var selector = '';
        if(this.selected.length > 0){
            var result = -1;
            for(i in pk_values){
                result = this.selected.indexOf(pk_values[i])
                if(result >= 0){
                    // update item selected array
                    this.selected.splice(result, 1);
                    // remove item html
                    selector = '.ad-selected-item[' + pk_attr + '=\'' + pk_values[i] + '\']';
                    $(selector).remove();
                }
            }
        }
    }
    function get_remove_items(item_class, pk_attr){
        var items = [];
        $(item_class).each(function(){
            if( $(this).find('input:checked').length > 0){
                items.push($(this).attr(pk_attr));
            }
        });
        return items;
    }
    function get_add_items(item_class, pk_attr){
        var items = [];
        $(item_class).each(function(){
            if( $(this).find('input:checked').length > 0){
                items.push($(this).attr(pk_attr));
            }
        });
        return items;
    }
    AddSelect.prototype._bind = function(){
        // todo
        var rm_id = '#ad-select-rm-btn';
        var add_id = '#ad-select-add-btn';
        var save_id = '#ad-select-save-btn';
        var pk_attr = this.get_pk_attr()
        var $this = this;
        $(rm_id).click(function(){
            var rm_items = get_remove_items('.ad-selected-item', pk_attr);
            $this.remove(rm_items);
        });
        $(add_id).click(function(){
            var add_items = get_add_items('.ad-select-item', pk_attr);
            var real_add_items = [];
            for(i in add_items){
                if(-1 === $this.selected.indexOf(add_items[i])){
                    real_add_items.push(add_items[i]);
                }
            }
            $this.add(real_add_items);
        });
        $(save_id).click(function(){
            if('function' === typeof $this.config.on_submit){
                var new_items = $this.get_new_items();
                var rm_items = $this.get_rm_items();
                if((new_items.length + rm_items.length) > 0){
                    $this.config.on_submit(
                        new_items,
                        rm_items,
                        $this
                    );
                }
            }
        });
        $('#ad-select-check-all').change(function(){
            $('.ad-select-item').find('input[type=checkbox]').prop('checked', $(this).prop('checked'));
        });
        $('#ad-selected-check-all').change(function(){
            $('.ad-selected-item').find('input[type=checkbox]').prop('checked', $(this).prop('checked'));
        });
    }
    AddSelect.prototype.get_new_items = function(){
        var new_items = [];
        var init_items = this.get_init_selected();
        for(i in this.selected){
            if(-1 === init_items.indexOf(this.selected[i])){
                new_items.push(this.selected[i]);
            }
        }
        return new_items;
    }
    AddSelect.prototype.get_rm_items = function(){
        var rm_items = [];
        var init_items = this.get_init_selected();
        for(i in init_items){
            if(-1 === this.selected.indexOf(init_items[i])){
                rm_items.push(init_items[i]);
            }
        }
        return rm_items;
    }

    function merge(target, source) {
        if ( typeof target !== 'object' ) {
            target = {};
        }
        for (var property in source) {
            if ( source.hasOwnProperty(property) ) {
                var sourceProperty = source[ property ];
                if ( typeof sourceProperty === 'object' ) {
                    target[ property ] = merge( target[ property ], sourceProperty );
                    continue;
                }
                target[ property ] = sourceProperty;
            }
        }
        for (var a = 2, l = arguments.length; a < l; a++) {
            merge(target, arguments[a]);
        }
        return target;
    };
    this.AddSelect = AddSelect;
})(jQuery);
