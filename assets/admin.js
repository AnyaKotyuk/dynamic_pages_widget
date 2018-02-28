

var pagesInWidget = 0;
jQuery(document).on('click', '.add_page', function () {

    if (typeof php != undefined) {
        pagesInWidget++;
        var currBlock = jQuery(this).closest('.pages');
        var newPage = jQuery(currBlock).find(' > .hidden').clone().removeClass('hidden');
        jQuery(newPage).find('input').each(function(){
            var el = this;
            jQuery(el).attr('name', jQuery(el).attr('name').replace(/\[0\]/, '['+pagesInWidget+']'));
        })

        jQuery(currBlock).find('.pages-block').append(newPage);
        setSuggest(jQuery(newPage).find('.find'));
        return false;
    }

});


jQuery(document).on('ready widget-updated', function(){
    if (typeof php != 'undefined') {
        setSuggest(jQuery('.pages-block .find'));
    }
});

function setSuggest(el) {
    jQuery(el).autocomplete({
        source: php.ajax_url + "?action=get_pages",
        select: function (event, ui) {
            var th = this;
            setTimeout(function () {
                jQuery(th).val(ui.item.label);
                jQuery(th).next().next().val(ui.item.value);
            }, 300)
        }
    });
}
