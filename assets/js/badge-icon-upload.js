jQuery(document).ready(function($){
    var frame;
    $(document).on('click', '.fgf-upload-badge-icon', function(e){
        e.preventDefault();

        // 如果已經存在，就打開
        if (frame) {
            frame.open();
            return;
        }

        // 新建 media frame
        frame = wp.media({
            title: 'Select or Upload Badge Icon',
            button: { text: 'Use this image' },
            multiple: false
        });

        // 當選擇完
        frame.on('select', function(){
            var attachment = frame.state().get('selection').first().toJSON();
            $('.fgf-badge-icon-url').val(attachment.url);
            $('#preview-badge-icon').attr('src', attachment.url);
        });

        frame.open();
    });
});
