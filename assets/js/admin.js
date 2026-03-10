(function ($) {
    function openMedia(callback, title, buttonText, multiple) {
        var frame = wp.media({
            title: title,
            button: { text: buttonText },
            multiple: !!multiple,
        });

        frame.on('select', function () {
            callback(frame.state().get('selection'));
        });

        frame.open();
    }

    $(document).on('click', '.pp-term-image-upload', function (e) {
        e.preventDefault();
        var $wrap = $(this).closest('td, .term-group');

        openMedia(function (selection) {
            var attachment = selection.first().toJSON();
            $wrap.find('#pp_term_image_id').val(attachment.id);
            $wrap.find('.pp-term-image-preview').html('<img src="' + attachment.sizes.thumbnail.url + '" alt="">');
        }, PP_ADMIN.select_image, PP_ADMIN.use_image, false);
    });

    $(document).on('click', '.pp-term-image-remove', function (e) {
        e.preventDefault();
        var $wrap = $(this).closest('td, .term-group-wrap');
        $wrap.find('#pp_term_image_id').val('');
        $wrap.find('.pp-term-image-preview').empty();
    });

    $(document).on('click', '.pp-select-gallery', function (e) {
        e.preventDefault();
        var $field = $('#pp_gallery_ids');
        var $preview = $('.pp-gallery-preview');

        openMedia(function (selection) {
            var ids = [];
            var html = '';
            selection.each(function (attachment) {
                var data = attachment.toJSON();
                ids.push(data.id);
                if (data.sizes && data.sizes.thumbnail) {
                    html += '<img src="' + data.sizes.thumbnail.url + '" alt="">';
                }
            });

            $field.val(ids.join(','));
            $preview.html(html);
        }, PP_ADMIN.select_gallery, PP_ADMIN.use_gallery, true);
    });

    $(document).on('click', '.pp-clear-gallery', function (e) {
        e.preventDefault();
        $('#pp_gallery_ids').val('');
        $('.pp-gallery-preview').empty();
    });
})(jQuery);
