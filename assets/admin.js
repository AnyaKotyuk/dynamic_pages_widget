

var pagesInWidget = 0;
jQuery(document).on('click', '#pages_sidebar .add_page', function () {

    if (typeof php != undefined) {
        pagesInWidget++;
        var currBlock = jQuery(this).closest('.pages');
        var newPage = jQuery(currBlock).find(' > .hidden').clone().removeClass('hidden');
        jQuery(newPage).find('.page-id').attr('name', jQuery(newPage).find('.page-id').attr('name').replace(/\[page_id\]\[0\]/, '[page_id]['+pagesInWidget+']'));
        jQuery(newPage).find('.page-title').attr('name', jQuery(newPage).find('.page-title').attr('name').replace(/\[page_title\]\[0\]/, '[page_title]['+pagesInWidget+']'));
        jQuery(newPage).find('.page-sub').attr('name', jQuery(newPage).find('.page-sub').attr('name').replace(/\[page_sub\]\[0\]/, '[page_sub]['+pagesInWidget+']'));
        jQuery(currBlock).find('.pages-block').append(newPage);
        setSuggest(jQuery(newPage).find('.find'));
        return false;
    }

});


jQuery(document).on('ready widget-updated', function(){
    if (typeof php != 'undefined') {
        setSuggest(jQuery('#pages_sidebar .pages-block .find'));
    }
});

function setSuggest(el) {
    console.log('jQuery(\'#pages_sidebar .pages-block .find\')', jQuery('#pages_sidebar .pages-block .find'))
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
