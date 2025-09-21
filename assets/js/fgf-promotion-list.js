jQuery(document).on('click', '.fgf-promotion-card', function(e) {
    if (!jQuery(e.target).closest('a').length) {
        window.location.href = jQuery(this).data('href');
    }
});
jQuery(function ($) {
    var currentPage = parseInt(fgf_ajax.current) || 1;
    var maxPage     = parseInt(fgf_ajax.maxPage) || 1;
    var loading     = false;

    function loadPage(page) {
        if (loading || page > maxPage || page < 1) return;
        loading = true;

        if (fgf_ajax.mode !== 'pagination') {
            $('#promotion-loader').show();
        }

        $.post(fgf_ajax.ajaxurl, {
            action: 'fgf_promotion_list_pagination',
            nonce: fgf_ajax.nonce,
            page: page,
            per_page: fgf_ajax.per_page
        }, function (response) {
            if (response.success) {
                if (fgf_ajax.mode === 'scroll' || fgf_ajax.mode === 'click') {
                    $('#promotion-list').append(response.data.html);
                } else if (fgf_ajax.mode === 'pagination') {
                    $('#promotion-list').html(response.data.html);
                    $('#promotion-pagination').replaceWith(response.data.pagination);
                }
                currentPage = page;
            }
        }).always(function () {
            loading = false;
            $('#promotion-loader').hide();
        });
    }

    // ===== 无限滚动模式 =====
    if (fgf_ajax.mode === 'scroll') {
        $(window).on('scroll', function () {
            if ($(window).scrollTop() + $(window).height() >= $(document).height() - 200) {
                loadPage(currentPage + 1);
            }
        });
    }

    // ===== 点击加载更多模式 =====
    else if (fgf_ajax.mode === 'click') {
        if (currentPage < maxPage && !$('#promotion-loadmore-container').length) {
            $('#promotion-list').after('<div id="promotion-loadmore-container" style="text-align:center; margin:20px 0;"><button id="promotion-loadmore">加载更多</button></div>');
        }

        $(document).on('click', '#promotion-loadmore', function () {
            loadPage(currentPage + 1);
            if (currentPage + 1 >= maxPage) {
                $('#promotion-loadmore-container').remove();
            }
        });
    }


    // ===== 分页模式 =====
    else if (fgf_ajax.mode === 'pagination') {
        $(document).on('click', '#promotion-pagination a', function (e) {
            e.preventDefault();
            var page = $(this).data('page');
            loadPage(page);
        });
    }
});