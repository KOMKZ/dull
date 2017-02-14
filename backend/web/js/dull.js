window.dull = (function($){
    var dull = function(){

    }
    dull.prototype.new_alert = function (message, alerttype, container) {
        if($.isArray(message)){
            message = message.join("<br/>");
        }
        container = container ? container : '#alert';
        $(container).append('<div id="alertdiv" class="alert alert-' +  alerttype + '"><a class="close" data-dismiss="alert">×</a><span>'+message+'</span></div>')
        setTimeout(function() { // this will automatically close the alert and remove this if the users doesnt close it in 5 secs
            $("#alertdiv").remove();
        }, 10000);
    }
    return new dull();
})(jQuery);
