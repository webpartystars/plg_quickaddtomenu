var quickaddmenu = {
    submit : function (e) {
        e.preventDefault();
        var form = jQuery('#adminForm');
        Joomla.loadingLayer('show');
        jQuery.ajax({
            type: "POST",
            url: Joomla.getOptions('system.paths').base+"/index.php?option=com_ajax&plugin=quickaddtomenu&format=json&group=system",
            dataType: "json",
            async : true,
            data: form.serialize()
        })
            .done(function (response) {

                if (response.message)
                {
                    if(response.success){
                        Joomla.renderMessages({'success' : [response.message]});
                    }
                    else {
                        Joomla.renderMessages({'error' : [response.message]});
                    }
                }

                if(response.messages){
                    Joomla.renderMessages(response.messages);
                }
            })
            .error(function (xhr, textStatus, error){
                Joomla.ajaxErrorsMessages(xhr, textStatus, error);
            });
        quickaddmenu.close(true);
        Joomla.loadingLayer('hide');
    },
    close : function (clear = true){
        if(clear){
            quickaddmenu.clear();
        }
        jQuery('#quickaddmenupopup').modal('hide');
    },
    clear : function () {
        document.getElementById('quickaddmenu-language-id').value='';
        document.getElementById('quickaddmenu-access').value='';
        document.getElementById('quickaddmenu-menu-id').value='';
    }
};
