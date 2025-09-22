<?php

/**
 * Settings Tab.
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

if (class_exists('FGF_Settings_Tab')) {
	return new FGF_Settings_Tab();
}

/**
 * Class.
 */
class FGF_Settings_Tab extends FGF_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id = 'settings';
		$this->label = __('Settings', 'buy-x-get-y-promo');

		//Display the cron information.
		add_action('woocommerce_admin_field_fgf_display_cron_information', array( $this, 'display_cron_information' ));

		parent::__construct();
	}

	/**
	 * Get the sections.
	 *
	 * @return array
	 */
	public function get_sections() {
		$sections = array(
			'general' => __('General', 'buy-x-get-y-promo'),
			'display' => __('Display', 'buy-x-get-y-promo'),
			'notices' => __('Notices', 'buy-x-get-y-promo'),
			'progress_bar' => __('Progress Bar', 'buy-x-get-y-promo'),
			'advanced' => __('Advanced', 'buy-x-get-y-promo'),
			'notifications' => __('Notifications', 'buy-x-get-y-promo'),
			'localizations' => __('Localization', 'buy-x-get-y-promo'),
			'messages' => __('Messages', 'buy-x-get-y-promo'),
		);
		/**
		 * This hook is used to alter the settings sections.
		 *
		 * @since 1.0
		 */
		return apply_filters($this->plugin_slug . '_get_sections_' . $this->id, $sections);
	}

	/**
	 * Get the settings for general section array.
	 *
	 * @return array
	 */
	public function general_section_array() {
		$section_fields = array();

		// General Section Start
		$section_fields[] = array(
			'type' => 'title',
			'title' => __('General Settings', 'buy-x-get-y-promo'),
			'id' => 'fgf_general_options',
		);
		$section_fields[] = array(
			'title' => __('Hide Free Gift Products on Shop and Category Pages', 'buy-x-get-y-promo'),
			'type' => 'checkbox',
			'default' => 'no',
			'desc_tip' => true,
			'desc' => __(' When enabled, the products which are configured to be given as Free Gifts will be hidden in Shop and Category Pages.', 'buy-x-get-y-promo'),
			'id' => $this->get_option_key('restrict_gift_product_display'),
		);
		$section_fields[] = array(
			'title' => __('Rule Status', 'buy-x-get-y-promo'),
			'type' => 'multiselect',
			'class' => 'fgf_select2',
			'default' => array( 'fgf_active', 'fgf_inactive' ),
			'options' => fgf_get_rule_statuses_options(),
			'id' => $this->get_option_key('gift_products_valid_rule_statuses'),
		);
		$section_fields[] = array(
			'title' => __('Maximum Number of Gift Products in "Manual Rule Type" is decided based on', 'buy-x-get-y-promo'),
			'id' => $this->get_option_key('gifts_count_per_order_type'),
			'type' => 'select',
			'default' => '1',
			'options' => array(
				'1' => __('Global Settings', 'buy-x-get-y-promo'),
				'2' => __('Rule Settings', 'buy-x-get-y-promo'),
			),
			'desc_tip' => true,
			'desc' => __('Global Settings - The Maximum Gifts Restriction applies for all rules. Rule Settings - The Maximum Gifts Restriction can be set on each rule.', 'buy-x-get-y-promo'),
		);
		$section_fields[] = array(
			'title' => __('Maximum Gifts in an Order', 'buy-x-get-y-promo'),
			'type' => 'number',
			'default' => '5',
			'custom_attributes' => array(
				'min' => '1',
				'data-error' => __('Please set any value as per your need in the Maximum Gifts in an Order field to proceed furthermore', 'buy-x-get-y-promo'),
			),
			'desc_tip' => true,
			'desc' => __('The Maximum number of gift products which can be chosen for each order.', 'buy-x-get-y-promo'),
			'id' => $this->get_option_key('gifts_count_per_order'),
		);
		$section_fields[] = array(
			'title' => __('Ajax Add to Cart for Manual Gift Products', 'buy-x-get-y-promo'),
			'type' => 'checkbox',
			'default' => 'no',
			'desc_tip' => true,
			'id' => $this->get_option_key('enable_ajax_add_to_cart'),
		);
		$section_fields[] = array(
			'title' => __('Allow Adding Multiple Quantities of Same Gift Product in an Order (Only for Manual Free Gifts)', 'buy-x-get-y-promo'),
			'type' => 'checkbox',
			'default' => 'no',
			'desc_tip' => true,
			'desc' => __('When enabled, a user can add the same product multiple times to the cart. Provided they are eligible to receive multiple gifts for a single purchase.', 'buy-x-get-y-promo'),
			'id' => $this->get_option_key('gifts_selection_per_user'),
		);
		$section_fields[] = array(
			'title' => __('Allow User(s) to remove the automatically added Gift Product(s)', 'buy-x-get-y-promo'),
			'type' => 'checkbox',
			'default' => 'no',
			'desc_tip' => true,
			'desc' => __('When enabled, a user can remove the automatically added gift products.', 'buy-x-get-y-promo'),
			'id' => $this->get_option_key('show_automatic_free_gift_product_remove_link'),
		);

		$section_fields[] = array(
			'title' => __('Allow Shipping Cost for Free Gift', 'buy-x-get-y-promo'),
			'type' => 'checkbox',
			'default' => 'no',
			'desc_tip' => true,
			'desc' => __('When enabled, the shipping cost will be consider for free gifts in the order.', 'buy-x-get-y-promo'),
			'id' => $this->get_option_key('allow_shipping_free_gift'),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id' => 'fgf_general_options',
		);
		// General Section End
		// Restriction Section Start
		$section_fields[] = array(
			'type' => 'title',
			'title' => __('Restriction Settings', 'buy-x-get-y-promo'),
			'id' => 'fgf_restriction_options',
		);
		$section_fields[] = array(
			'title' => __('Restrict Free Gift if WooCommerce Coupon is used', 'buy-x-get-y-promo'),
			'type' => 'checkbox',
			'default' => 'no',
			'desc_tip' => true,
			'desc' => __('When enabled, the user will not be eligible for a free gift if they have used a WooCommerce Coupon in the order.', 'buy-x-get-y-promo'),
			'id' => $this->get_option_key('gift_restriction_based_coupon'),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id' => 'fgf_restriction_options',
		);
		// Restriction section end

		return $section_fields;
	}

	/**
	 * Get the settings for display section array.
	 *
	 * @return array
	 */
	public function display_section_array() {
		wp_enqueue_media(); // 引入 WP 媒體庫腳本
		wp_enqueue_script('fgf-badge-icon-upload', FGF_PLUGIN_URL.'/assets/js/badge-icon-upload.js', array('jquery'), '1.0', true);
		$section_fields = array();

		// General section start.
		$section_fields[] = array(
			'type' => 'title',
			'title' => __('General Settings', 'buy-x-get-y-promo'),
			'id' => 'fgf_general_options',
		);
		$section_fields[] = array(
			'title' => __('Free Gift(s) Display Method in the Cart Items Table', 'buy-x-get-y-promo'),
			'id' => $this->get_option_key('gift_product_cart_display_order'),
			'type' => 'select',
			'default' => '1',
			'options' => array(
				'1' => __('Order in which they get added', 'buy-x-get-y-promo'),
				'2' => __('Grouped & displayed at the Bottom of the Table', 'buy-x-get-y-promo'),
			),
		);
		$section_fields[] = array(
			'title' => __("Gift Product's Original Price display method", 'buy-x-get-y-promo'),
			'id' => $this->get_option_key('gift_product_price_display_type'),
			'type' => 'select',
			'default' => '1',
			'options' => array(
				'1' => __("Don't display price", 'buy-x-get-y-promo'),
				'2' => __('Strike and display the Price', 'buy-x-get-y-promo'),
			),
			'desc' => __('Strike and display the price will work only for shortcode based checkout page. For block based cart and checkout page, the price will display as zero.', 'buy-x-get-y-promo'),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id' => 'fgf_restriction_options',
		);
		// General section end.
		// Promotion page section start.
		$section_fields[] = array(
			'type' => 'title',
			'title' => __('Promotion Page Settings', 'buy-x-get-y-promo'),
			'id' => 'fgf_promotion_page_options',
		);
		$section_fields[] = array(
			'title' => __('Promotion Badge Icon', 'buy-x-get-y-promo'),
			'type'  => 'text', // 用 input 來存 URL
			'id'    => $this->get_option_key('promotion_badge_icon'),
			'class' => 'regular-text fgf-badge-icon-url',
			'desc'  => '<button type="button" class="button fgf-upload-badge-icon">' . __('Upload Badge Icon', 'buy-x-get-y-promo') . '</button><br>' .
					__('Upload an image to be used as the badge icon for free gifts.', 'buy-x-get-y-promo'),
		);
		// Badge 顯示位置
		$section_fields[] = array(
			'title'   => __('Promotion Badge Position', 'buy-x-get-y-promo'),
			'type'    => 'select',
			'id'      => $this->get_option_key('promotion_badge_position'),
			'default' => 'top-left',
			'options' => array(
				'top-left'     => __('Top Left', 'buy-x-get-y-promo'),
				'top-center'   => __('Top Center', 'buy-x-get-y-promo'),
				'top-right'    => __('Top Right', 'buy-x-get-y-promo'),
				'middle-left'  => __('Middle Left', 'buy-x-get-y-promo'),
				'middle-center'=> __('Middle Center', 'buy-x-get-y-promo'),
				'middle-right' => __('Middle Right', 'buy-x-get-y-promo'),
				'bottom-left'  => __('Bottom Left', 'buy-x-get-y-promo'),
				'bottom-center'=> __('Bottom Center', 'buy-x-get-y-promo'),
				'bottom-right' => __('Bottom Right', 'buy-x-get-y-promo'),
			),
			'desc'    => __('Choose where the promotion badge should be displayed on the product image.', 'buy-x-get-y-promo'),
		);
		// Badge 大小
		// Badge 寬度
		$section_fields[] = array(
			'title' => __('Badge Width (px)', 'buy-x-get-y-promo'),
			'type'  => 'number',
			'id'    => $this->get_option_key('promotion_badge_width'),
			'class' => 'small-text',
			'desc'  => __('Set the width of the promotion badge in pixels.', 'buy-x-get-y-promo'),
			'default' => 40,
		);

		$section_fields[] = array(
			'type' => 'sectionend',
			'id' => 'fgf_promotion_page_options',
		);
		// Promotion page section end.
		// Cart page section start.
		$section_fields[] = array(
			'type' => 'title',
			'title' => __('Cart Page Settings', 'buy-x-get-y-promo'),
			'id' => 'fgf_cart_page_options',
		);
		$section_fields[] = array(
			'title' => __('Free Gifts display mode in the Cart Page', 'buy-x-get-y-promo'),
			'id' => $this->get_option_key('gift_cart_page_display'),
			'type' => 'select',
			'default' => '1',
			'options' => array(
				'1' => __('Inline', 'buy-x-get-y-promo'),
				'2' => __('Pop-Up', 'buy-x-get-y-promo'),
			),
			'desc_tip' => true,
			'desc' => __('Select Whether the Gift Products should be displayed Inline or Pop-up', 'buy-x-get-y-promo'),
		);
		$section_fields[] = array(
			'title' => __('Free Gifts display position in the Cart Page', 'buy-x-get-y-promo'),
			'id' => $this->get_option_key('gift_cart_page_display_position'),
			'type' => 'select',
			'default' => '1',
			'options' => array(
				'1' => __('After Cart Table', 'buy-x-get-y-promo'),
				'2' => __('Before Cart Table', 'buy-x-get-y-promo'),
			),
			'desc' => __('Positioning will work only for shortcode based cart page. For positioning in block based cart page, edit the cart page and move the "Free Gifts" block as per your needs.', 'buy-x-get-y-promo'),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id' => 'fgf_cart_page_options',
		);
		// Cart page section end.
		// Checkout page section start.
		$section_fields[] = array(
			'type' => 'title',
			'title' => __('Checkout Page Settings', 'buy-x-get-y-promo'),
			'id' => 'fgf_checkout_page_options',
		);
		$section_fields[] = array(
			'title' => __('Allow Users to Choose Free Gifts in Checkout Page', 'buy-x-get-y-promo'),
			'id' => $this->get_option_key('gift_checkout_page_display'),
			'type' => 'select',
			'default' => '1',
			'options' => array(
				'1' => __('No', 'buy-x-get-y-promo'),
				'2' => __('Yes', 'buy-x-get-y-promo'),
			),
			'desc_tip' => true,
			'desc' => __('Select whether to allow the Free Gifts selection in the checkout page', 'buy-x-get-y-promo'),
		);
		$section_fields[] = array(
			'title' => __('Free Gifts display mode in the Checkout Page', 'buy-x-get-y-promo'),
			'id' => $this->get_option_key('checkout_gift_products_display_type'),
			'type' => 'select',
			'default' => '2',
			'options' => array(
				'1' => __('Inline', 'buy-x-get-y-promo'),
				'2' => __('Pop-Up', 'buy-x-get-y-promo'),
			),
			'class' => 'fgf-gift-products-checkout-field',
			'desc_tip' => true,
			'desc' => __('Select Whether the Gift Products should be displayed Inline or Pop-up', 'buy-x-get-y-promo'),
		);
		$section_fields[] = array(
			'title' => __('Free Gifts display position in the Checkout Page', 'buy-x-get-y-promo'),
			'id' => $this->get_option_key('checkout_gift_products_hook_name'),
			'type' => 'select',
			'default' => '1',
			'class' => 'fgf-gift-products-checkout-field fgf-gift-products-checkout-display-type-field',
			'options' => array(
				'1' => __('Before Checkout Form', 'buy-x-get-y-promo'),
				'2' => __('After Checkout Billing Form', 'buy-x-get-y-promo'),
				'3' => __('Custom Hook', 'buy-x-get-y-promo'),
			),
			'desc' => __('Positioning will work only for shortcode based checkout page. For positioning in block based checkout page, edit the checkout page and move the "Free Gifts" block as per your needs.', 'buy-x-get-y-promo'),
		);
		$section_fields[] = array(
			'title' => __('Custom Hook Name', 'buy-x-get-y-promo'),
			'type' => 'text',
			'default' => '',
			'id' => $this->get_option_key('checkout_gift_products_custom_hook_name'),
			'class' => 'fgf-gift-products-checkout-field fgf-gift-products-checkout-display-type-field fgf-gift-products-checkout-display-hook-field',
		);
		$section_fields[] = array(
			'title' => __('Custom Hook Priority', 'buy-x-get-y-promo'),
			'type' => 'number',
			'default' => '10',
			'id' => $this->get_option_key('checkout_gift_products_custom_hook_priority'),
			'class' => 'fgf-gift-products-checkout-field fgf-gift-products-checkout-display-type-field fgf-gift-products-checkout-display-hook-field',
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id' => 'fgf_checkout_page_options',
		);
		// Checkout page section end.
		// Gift display section start.
		$section_fields[] = array(
			'type' => 'title',
			'title' => __('Gift Products Display Settings', 'buy-x-get-y-promo'),
			'id' => 'fgf_gift_display_options',
		);
		$section_fields[] = array(
			'title' => __('Gift Products Display Type', 'buy-x-get-y-promo'),
			'id' => $this->get_option_key('gift_display_type'),
			'type' => 'select',
			'default' => '1',
			'options' => array(
				'1' => __('Table', 'buy-x-get-y-promo'),
				'2' => __('Carousel', 'buy-x-get-y-promo'),
				'3' => __('Select Box(Dropdown)', 'buy-x-get-y-promo'),
			),
			'desc_tip' => true,
			'desc' => __('Select whether the Gift Products should be displayed in a Table or Carousel or Select Box', 'buy-x-get-y-promo'),
		);
		$section_fields[] = array(
			'title' => __('Pagination Display', 'buy-x-get-y-promo'),
			'id' => $this->get_option_key('gift_display_table_pagination'),
			'class' => 'fgf_gift_table_display_type',
			'type' => 'select',
			'default' => '1',
			'options' => array(
				'1' => __('Show', 'buy-x-get-y-promo'),
				'2' => __('Hide', 'buy-x-get-y-promo'),
			),
			'desc_tip' => true,
			'desc' => __('Select whether to Display/Hide the Pagination', 'buy-x-get-y-promo'),
		);
		$section_fields[] = array(
			'title' => __('Pagination to Display Gift Products', 'buy-x-get-y-promo'),
			'type' => 'number',
			'default' => '4',
			'class' => 'fgf_gift_table_display_type',
			'custom_attributes' => array( 'min' => '1' ),
			'id' => $this->get_option_key('free_gift_per_page_column_count'),
		);
		$section_fields[] = array(
			'title' => __('Gift Products Per Page - Desktop', 'buy-x-get-y-promo'),
			'type' => 'number',
			'default' => '3',
			'class' => 'fgf_gift_carousel_display_type',
			'custom_attributes' => array(
				'min' => '1',
				'data-error' => __(' Displaying more than 3 products per page in a Carousel can cause Display related issues. Do you want to save anyway?', 'buy-x-get-y-promo'),
			),
			'id' => $this->get_option_key('carousel_gift_per_page'),
		);
		$section_fields[] = array(
			'title' => __('Gift Products Per Page - Tablet', 'buy-x-get-y-promo'),
			'type' => 'number',
			'default' => '2',
			'class' => 'fgf_gift_carousel_display_type',
			'custom_attributes' => array(
				'min' => '1',
			),
			'id' => $this->get_option_key('carousel_gift_per_page_tablet'),
		);
		$section_fields[] = array(
			'title' => __('Gift Products Per Page - Mobile', 'buy-x-get-y-promo'),
			'type' => 'number',
			'default' => '1',
			'class' => 'fgf_gift_carousel_display_type',
			'custom_attributes' => array(
				'min' => '1',
			),
			'id' => $this->get_option_key('carousel_gift_per_page_mobile'),
		);
		$section_fields[] = array(
			'title' => __('Space Between Products in Carousel in Pixels', 'buy-x-get-y-promo'),
			'type' => 'number',
			'default' => '10',
			'class' => 'fgf_gift_carousel_display_type',
			'custom_attributes' => array( 'min' => '1' ),
			'id' => $this->get_option_key('carousel_item_margin'),
		);
		$section_fields[] = array(
			'title' => __('Number of Products to Slide During Navigation', 'buy-x-get-y-promo'),
			'type' => 'number',
			'default' => '1',
			'class' => 'fgf_gift_carousel_display_type',
			'custom_attributes' => array( 'min' => '1' ),
			'id' => $this->get_option_key('carousel_item_per_slide'),
		);
		$section_fields[] = array(
			'title' => __('Display Pagination', 'buy-x-get-y-promo'),
			'type' => 'checkbox',
			'default' => 'yes',
			'class' => 'fgf_gift_carousel_display_type',
			'id' => $this->get_option_key('carousel_pagination'),
		);
		$section_fields[] = array(
			'title' => __('Display Controls', 'buy-x-get-y-promo'),
			'type' => 'checkbox',
			'default' => 'yes',
			'class' => 'fgf_gift_carousel_display_type',
			'id' => $this->get_option_key('carousel_navigation'),
		);
		$section_fields[] = array(
			'title' => __('Navigation Previous Text', 'buy-x-get-y-promo'),
			'type' => 'text',
			'default' => '<',
			'class' => 'fgf_gift_carousel_display_type fgf_carousel_navigation_type',
			'id' => $this->get_option_key('carousel_navigation_prevoius_text'),
		);
		$section_fields[] = array(
			'title' => __('Navigation Next Text', 'buy-x-get-y-promo'),
			'type' => 'text',
			'default' => '>',
			'class' => 'fgf_gift_carousel_display_type fgf_carousel_navigation_type',
			'id' => $this->get_option_key('carousel_navigation_next_text'),
		);
		$section_fields[] = array(
			'title' => __('Auto Play', 'buy-x-get-y-promo'),
			'type' => 'checkbox',
			'default' => 'yes',
			'class' => 'fgf_gift_carousel_display_type',
			'id' => $this->get_option_key('carousel_auto_play'),
		);
		$section_fields[] = array(
			'title' => __('Slide Speed in Milliseconds', 'buy-x-get-y-promo'),
			'type' => 'number',
			'default' => '5000',
			'class' => 'fgf_gift_carousel_display_type fgf_carousel_auto_play',
			'custom_attributes' => array( 'min' => '1' ),
			'id' => $this->get_option_key('carousel_slide_speed'),
		);
		$section_fields[] = array(
			'title' => __('Gift Product Name Display Method', 'buy-x-get-y-promo'),
			'id' => $this->get_option_key('gift_display_product_linkable'),
			'class' => 'fgf_gift_table_display_type fgf_gift_carousel_display_type',
			'type' => 'select',
			'default' => '1',
			'options' => array(
				'1' => __('Only Product Name', 'buy-x-get-y-promo'),
				'2' => __('Product Name with Hyperlink to Product Page', 'buy-x-get-y-promo'),
			),
			'desc_tip' => true,
			'desc' => __('Select whether to Display the Gift Product Name as Name Only/Name with Hyperlink to Product Page', 'buy-x-get-y-promo'),
		);
		$section_fields[] = array(
			'title' => __('Gift product display when choosing select-box', 'buy-x-get-y-promo'),
			'id' => $this->get_option_key('gift_dropdown_display_type'),
			'class' => 'fgf_gift_dropdown_display_type',
			'type' => 'select',
			'default' => '1',
			'options' => array(
				'1' => __('Display product name only', 'buy-x-get-y-promo'),
				'2' => __('Display product name and image', 'buy-x-get-y-promo'),
			),
			'desc_tip' => true,
			'desc' => __('Select whether the Gift Products should be displayed with the product name only or product name and image in the select-box', 'buy-x-get-y-promo'),
		);
		$section_fields[] = array(
			'title' => __('Gift Product Add to Cart Method', 'buy-x-get-y-promo'),
			'id' => $this->get_option_key('dropdown_add_to_cart_behaviour'),
			'class' => 'fgf_gift_dropdown_display_type',
			'type' => 'select',
			'default' => '1',
			'options' => array(
				'1' => __('Manual', 'buy-x-get-y-promo'),
				'2' => __('Automatic', 'buy-x-get-y-promo'),
			),
			'desc_tip' => true,
			'desc' => __('Automatic: Gift Product will be added to cart automatically once the Gift Product is selected in the dropdown(select box). Manually: The Addd to Cart Button has to be clicked for adding the product to the cart.', 'buy-x-get-y-promo'),
		);
		$section_fields[] = array(
			'title' => __('Allow Quantity selection for Gift Product(s)', 'buy-x-get-y-promo'),
			'id' => $this->get_option_key('gift_product_quantity_field_enabled'),
			'class' => 'fgf_gift_table_display_type fgf_gift_carousel_display_type',
			'type' => 'select',
			'default' => '2',
			'options' => array(
				'1' => __('Yes', 'buy-x-get-y-promo'),
				'2' => __('No', 'buy-x-get-y-promo'),
			),
			'desc_tip' => true,
			'desc' => __('Allow Quantity selection for the Manually selecting Gift Product(s)', 'buy-x-get-y-promo'),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id' => 'fgf_gift_display_options',
		);
		// Gift display section end.

		return $section_fields;
	}

	/**
	 * Get the settings for notices section array.
	 *
	 * @since 11.4.0
	 * @return array
	 */
	public function notices_section_array() {
		$section_fields = array();
		// Notices general section start.
		$section_fields[] = array(
			'type' => 'title',
			'title' => __('Notice Display Settings', 'buy-x-get-y-promo'),
			'id' => 'fgf_notices_general_options',
		);
		$section_fields[] = array(
			'title' => __('Free Gifts Notice Display Type', 'buy-x-get-y-promo'),
			'id' => $this->get_option_key('display_notice_mode'),
			'type' => 'select',
			'default' => '1',
			'options' => array(
				'1' => __('Standard WooCommerce Notice', 'buy-x-get-y-promo'),
				'2' => __("Plugin's Own Notice", 'buy-x-get-y-promo'),
			),
			'desc_tip' => true,
			'desc' => __("By default, Free Gift messages will be displayed in WooCommerce Notices. You can switch to Plugin's Own Notice if your theme doesn't support WooCommerce Notices.", 'buy-x-get-y-promo'),
		);
		$section_fields[] = array(
			'title' => __('Display Free Gifts Notice in Checkout Page', 'buy-x-get-y-promo'),
			'type' => 'checkbox',
			'default' => 'no',
			'id' => $this->get_option_key('enable_checkout_free_gift_notice'),
			'desc' => __('When enabled, a notice will be displayed in checkout page asking the users to choose their free gifts. This notice will hidden if the user has already chosen their free gifts on cart page.', 'buy-x-get-y-promo'),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id' => 'fgf_notices_general_options',
		);
		// Notices general section end.
		// Eligible notices section start.
		$section_fields[] = array(
			'type' => 'title',
			'title' => __('Eligible Notices Settings', 'buy-x-get-y-promo'),
			'id' => 'fgf_eligible_notices_options',
		);
		$section_fields[] = array(
			'title' => __('Free Gift(s) Eligibility Notice Will be Displayed On', 'buy-x-get-y-promo'),
			'id' => $this->get_option_key('display_cart_notices_type'),
			'type' => 'select',
			'default' => '1',
			'options' => array(
				'1' => __('Cart & Checkout', 'buy-x-get-y-promo'),
				'2' => __('Cart', 'buy-x-get-y-promo'),
				'3' => __('Checkout', 'buy-x-get-y-promo'),
			),
			'desc_tip' => true,
			'desc' => __('Choose where you want to display the Free Gift(s) eligibility notice.', 'buy-x-get-y-promo'),
		);
		$section_fields[] = array(
			'title' => __('Free Gift(s) Eligibility Notices Display Type', 'buy-x-get-y-promo'),
			'id' => $this->get_option_key('display_eligibility_notices_type'),
			'type' => 'select',
			'default' => '1',
			'options' => array(
				'1' => __('Multiple Notices Display', 'buy-x-get-y-promo'),
				'2' => __('Single Notice Display', 'buy-x-get-y-promo'),
			),
			'desc_tip' => true,
			'desc' => __('By default, All the Free Gift(s) Eligibility Notices will display. You can switch to Single Notice Display to show the First Match rule.', 'buy-x-get-y-promo'),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id' => 'fgf_eligible_notices_options',
		);
		// Eligible notices section end.

		return $section_fields;
	}

	/**
	 * Get the settings for progress bar section array.
	 *
	 * @since 11.4.0
	 * @return array
	 */
	public function progress_bar_section_array() {
		$section_fields = array();

		// Progess bar section start.
		$section_fields[] = array(
			'type' => 'title',
			'title' => __('Gift Product selection Progress Bar Display Settings', 'buy-x-get-y-promo'),
			'id' => 'fgf_progress_bar_options',
		);
		$section_fields[] = array(
			'title' => __('Display Gift Product selection Progress Bar in the Cart', 'buy-x-get-y-promo'),
			'type' => 'checkbox',
			'default' => 'no',
			'id' => $this->get_option_key('cart_page_progress_bar_enabled'),
			'desc' => __('Positioning will work only for block based cart page. For positioning in block based cart page, edit the cart page and move the "Progress bar" block as per your needs.', 'buy-x-get-y-promo'),
		);
		$section_fields[] = array(
			'title' => __('Display Gift Product selection Progress Bar in the Checkout', 'buy-x-get-y-promo'),
			'type' => 'checkbox',
			'default' => 'no',
			'id' => $this->get_option_key('checkout_page_progress_bar_enabled'),
			'desc' => __('Positioning will work only for block based checkout page. For positioning in block based checkout page, edit the checkout page and move the "Progress bar" block as per your needs.', 'buy-x-get-y-promo'),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id' => 'fgf_progress_bar_options',
		);
		// Progess bar section end.

		return $section_fields;
	}

	/**
	 * Get the settings for advanced section array.
	 *
	 * @return array
	 */
	public function advanced_section_array() {
		$section_fields = array();

		// Trobuleshoot section start.
		$section_fields[] = array(
			'type' => 'title',
			'title' => __('Troubleshoot', 'buy-x-get-y-promo'),
			'id' => 'fgf_trobuleshoot_options',
		);
		$section_fields[] = array(
			'title' => __('Free Gift Product Price', 'buy-x-get-y-promo'),
			'type' => 'fgf_custom_fields',
			'fgf_field' => 'price',
			'default' => '0',
			'desc_tip' => true,
			'desc' => __('The value configured here will be considered as price for the Gift Products and used for Legal as well as Payment Gateway validation.', 'buy-x-get-y-promo'),
			'id' => $this->get_option_key('gift_product_price'),
		);
		$section_fields[] = array(
			'title' => __('Frontend Scripts Enqueued on', 'buy-x-get-y-promo'),
			'id' => $this->get_option_key('frontend_enqueue_scripts_type'),
			'type' => 'select',
			'default' => '1',
			'options' => array(
				'1' => __('Header', 'buy-x-get-y-promo'),
				'2' => __('Footer', 'buy-x-get-y-promo'),
			),
			'desc_tip' => true,
			'desc' => __('Choose whether the frontend scripts has to be loaded on Header/Footer', 'buy-x-get-y-promo'),
		);
		$section_fields[] = array(
			'title' => __('Restrict Redirection when Free Gifts are Added', 'buy-x-get-y-promo'),
			'id' => $this->get_option_key('restrict_redirection_after_gifts_added'),
			'type' => 'select',
			'default' => '1',
			'options' => array(
				'1' => __('No', 'buy-x-get-y-promo'),
				'2' => __('Yes', 'buy-x-get-y-promo'),
			),
			'desc_tip' => true,
			'desc' => __('If you are facing any issues (display issues in mini cart, sliders, incorrect URL redirection) when free gifts are added, then select "Yses" option and check.', 'buy-x-get-y-promo'),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id' => 'fgf_trobuleshoot_options',
		);
		// Custom CSS section end.
		// Delete data section start.
		$section_fields[] = array(
			'type' => 'title',
			'title' => __('Deletion Settings', 'buy-x-get-y-promo'),
			'id' => 'fgf_delete_data_options',
		);
		$section_fields[] = array(
			'title' => __('Delete Master log(s) after X Duration', 'buy-x-get-y-promo'),
			'id' => $this->get_option_key('master_log_deletion'),
			'type' => 'select',
			'default' => '2',
			'options' => array(
				'1' => __('Yes', 'buy-x-get-y-promo'),
				'2' => __('No', 'buy-x-get-y-promo'),
			),
			'desc_tip' => true,
			'desc' => __('If "Yes" is selected, Master log entries will be removed after a specific duration.', 'buy-x-get-y-promo'),
		);
		$section_fields[] = array(
			'title' => __('Delete Master log(s) after', 'buy-x-get-y-promo'),
			'id' => $this->get_option_key('master_log_deletion_duration'),
			'type' => 'fgf_custom_fields',
			'fgf_field' => 'relative_date_selector',
			'default' => array( 'number' => 1, 'unit' => 'years' ),
			'periods' => array(
				'days' => __('Day(s)', 'buy-x-get-y-promo'),
				'weeks' => __('Week(s)', 'buy-x-get-y-promo'),
				'months' => __('Month(s)', 'buy-x-get-y-promo'),
				'years' => __('Year(s)', 'buy-x-get-y-promo'),
			),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id' => 'fgf_delete_data_options',
		);
		// Delete data section end.
		// Cron section start.
		$section_fields[] = array(
			'type' => 'title',
			'title' => __('Cron Information', 'buy-x-get-y-promo'),
			'id' => 'fgf_cron_options',
		);
		$section_fields[] = array(
			'type' => 'fgf_display_cron_information',
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id' => 'fgf_cron_options',
		);
		// Cron section end.
		// Custom CSS section start.
		$section_fields[] = array(
			'type' => 'title',
			'title' => __('Custom CSS', 'buy-x-get-y-promo'),
			'id' => 'fgf_custom_css_options',
		);
		$section_fields[] = array(
			'title' => __('Custom CSS', 'buy-x-get-y-promo'),
			'type' => 'textarea',
			'default' => '',
			'custom_attributes' => array( 'rows' => 10 ),
			'id' => $this->get_option_key('custom_css'),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id' => 'fgf_custom_css_options',
		);
		// Custom CSS section end.

		return $section_fields;
	}

	/**
	 * Get the settings for notifications section array.
	 *
	 * @return array
	 */
	public function notifications_section_array() {
		$section_fields = array();

		// Email settings section start
		$section_fields[] = array(
			'type' => 'title',
			'title' => __('Email Settings', 'buy-x-get-y-promo'),
			'id' => 'fgf_email_options',
		);
		$section_fields[] = array(
			'title' => __('Email Type', 'buy-x-get-y-promo'),
			'id' => $this->get_option_key('email_template_type'),
			'type' => 'select',
			'default' => '1',
			'options' => array(
				'1' => __('HTML', 'buy-x-get-y-promo'),
				'2' => __('WooComerce Template', 'buy-x-get-y-promo'),
			),
		);
		$section_fields[] = array(
			'title' => __('From Name', 'buy-x-get-y-promo'),
			'id' => $this->get_option_key('email_from_name'),
			'type' => 'text',
			'default' => get_option('woocommerce_email_from_name'),
		);
		$section_fields[] = array(
			'title' => __('From Address', 'buy-x-get-y-promo'),
			'id' => $this->get_option_key('email_from_address'),
			'type' => 'text',
			'default' => get_option('woocommerce_email_from_address'),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id' => 'fgf_email_options',
		);
		// Email settings section end
		// Manual Gift Email section start
		$section_fields[] = array(
			'type' => 'title',
			'title' => __('Manual Gift Email', 'buy-x-get-y-promo'),
			'id' => 'fgf_manual_gift_email_options',
		);
		$section_fields[] = array(
			'title' => __('Enable/Disable', 'buy-x-get-y-promo'),
			'id' => $this->get_option_key('enable_manual_gift_email'),
			'type' => 'checkbox',
			'default' => 'yes',
		);
		$section_fields[] = array(
			'title' => __('Subject', 'buy-x-get-y-promo'),
			'id' => $this->get_option_key('manual_gift_email_subject'),
			'type' => 'text',
			'class' => 'fgf_manual_gift_email',
			'default' => '{site_name}  - Free Gift Received',
			'desc' => __('<b>Supported Shortcodes:</b></br>{site_name} - To show the site name', 'buy-x-get-y-promo'),
		);
		$section_fields[] = array(
			'title' => __('Message', 'buy-x-get-y-promo'),
			'id' => $this->get_option_key('manual_gift_email_message'),
			'type' => 'fgf_custom_fields',
			'fgf_field' => 'wpeditor',
			'class' => 'fgf_manual_gift_email',
			'default' => 'Hi {user_name},

You have received the following Product(s) as a Gift from the Site Admin.

{free_gifts}

Thanks',
			'desc' => __('<b>Supported Shortcodes:</b></br>{user_name} - To show the username</br>{free_gifts} - To show the free gifts details</br>{order_id} - To show the order ID', 'buy-x-get-y-promo'),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id' => 'fgf_manual_gift_email_options',
		);
		// Manual Gift Email section end

		return $section_fields;
	}

	/**
	 * Get the settings for localizations section array.
	 *
	 * @return array
	 */
	public function localizations_section_array() {
		$section_fields = array();

		// Gifts localizations section start.
		$section_fields[] = array(
			'type' => 'title',
			'title' => __('Gift Product Selection Localization', 'buy-x-get-y-promo'),
			'id' => 'fgf_gifts_localizations_options',
		);
		$section_fields[] = array(
			'title' => __('Gift Product Selection Section Heading', 'buy-x-get-y-promo'),
			'type' => 'text',
			'default' => 'Choose Your Gift(s)',
			'id' => $this->get_option_key('free_gift_heading_label'),
		);
		$section_fields[] = array(
			'title' => __('Gift Product Add to Cart Button Label', 'buy-x-get-y-promo'),
			'type' => 'text',
			'default' => 'Add to Cart',
			'id' => $this->get_option_key('free_gift_add_to_cart_button_label'),
		);
		$section_fields[] = array(
			'title' => __('Gift Product Selection Label - Select Box(Dropdown)', 'buy-x-get-y-promo'),
			'type' => 'text',
			'default' => 'Please select a Gift',
			'id' => $this->get_option_key('free_gift_dropdown_default_option_label'),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id' => 'fgf_gifts_localizations_options',
		);
		// Gifts localizations section end.
		// Cart page localizations section start.
		$section_fields[] = array(
			'type' => 'title',
			'title' => __('Cart Page Localization', 'buy-x-get-y-promo'),
			'id' => 'fgf_cart_page_localizations_options',
		);
		$section_fields[] = array(
			'title' => __('Cart Gift Type Label', 'buy-x-get-y-promo'),
			'type' => 'text',
			'default' => 'Type',
			'id' => $this->get_option_key('free_gift_cart_item_type_localization'),
		);
		$section_fields[] = array(
			'title' => __('Cart Free Gift Label', 'buy-x-get-y-promo'),
			'type' => 'text',
			'default' => 'Free Product',
			'id' => $this->get_option_key('free_gift_cart_item_type_value_localization'),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id' => 'fgf_cart_page_localizations_options',
		);
		// Cart page localizations section end.
		// Progress bar localizations section start.
		$section_fields[] = array(
			'type' => 'title',
			'title' => __('Progress Bar Localization', 'buy-x-get-y-promo'),
			'id' => 'fgf_progress_bar_localizations_options',
		);
		$section_fields[] = array(
			'title' => __("Progress Bar's Heading", 'buy-x-get-y-promo'),
			'type' => 'text',
			'default' => 'Gift Product Selection Info',
			'id' => $this->get_option_key('progress_bar_heading_label'),
		);
		$section_fields[] = array(
			'title' => __('Maximum Gift Count Label', 'buy-x-get-y-promo'),
			'type' => 'text',
			'default' => 'Maximum Gift Count: [maximum_gift_count]',
			'id' => $this->get_option_key('progress_bar_maximum_gift_count_label'),
			'desc' => __('<b>Supported Shortcodes:</b></br>[maximum_gift_count] - To show the Maximum gift count', 'buy-x-get-y-promo'),
		);
		$section_fields[] = array(
			'title' => __('Added Gift Count Label', 'buy-x-get-y-promo'),
			'type' => 'text',
			'default' => 'Added Gift Count: [added_gift_count]',
			'id' => $this->get_option_key('progress_bar_added_gift_count_label'),
			'desc' => __('<b>Supported Shortcodes:</b></br>[added_gift_count] - To show the Added gift count', 'buy-x-get-y-promo'),
		);
		$section_fields[] = array(
			'title' => __('Remaining Gift Count Label', 'buy-x-get-y-promo'),
			'type' => 'text',
			'default' => 'Remaining Gift Count: [remaining_gift_count]',
			'id' => $this->get_option_key('progress_bar_remaining_gift_count_label'),
			'desc' => __('<b>Supported Shortcodes:</b></br>[remaining_gift_count] - To show the remaining gift count', 'buy-x-get-y-promo'),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id' => 'fgf_progress_bar_localizations_options',
		);
		// Progress bar localizations section end.
		return $section_fields;
	}

	/**
	 * Get the settings for messages section array.
	 *
	 * @return array
	 */
	public function messages_section_array() {
		$section_fields = array();

		// Cart and checkout page messages section start
		$section_fields[] = array(
			'type' => 'title',
			'title' => __('Cart & Checkout Page Messages', 'buy-x-get-y-promo'),
			'id' => 'fgf_cart_checkout_page_messages_options',
		);
		$section_fields[] = array(
			'title' => __('Free Gift Notice in Cart/Checkout - Inline Mode', 'buy-x-get-y-promo'),
			'type' => 'textarea',
			'default' => 'Based on your Current Cart Contents, you are eligible for Free Gift(s). Choose your gift from the Table below.',
			'id' => $this->get_option_key('free_gift_notice_message'),
			'desc' => __('<b>Supported Shortcodes:</b></br>[remaining_gift_count] - To show the remaining gift count that need to be manually added by the customer', 'buy-x-get-y-promo'),
		);
		$section_fields[] = array(
			'title' => __('Free Gift Notice in Cart/Checkout - Pop-up Mode', 'buy-x-get-y-promo'),
			'type' => 'textarea',
			'default' => 'Based on your Current Cart Contents, you are eligible for Free Gift(s). Choose your gift [popup_link].',
			'id' => $this->get_option_key('free_gift_popup_notice_message'),
			'desc' => __('<b>Supported Shortcodes:</b></br>[remaining_gift_count] - To show the remaining gift count that need to be manually added by the customer', 'buy-x-get-y-promo'),
		);
		$section_fields[] = array(
			'title' => __('Popup Link Shortcode Label', 'buy-x-get-y-promo'),
			'type' => 'text',
			'default' => 'Here',
			'id' => $this->get_option_key('free_gift_popup_link_message'),
		);
		$section_fields[] = array(
			'title' => __('Free Gift Checkout Page Notice', 'buy-x-get-y-promo'),
			'type' => 'textarea',
			'class' => 'fgf_checkout_free_gift_notice',
			'default' => 'Based on your Current Cart Contents, you are eligible for Free Gift(s). Choose your gift on [cart_page].',
			'id' => $this->get_option_key('checkout_free_gift_notice_message'),
			'desc' => __('<b>Supported Shortcodes:</b></br>[cart_page] - To show the cart page link as a button', 'buy-x-get-y-promo'),
		);
		$section_fields[] = array(
			'title' => __('Cart Link Shortcode Label', 'buy-x-get-y-promo'),
			'type' => 'text',
			'default' => 'Cart Page',
			'class' => 'fgf_checkout_free_gift_notice',
			'id' => $this->get_option_key('checkout_free_gift_notice_shortcode_message'),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id' => 'fgf_cart_checkout_page_messages_options',
		);
		// Cart and checkout page messages section end.
		// Shortcode page messages section start
		$section_fields[] = array(
			'type' => 'title',
			'title' => __('Shortcode Messages', 'buy-x-get-y-promo'),
			'id' => 'fgf_shortcode_messages_options',
		);
		$section_fields[] = array(
			'title' => __("Message - If the criteria don't match to Gift the Product / the cart is empty", 'buy-x-get-y-promo'),
			'type' => 'textarea',
			'default' => 'As of now no Gift Product(s) available based on your Cart Content',
			'id' => $this->get_option_key('shortcode_free_gift_empty_message'),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id' => 'fgf_shortcode_messages_options',
		);
		// Shortcode messages section end.
		// Success messages section start.
		$section_fields[] = array(
			'type' => 'title',
			'title' => __('Success Messages', 'buy-x-get-y-promo'),
			'id' => 'fgf_success_messages_options',
		);
		$section_fields[] = array(
			'title' => __('Manual Gift', 'buy-x-get-y-promo'),
			'type' => 'textarea',
			'default' => 'Gift product added successfully',
			'id' => $this->get_option_key('free_gift_success_message'),
		);
		$section_fields[] = array(
			'title' => __('Automatic Gift', 'buy-x-get-y-promo'),
			'type' => 'textarea',
			'default' => 'Gift product(s) has been added to your cart based on your cart contents.',
			'id' => $this->get_option_key('free_gift_automatic_success_message'),
		);
		$section_fields[] = array(
			'title' => __('Buy X Get Y - Automatic', 'buy-x-get-y-promo'),
			'type' => 'textarea',
			'default' => 'Gift product(s) has been added to your cart based on your cart contents.',
			'id' => $this->get_option_key('free_gift_bogo_success_message'),
		);
		$section_fields[] = array(
			'title' => __('Coupon based Gift - Automatic', 'buy-x-get-y-promo'),
			'type' => 'textarea',
			'default' => 'Gift product(s) has been added to your cart based on your cart contents.',
			'id' => $this->get_option_key('free_gift_coupon_success_message'),
		);
		$section_fields[] = array(
			'title' => __('Subtotal-based Gift - Automatic', 'buy-x-get-y-promo'),
			'type' => 'textarea',
			'default' => 'Gift product(s) has been added to your cart based on your cart contents',
			'id' => $this->get_option_key('subtotal_based_free_gifts_success_message'),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id' => 'fgf_success_messages_options',
		);
		// Success messages section end.
		// Error messages section start.
		$section_fields[] = array(
			'type' => 'title',
			'title' => __('Error Messages', 'buy-x-get-y-promo'),
			'id' => 'fgf_error_messages_options',
		);
		$section_fields[] = array(
			'title' => __('Gift Product not selected in the Select Box(Dropdown) Display', 'buy-x-get-y-promo'),
			'type' => 'textarea',
			'default' => 'Please select a Gift',
			'id' => $this->get_option_key('gift_product_dropdown_valid_message'),
		);
		$section_fields[] = array(
			'title' => __("Gift Product(s) removed if the criteria don't matched", 'buy-x-get-y-promo'),
			'type' => 'textarea',
			'default' => 'Your Free Gift(s) were removed because your current cart contents is not eligible for a free gift',
			'id' => $this->get_option_key('free_gift_error_message'),
		);
		$section_fields[] = array(
			'title' => __('Coupon based Gift limitation', 'buy-x-get-y-promo'),
			'type' => 'textarea',
			'default' => 'The Maximum Gift Limit has been reached and the coupon cannot be applied',
			'id' => $this->get_option_key('free_gift_coupon_restriction_error_message'),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id' => 'fgf_error_messages_options',
		);
		// Error messages section end.

		return $section_fields;
	}

	/**
	 * Display the server cron information.
	 * */
	public function display_cron_information() {
		$master_log_deletion_date = get_option('fgf_master_log_deletion_last_updated_date');

		$server_cron_info = array(
			'master_log_deletion' => array(
				'cron' => __('Master Log Deletion Cron', 'buy-x-get-y-promo'),
				'last_updated_date' => self::format_last_updated_date($master_log_deletion_date),
			),
		);

		include_once FGF_ABSPATH . 'inc/admin/menu/views/html-cron-info.php';
	}

	/**
	 * Format the last update date.
	 *
	 * @return string.
	 * */
	public function format_last_updated_date( $date ) {
		if (empty($date)) {
			return __('Cron not Triggered', 'buy-x-get-y-promo');
		}

		return FGF_Date_Time::get_wp_format_datetime_from_gmt($date, false, ' ', true);
	}
}

return new FGF_Settings_Tab();
