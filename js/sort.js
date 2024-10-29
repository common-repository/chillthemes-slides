jQuery(document).ready(function($) {
    var slidesList = $('#chillthemes-slides-list');
    slidesList.sortable({
        update: function( event, ui ) {
            opts = {
                async: true,
                cache: false,
                dataType: 'json',
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'slides_sort',
                    order: slidesList.sortable( 'toArray' ).toString() 
                },
                success: function( response ) {
                    return;
                },
                error: function( xhr, textStatus, e ) {
                    alert( 'The order of the items could not be saved at this time, please try again.' );
                    return;
                }
            };
        $.ajax(opts);
        }
    });
});