<?php

/**
 * Manual Gift Tab
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

if (class_exists('FGF_Manual_Gift_Tab')) {
	return new FGF_Manual_Gift_Tab();
}

/**
 * FGF_Manual_Gift_Tab.
 */
class FGF_Manual_Gift_Tab extends FGF_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id = 'manual_gift';
		$this->show_buttons = false;
		$this->label = __('Manual Gift', 'buy-x-get-y-promo');

		parent::__construct();
	}

	/**
	 * Get Manual Gift setting section array.
	 */
	public function manual_gift_section_array() {
		$section_fields = array();

		// Manul Gift section start
		$section_fields[] = array(
			'type' => 'title',
			'title' => __('Manual Gift Settings', 'buy-x-get-y-promo'),
			'desc' => __(
					'You can manually send free gifts to any user on the site. 
To send a free gift, select a user, product(s) and click the Send Gift button. An order will be created on behalf of the user.
', 'buy-x-get-y-promo'
			),
			'id' => 'fgf_manual_gift_options',
				);
		$section_fields[] = array(
			'title' => __('Select a user', 'buy-x-get-y-promo'),
			'id' => $this->get_option_key('selected_user'),
			'action' => 'fgf_json_search_customers',
			'type' => 'fgf_custom_fields',
			'list_type' => 'customers',
			'fgf_field' => 'ajaxmultiselect',
			'placeholder' => __('Select a user', 'buy-x-get-y-promo'),
			'multiple' => false,
			'allow_clear' => false,
				);
		$section_fields[] = array(
			'title' => __('Product Selection', 'buy-x-get-y-promo'),
			'id' => $this->get_option_key('selected_products'),
			'action' => 'fgf_json_search_products_and_variations',
			'type' => 'fgf_custom_fields',
			'exclude_global_variable' => 'yes',
			'display_stock' => 'yes',
			'list_type' => 'products',
			'fgf_field' => 'ajaxmultiselect',
			'desc_tip' => true,
			'desc' => __('You can also choose multiple products', 'buy-x-get-y-promo'),
			'placeholder' => __('Select a Product', 'buy-x-get-y-promo'),
			'allow_clear' => false,
				);
		$section_fields[] = array(
			'title' => __('Order Status', 'buy-x-get-y-promo'),
			'type' => 'select',
			'default' => 'completed',
			'options' => fgf_get_paid_order_statuses(),
			'desc_tip' => true,
			'desc' => __('This option controls the order status of the Manual order', 'buy-x-get-y-promo'),
			'id' => $this->get_option_key('order_status'),
				);
		$section_fields[] = array(
			'default' => __('Send Gift', 'buy-x-get-y-promo'),
			'id' => $this->get_option_key('manual_gift_btn'),
			'class' => 'button-primary',
			'type' => 'fgf_custom_fields',
			'fgf_field' => 'button',
				);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id' => 'fgf_manual_gift_options',
				);
		// Manul Gift section end

		return $section_fields;
	}
}

return new FGF_Manual_Gift_Tab();
