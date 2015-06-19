
jQuery(document).ready(function($) {

    $( "#crossorder_orig ul, #crossorder_order ul" ).sortable({
        connectWith: ".connectedSortable",
        placeholder: "ui-state-highlight"
    }).disableSelection();

    $( "#crossorder_form_order" ).submit(function() {

        var order = new Array(0);
        $("#crossorder_order ul li").each(function(i,o) {
            var blog_id = $(this).find('.blog_id a');
            var id = $(this).find('.id a');

            if (!blog_id)
                order.push( '1-' + parseInt(id.html()) );
            else
                order.push( parseInt(blog_id.html())+'-'+ parseInt(id.html()) );

            if ( ! $(this).find('.program').hasClass('active') ) {
                $(this).find('.program input[type=text]').remove();
            }
        });

        $('input[name=new_order]',this).val( order.join(',') );
    });

    $( "a.crossorder-remove" ).bind('click',function() {
        if (confirm( 'Remove the filter: '+$(this).attr('slug') ))
            return true;

        return false;
    });

    $('.crosstime').appendDtpicker({
        inline: false,
        dateFormat : 'YYYY/MM/DD hh:mm',
        closeOnSelected: true,
    });

    $('#crossorder .program .show').click(function() {
        var p = $(this).parent();
        p.addClass('active');
        // p.find('.crosstime').appendDtpicker({
        //     inline: false,
        //     dateFormat : 'YYYY/MM/DD hh:mm',
        //     closeOnSelected: true,
        // });
    });

    $('#crossorder .program .close').click(function() {
        var p = $(this).parent();
        p.removeClass('active');
        p.find('input[type=text]').val('');
    });

});

