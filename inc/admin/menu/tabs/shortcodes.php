<?php

/**
 * Tab - Shortcode
 * 
 * @since 1.0.0
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

if (class_exists('FGF_Shortcode_Tab')) {
	return new FGF_Shortcode_Tab();
}

/**
 * Class.
 * 
 * @since 1.0.0
 */
class FGF_Shortcode_Tab extends FGF_Settings_Page {

	/**
	 * Constructor.
	 * 
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->id = 'shortcodes';
		$this->show_buttons = false;
		$this->label = __('Shortcodes', 'buy-x-get-y-promo');

		parent::__construct();
	}

	/**
	 * Display the short codes details.
	 * 
	 * @since 10.1.0
	 */
	public function output_extra_fields() {
		include_once FGF_ABSPATH . 'inc/admin/menu/views/shortcode/html-shortcodes.php';
	}

	/**
	 * Get the short code tabs.
	 * 
	 * @since 11.4.0
	 * @return array
	 */
	public static function get_shortcode_tabs() {
		/**
		 * This hook is used to alter the short code tabs.
		 * 
		 * @since 11.4.0
		 */
		return apply_filters('fgf_shortcode_tabs', array(
			'common' => __('Common Shortcodes', 'buy-x-get-y-promo'),
			'parameters' => __('Parameters Value', 'buy-x-get-y-promo'),
			'example' => __('Example', 'buy-x-get-y-promo'),
		));
	}

	/**
	 * Get the common short codes.
	 * 
	 * @since 11.4.0
	 * @return array
	 */
	public static function get_common_shortcodes() {
		/**
		 * This hook is used to alter the common short codes.
		 * 
		 * @since 11.4.0
		 */
		return apply_filters('fgf_common_shortcodes', array(
			'[fgf_gift_products]' => array(
				'supported_parameters' => 'type, mode ,per_page',
				'usage' => __('Displays gifts products based on the cart contents', 'buy-x-get-y-promo'),
			),
			'[fgf_cart_eligible_notices]' => array(
				'supported_parameters' => 'No',
				'usage' => __('Displays the cart eligible notices based on cart contents.', 'buy-x-get-y-promo'),
			),
			'[fgf_progress_bar]' => array(
				'supported_parameters' => 'No',
				'usage' => __('Displays the progress bar of gift products count', 'buy-x-get-y-promo'),
			),
			'[fgf_promotion_list mode=\'scroll\' per_page=\'10\' columns=\'4\' ids=\'1,2,3\']' => array(
				'supported_parameters' => 'mode [scroll | click | pagination], per_page,columns,ids',
				'usage' => __('Displays the list of promotions', 'buy-x-get-y-promo'),
			),
		));
	}

	/**
	 * Get the short code parameter value.
	 * 
	 * @since 11.4.0
	 * @return array
	 */
	public static function get_shortcode_parameter_values() {
		/**
		 * This hook is used to alter the short code parameter values.
		 * 
		 * @since 11.4.0
		 */
		return apply_filters('fgf_shortcode_parameter_values', array(
			'type' => 'table, carousel, selectbox',
			'mode' => 'inline, popup',
			'per_page' => 'any number',
		));
	}
}

return new FGF_Shortcodes_Tab();
