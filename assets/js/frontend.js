/* global fgf_frontend_params, lightcase */

jQuery(function ($) {
    'use strict';

    if (typeof fgf_frontend_params === 'undefined') {
        return false;
    }

    var FGF_Frontend = {
        init: function ( ) {
            $(document).on('click', '.fgf_pagination', this.manual_gift_pagination);
            // Block the manual gift products.
            $(document).on('click', '.fgf-add-manual-gift-product', this.block_manual_gift_products);
            // Add a gift product via ajax.
            $(document).on('click', '.fgf-add-manual-gift-product', this.add_gift_product_ajax);
            // Add the gift product via dropdown.
            $(document).on('click', '.fgf-add-gift-product', this.add_manually_gift_product);
            // Show the dropdown options.
            $(document).on('click', '.fgf-dropdown-default-option-wrapper', this.toggle_dropdown_options);
            // Select the dropdown selection option.
            $(document).on('click', '.fgf-dropdown-option-content', this.select_dropdown_option);
            // Hide the drowdown options when click outside the element.
            $(document).on('click', this.hide_dropdown_option);
            // Add the gift product via dropdown.
            $(document).on('change', '.fgf-gift-product-selection', this.handle_automatic_gift_product);
            // Add the gift product via dropdown.
            $(document).on('change', '.fgf-product-variations', this.handle_variation_gift_product);
            //Update the cart when updating shipping.
            $(document.body).on('updated_shipping_method', this.updated_shipping_method);
            // Initialize the third party library after cart updated.
            $(document.body).on('updated_wc_div', this.reinitilaize_require_library);
            // Update gift details in the checkout page after updated checkout.
            $(document.body).on('updated_checkout', this.update_gift_details_in_checkout);
        }, updated_shipping_method: function ( ) {
            console.log('');
            $(document.body).trigger('wc_update_cart');
        }, reinitilaize_require_library: function () {
            $(document.body).trigger('fgf-enhanced-carousel');
        }, update_gift_details_in_checkout: function (e, data) {
            if (data && data.fragments) {
                if (data.fragments.fgf_notices_html) {
                    $('#fgf-checkout-gift-notices-wrapper').replaceWith(data.fragments.fgf_notices_html);
                }

                if (data.fragments.fgf_gift_details_html) {
                    $('#fgf-checkout-gift-details-wrapper').replaceWith(data.fragments.fgf_gift_details_html);
                    FGF_Frontend.reinitilaize_require_library();
                }

                if (data.fragments.fgf_gift_details_html) {
                    $('#fgf-checkout-progress-bar-wrapper').replaceWith(data.fragments.fgf_progress_bar_html);
                }
            }
        }, block_manual_gift_products: function (event) {
            var $this = $(event.currentTarget),
                    wrapper = $this.closest('.fgf_gift_products_wrapper');

            if (isBlockCart() || 'yes' === fgf_frontend_params.ajax_add_to_cart || '2' !== fgf_frontend_params.quantity_field_enabled) {
                return true;
            }

            FGF_Frontend.block(wrapper);
            return true;
        }, add_gift_product_ajax: function (event) {
            if (!isBlockCart() && 'yes' !== fgf_frontend_params.ajax_add_to_cart && '2' === fgf_frontend_params.quantity_field_enabled) {
                return true;
            }

            event.preventDefault( );
            var $this = $(event.currentTarget),
                    rule_id = $($this).data('rule_id'),
                    product_id = $($this).data('product_id'),
                    buy_product_id = $($this).data('buy_product_id'),
                    coupon_id = $($this).data('coupon_id'),
                    quantity = 1,
                    reload = ('yes' !== fgf_frontend_params.ajax_add_to_cart && '1' !== fgf_frontend_params.quantity_field_enabled),
                    qunatity_field = $($this).closest('.fgf-gift-product-add-to-cart-actions').find('.fgf-gift-product-qty');

            if (qunatity_field.length && qunatity_field.val()) {
                quantity = qunatity_field.val();
            }

            FGF_Frontend.add_gift_product($this, product_id, rule_id, buy_product_id, coupon_id, quantity, reload);

        }, add_gift_product: function ($this, product_id, rule_id, buy_product_id, coupon_id, quantity, reload) {
            var content = $this.closest('.fgf-gift-products-content');

            FGF_Frontend.block(content);

            var data = ({
                action: 'fgf_add_gift_product',
                product_id: product_id,
                rule_id: rule_id,
                buy_product_id: buy_product_id,
                coupon_id: coupon_id,
                quantity: quantity,
                fgf_security: fgf_frontend_params.gift_product_nonce,
            });

            $.post(fgf_frontend_params.ajaxurl, data, function (res) {
                if (true === res.success) {
                    if (reload) {
                        reloadWindow(1);
                    } else if ('popup' == $('#fgf_gift_products_type').val()) {
                        if (res.data.reload) {
                            reloadWindow(1);
                        } else {
                            FGF_Frontend.update_gift_products_content( );
                            $(document.body).trigger('fgf-enhanced-carousel');
                        }
                    } else {
                        updateCart(1);
                    }
                } else {
                    alert(res.data.error);
                }

                FGF_Frontend.unblock(content);
            });
        }, update_gift_products_content: function ( ) {
            var data = ({
                action: 'fgf_update_gift_products_content',
                fgf_security: fgf_frontend_params.gift_product_nonce,
            });
            $.ajax({
                type: 'POST',
                url: fgf_frontend_params.ajaxurl,
                data: data,
                async: false,
                dataType: 'html',
                success: function (response) {
                    var html = $.parseHTML(response);
                    $('.fgf-gift-products-content').replaceWith($('.fgf-gift-products-content', html));

                    lightcase.settings.onClose = {
                        foo: function () {
                            window.location.reload();
                        }
                    };
                }
            });
        }, handle_variation_gift_product: function (event) {

            var $this = $(event.currentTarget),
                    product_id = $($this).val( ),
                    url = fgf_frontend_params.add_to_cart_link,
                    link = $($this).closest('span').find('.fgf-add-manual-gift-product');

            // Create a add to cart link.
            url = url.replace('%s', product_id);
            url = url.replace('%s', $($this).data('rule_id'));
            url = url.replace('%s', $($this).data('buy_product_id'));
            url = url.replace('%s', $($this).data('coupon_id'));
            link.attr('href', url);
            link.data('product_id', product_id);
            link.data('rule_id', $($this).data('rule_id'));
            link.data('buy_product_id', $($this).data('buy_product_id'));
            link.data('coupon_id', $($this).data('coupon_id'));

            $($this).closest('.fgf-gift-product-item').find('.fgf-product-image').html($(this).find(':selected').data('image'));

            $(document.body).trigger('fgf_updated_gift_variation_product');
        }, toggle_dropdown_options: function (event) {
            event.preventDefault( );

            $('.fgf-dropdown-options-wrapper').toggle();

        }, select_dropdown_option: function (event) {
            event.preventDefault( );
            var $this = $(event.currentTarget),
                    content = $this.find('.fgf-dropdown-option');

            $('.fgf-dropdown-option-selected').html(content.html());
            $('.fgf-dropdown-options-wrapper').toggle();

            var rule_id = $($this).data('rule-id'),
                    buy_product_id = $($this).data('buy-product-id'),
                    coupon_id = $($this).data('coupon-id');

            // Add a gift automatically.
            if ('2' === fgf_frontend_params.dropdown_add_to_cart_behaviour) {
                FGF_Frontend.block($this.closest('.fgf-dropdown-wrapper'));
                FGF_Frontend.add_gift_product_automatically($this, $($this).val(), rule_id, buy_product_id, coupon_id);
            } else {
                $('.fgf-gift-product-selection')
                        .val($($this).val()).attr('data-rule-id', rule_id)
                        .attr('data-buy-product-id', buy_product_id)
                        .attr('data-coupon-id', coupon_id);
            }

        }, hide_dropdown_option: function (event) {
            var wrapper = $('.fgf-dropdown-wrapper');

            // if the target of the click isn't the container nor a descendant of the container
            if (!wrapper.is(event.target) && wrapper.has(event.target).length === 0) {
                $('.fgf-dropdown-options-wrapper').hide();
            }

        }, add_manually_gift_product: function (event) {
            event.preventDefault( );
            // Return if the autmatic add to cart mode is enabled.
            if ('2' === fgf_frontend_params.dropdown_add_to_cart_behaviour) {
                return false;
            }

            var $this = $(event.currentTarget),
                    wrapper = $this.closest('.fgf-gift-product-wrapper'),
                    product_id = wrapper.find('.fgf-gift-product-selection').val( );

            if ('' === product_id || '0' === product_id) {
                alert(fgf_frontend_params.add_to_cart_alert_message);
                return false;
            }

            if ('2' === fgf_frontend_params.dropdown_display_type) {
                var option_wrapper = wrapper.find('.fgf-gift-product-selection');
            } else {
                var option_wrapper = wrapper.find('.fgf-gift-product-selection').find(':selected');
            }

            var rule_id = option_wrapper.data('rule-id'),
                    buy_product_id = option_wrapper.data('buy-product-id'),
                    coupon_id = option_wrapper.data('coupon-id');

            if ('yes' === fgf_frontend_params.ajax_add_to_cart) {
                FGF_Frontend.add_gift_product($this, product_id, rule_id, buy_product_id, coupon_id, 1, false);
            } else {
                FGF_Frontend.add_gift_product_automatically($this, product_id, rule_id, buy_product_id, coupon_id);
            }

        }, handle_automatic_gift_product: function (event) {
            event.preventDefault( );
            // Return if the automatic add to cart is disbaled. 
            if ('2' !== fgf_frontend_params.dropdown_add_to_cart_behaviour) {
                return false;
            }

            var $this = $(event.currentTarget),
                    rule_id = $($this).find(':selected').data('rule-id'),
                    buy_product_id = $($this).find(':selected').data('buy-product-id'),
                    coupon_id = $($this).find(':selected').data('coupon-id');

            FGF_Frontend.add_gift_product_automatically($this, $($this).val(), rule_id, buy_product_id, coupon_id);

        }, add_gift_product_automatically: function ($this, product_id, rule_id, buy_product_id, coupon_id) {
            // Return if the product ID does not exists. 
            if ('' === product_id) {
                return false;
            }

            if (isBlockCart()) {
                FGF_Frontend.add_gift_product($this, product_id, rule_id, buy_product_id, coupon_id, 1, false);
            } else {
                // Create a add to cart link.
                var url = fgf_frontend_params.add_to_cart_link;
                url = url.replace('%s', product_id);
                url = url.replace('%s', rule_id);
                url = url.replace('%s', buy_product_id);
                url = url.replace('%s', coupon_id);

                // Redirect to add the gift product to the cart.
                window.location.href = url;
            }
        }, manual_gift_pagination: function (event) {
            event.preventDefault( );
            var $this = $(event.currentTarget),
                    table = $this.closest('table.fgf_gift_products_table'),
                    table_body = table.find('tbody'),
                    current_page = $this.data('page');

            FGF_Frontend.block(table_body);

            var data = ({
                action: 'fgf_gift_products_pagination',
                page_number: current_page,
                page_url: fgf_frontend_params.current_page_url,
                fgf_security: fgf_frontend_params.gift_products_pagination_nonce,
            });

            $.post(fgf_frontend_params.ajaxurl, data, function (res) {

                if (true === res.success) {
                    table_body.html(res.data.html);
                    table.find('.fgf_pagination').removeClass('current');
                    table.find('.fgf_pagination_' + current_page).addClass('current');
                    var next_page = current_page;
                    if (current_page > 1) {
                        next_page = current_page - 1;
                    }

                    var last_page = table.find('.fgf_last_pagination').data('page');
                    if (current_page < last_page) {
                        last_page = current_page + 1;
                    }

                    table.find('.fgf_next_pagination').data('page', last_page);
                    table.find('.fgf_prev_pagination').data('page', next_page);

                    $(document.body).trigger('fgf_updated_pagination');

                } else {
                    alert(res.data.error);
                }

                FGF_Frontend.unblock(table_body);
            }
            );
        }, block: function (id) {
            $(id).block({
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.7
                }
            });
        }, unblock: function (id) {
            $(id).unblock( );
        }
    };

    /**
     * Is block cart/checkout.
     * 
     * @since 11.0.0
     * @returns {boolean}
     */
    function isBlockCart() {
        return fgf_frontend_params.is_block_cart || fgf_frontend_params.is_block_checkout;
    }

    /**
     * Reload the window.
     * 
     * @since 11.0.0
     * @returns {undefined}
     */
    function reloadWindow(action) {
        if (isBlockCart()) {
            $(document.body).trigger('fgf_update_cart_block', action);
        } else {
            window.location.reload();
        }
    }

    /**
     * Update the cart after any action done.
     * 
     * @since 11.0.0
     * @returns {undefined}
     */
    function updateCart(action) {
        if (isBlockCart()) {
            $(document.body).trigger('fgf_update_cart_block', action);
        } else {
            $(document.body).trigger('wc_update_cart');
            $(document.body).trigger('update_checkout');
        }
    }

    FGF_Frontend.init( );
});
