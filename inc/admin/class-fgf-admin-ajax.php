<?php

/**
 * Admin Ajax.
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
if (!class_exists('FGF_Admin_Ajax')) {

	/**
	 * FGF_Admin_Ajax Class.
	 */
	class FGF_Admin_Ajax {

		/**
		 * Class initialization.
		 */
		public static function init() {

			$actions = array(
				'json_search_products_and_variations' => false,
				'json_search_products' => false,
				'json_search_customers' => false,
				'json_search_coupons' => false,
				'create_gift_order' => false,
				'master_log_info_popup' => false,
				'add_order_item_gifts' => false,
				'gift_products_pagination' => true,
				'drag_rules_list' => false,
				'reset_rule_usage_count' => false,
				'add_gift_product' => true,
				'update_gift_products_content' => true,
				'promotion_list_pagination' => true,
			);

			foreach ($actions as $action => $nopriv) {
				add_action('wp_ajax_fgf_' . $action, array( __CLASS__, $action ));

				if ($nopriv) {
					add_action('wp_ajax_nopriv_fgf_' . $action, array( __CLASS__, $action ));
				}
			}
		}

		public static function promotion_list_pagination() {
			// 驗證安全性
			check_ajax_referer('fgf_promotion_list_nonce', 'nonce');

			global $wpdb;
			$rules_post_type = FGF_Register_Post_Types::RULES_POSTTYPE;
			$statuses        = implode("','", fgf_get_rule_statuses());

			$paged    = isset($_POST['page']) ? max(1, intval($_POST['page'])) : 1;
			$per_page = isset($_POST['per_page']) ? max(1, intval($_POST['per_page'])) : 3;
			$offset   = ($paged - 1) * $per_page;

			// 查詢數據
			$rules = $wpdb->get_results($wpdb->prepare("
				SELECT ID, post_title 
				FROM $wpdb->posts 
				WHERE post_type = %s 
				AND post_status IN ('$statuses')
				ORDER BY menu_order ASC, ID ASC
				LIMIT %d OFFSET %d
			", $rules_post_type, $per_page, $offset));

			// 總數據量
			$total_rules = $wpdb->get_var("
				SELECT COUNT(*) 
				FROM $wpdb->posts 
				WHERE post_type = '$rules_post_type' 
				AND post_status IN ('$statuses')
			");
			$total_pages = ceil($total_rules / $per_page);

			if ($rules) {
				// 渲染卡片 HTML
				$html = FGF_Shortcodes::render_promotion_cards($rules);

				// 生成分頁 HTML
				$pagination = '<div id="promotion-pagination" class="fgf-pagination">';
				for ($i = 1; $i <= $total_pages; $i++) {
					if ($i == $paged) {
						$pagination .= "<span class='current-page'>{$i}</span> ";
					} else {
						$pagination .= "<a href='#' data-page='{$i}'>{$i}</a> ";
					}
				}
				
				$pagination .= '</div>';

				wp_send_json_success([
					'html'       => $html,
					'page'       => $paged,
					'pagination' => $pagination,
				]);
			}

			wp_die();
		}




		/**
		 * Search for products.
		 */
		public static function json_search_products( $term = '', $include_variations = false ) {
			check_ajax_referer('search-products', 'fgf_security');

			try {

				if (empty($term) && isset($_GET['term'])) {
					$term = isset($_GET['term']) ? wc_clean(wp_unslash($_GET['term'])) : '';
				}

				if (empty($term)) {
					throw new exception(__('No Products found', 'buy-x-get-y-promo'));
				}

				if (!empty($_GET['limit'])) {
					$limit = absint($_GET['limit']);
				} else {
					/**
					 * This hook is used to alter the WooCommerce JSON search limit.
					 * 
					 * @since 1.0
					 */
					$limit = absint(apply_filters('woocommerce_json_search_limit', 30));
				}

				$data_store = WC_Data_Store::load('product');
				$ids = $data_store->search_products($term, '', (bool) $include_variations, false, $limit);

				$product_objects = fgf_filter_readable_products($ids);
				$products = array();

				$display_stock = isset($_GET['display_stock']) ? wc_clean(wp_unslash($_GET['display_stock'])) : 'no';
				$exclude_global_variable = isset($_GET['exclude_global_variable']) ? wc_clean(wp_unslash($_GET['exclude_global_variable'])) : 'no';
				foreach ($product_objects as $product_object) {
					if ('yes' == $exclude_global_variable && $product_object->is_type('variable')) {
						continue;
					}

					$formatted_name = $product_object->get_formatted_name();
					if ('yes' === $display_stock && $product_object->managing_stock()) {
						/* Translators: %d stock amount */
						$formatted_name .= ' &ndash; ' . sprintf(__('Stock: %d', 'buy-x-get-y-promo'), wc_format_stock_quantity_for_display($product_object->get_stock_quantity(), $product_object));
					}

					$products[$product_object->get_id()] = rawurldecode(wp_strip_all_tags($formatted_name));
				}

				/**
				 * This hook is used to alter the WooCommerce JSON search founded products.
				 * 
				 * @since 1.0
				 */
				wp_send_json(apply_filters('woocommerce_json_search_found_products', $products));
			} catch (Exception $ex) {
				wp_die();
			}
		}

		/**
		 * Search for product variations.
		 */
		public static function json_search_products_and_variations( $term = '', $include_variations = false ) {
			self::json_search_products('', true);
		}

		/**
		 * Customers search.
		 */
		public static function json_search_customers() {
			check_ajax_referer('fgf-search-nonce', 'fgf_security');

			try {
				$term = isset($_GET['term']) ? wc_clean(wp_unslash($_GET['term'])) : ''; // @codingStandardsIgnoreLine.

				if (empty($term)) {
					throw new exception(__('No Customer found', 'buy-x-get-y-promo'));
				}

				$exclude = isset($_GET['exclude']) ? wc_clean(wp_unslash($_GET['exclude'])) : ''; // @codingStandardsIgnoreLine.
				$exclude = !empty($exclude) ? array_map('intval', explode(',', $exclude)) : array();

				$found_customers = array();
				$customers_query = new WP_User_Query(
						array(
					'fields' => 'all',
					'orderby' => 'display_name',
					'search' => '*' . $term . '*',
					'search_columns' => array( 'ID', 'user_login', 'user_email', 'user_nicename' ),
						)
				);
				$customers = $customers_query->get_results();

				if (fgf_check_is_array($customers)) {
					foreach ($customers as $customer) {
						if (!in_array($customer->ID, $exclude)) {
							$found_customers[$customer->ID] = $customer->display_name . ' (#' . $customer->ID . ' &ndash; ' . sanitize_email($customer->user_email) . ')';
						}
					}
				}

				wp_send_json($found_customers);
			} catch (Exception $ex) {
				wp_die();
			}
		}

		/**
		 * Coupon search.
		 */
		public static function json_search_coupons() {
			check_ajax_referer('fgf-search-nonce', 'fgf_security');

			try {
				$term = isset($_GET['term']) ? wc_clean(wp_unslash($_GET['term'])) : ''; // @codingStandardsIgnoreLine.

				if (empty($term)) {
					throw new exception(__('No Coupon found', 'buy-x-get-y-promo'));
				}

				global $wpdb;
				$like = '%' . $wpdb->esc_like($term) . '%';

				$search_results = array_filter($wpdb->get_results($wpdb->prepare("SELECT DISTINCT ID as id, post_title as name FROM {$wpdb->posts}
			WHERE post_type='shop_coupon' AND post_status IN('publish')
                        AND (post_title LIKE %s) ORDER BY post_title ASC", $like), ARRAY_A));

				$found_coupons = array();

				if (fgf_check_is_array($search_results)) {
					foreach ($search_results as $search_result) {
						$found_coupons[$search_result['id']] = $search_result['name'] . ' (#' . $search_result['id'] . ')';
					}
				}

				wp_send_json($found_coupons);
			} catch (Exception $ex) {
				wp_die();
			}
		}

		/**
		 * Create order for selected user with gift products.
		 */
		public static function create_gift_order() {
			check_ajax_referer('fgf-manual-gift-nonce', 'fgf_security');

			try {
				if (!isset($_POST)) {
					throw new exception(__('Invalid Request', 'buy-x-get-y-promo'));
				}

				if (!isset($_POST['user']) || empty(absint($_POST['user']))) { // @codingStandardsIgnoreLine.
					throw new exception(__('Please select a User', 'buy-x-get-y-promo'));
				}

				if (!isset($_POST['products']) || empty(wc_clean(wp_unslash(( $_POST['products'] ))))) { // @codingStandardsIgnoreLine.
					throw new exception(__('Please select atleast one Product', 'buy-x-get-y-promo'));
				}

				// Sanitize post values
				$user_id = !empty($_POST['user']) ? absint($_POST['user']) : 0; // @codingStandardsIgnoreLine.
				$products = !empty($_POST['products']) ? wc_clean(wp_unslash(( $_POST['products'] ))) : array(); // @codingStandardsIgnoreLine.
				$order_status = !empty($_POST['status']) ? wc_clean(wp_unslash(( $_POST['status'] ))) : ''; // @codingStandardsIgnoreLine.
				// Create order for selected user with gift products
				$order_id = FGF_Manual_Gift_Order_Handler::create_free_gift_order($user_id, $products, $order_status);

				$msg = __('Free Gift has been sent successfully', 'buy-x-get-y-promo');

				wp_send_json_success(array( 'msg' => $msg ));
			} catch (Exception $ex) {
				wp_send_json_error(array( 'error' => $ex->getMessage() ));
			}
		}

		/**
		 * Add the order item gifts.
		 * 
		 * @since 10.0.0
		 */
		public static function add_order_item_gifts() {
			check_ajax_referer('fgf-manual-gift-nonce', 'fgf_security');

			try {
				if (!isset($_POST['data']) || !isset($_POST['order_id'])) {
					throw new exception(__('Invalid Request', 'buy-x-get-y-promo'));
				}

				$order = wc_get_order(wc_clean(wp_unslash($_POST['order_id'])));
				if (!is_object($order)) {
					throw new exception(__('Invalid Request', 'buy-x-get-y-promo'));
				}

				$selected_product_details = wc_clean(wp_unslash($_POST['data']));
				if (!fgf_check_is_array($selected_product_details)) {
					throw new exception(__('Please select at least one product to add as Gift item', 'buy-x-get-y-promo'));
				}

				if (!$order->is_editable()) {
					throw new exception(__('You cannot add the products because this order is no longer editable', 'buy-x-get-y-promo'));
				}

				$product_details = array();
				$added_items = array();
				foreach ($selected_product_details as $selected_product_detail) {
					$product = wc_get_product($selected_product_detail['id']);
					if (!is_object($product)) {
						continue;
					}

					$item_id = $order->add_product($product, $selected_product_detail['qty'], array( 'total' => 0, 'subtotal' => 0, 'order' => $order ));
					if (!$item_id) {
						continue;
					}

					$added_items[$item_id] = $product->get_formatted_name();

					$product_details[] = array(
						'product_id' => $selected_product_detail['id'],
						'product_name' => $product->get_name(),
						'product_price' => $product->get_price(),
						'quantity' => $selected_product_detail['qty'],
						'rule_id' => '',
						'mode' => 'admin',
					);

					// Add gifts details in the order item.
					wc_add_order_item_meta($item_id, '_fgf_gift_product', 'yes');
					wc_add_order_item_meta($item_id, '_fgf_gift_rule_id', 'manual');
					wc_add_order_item_meta($item_id, __('Type', 'buy-x-get-y-promo'), __('Free Product', 'buy-x-get-y-promo'));
				}

				if (!fgf_check_is_array($added_items)) {
					throw new exception(__('Please select the valid product(s) to proceed', 'buy-x-get-y-promo'));
				}

				if ('auto-draft' !== $order->get_status()) {
					// May be create/update the master log details.
					$master_log_id = self::maybe_update_master_log($order, $product_details);

					// Save the master log ID in the order.
					$order->add_meta_data('fgf_manual_gift_product', $master_log_id);
					$order->save();
				} else {
					// Which is used to create a master log after the order published.
					set_transient('fgf_gifts_added_manually_for_' . $order->get_id(), 'yes', 86400);
				}

				/* translators: %s item name. */
				$order->add_order_note(sprintf(__('Added Gift line items: %s', 'woocommerce'), implode(', ', $added_items)), false, true);

				wp_send_json_success(array( 'msg' => __('The Gift products were added successfully', 'buy-x-get-y-promo') ));
			} catch (Exception $ex) {
				wp_send_json_error(array( 'error' => $ex->getMessage() ));
			}
		}

		/**
		 * May be update/create the master log details for the order.
		 * 
		 * @since 10.0.0
		 * @param object $order
		 * @param array $product_details
		 * @return int
		 */
		private static function maybe_update_master_log( $order, $product_details ) {
			$master_log_id = fgf_get_master_log_id_by_order_id($order->get_id(), 'fgf_manual');
			if ($master_log_id) {
				$master_log = fgf_get_master_log($master_log_id);
				$product_details = array_filter(array_merge((array) $master_log->get_product_details(), $product_details));
			}

			$meta_data = array(
				'fgf_product_details' => $product_details,
				'fgf_rule_ids' => '',
				'fgf_user_name' => $order->get_formatted_billing_full_name(),
				'fgf_user_email' => $order->get_billing_email(),
				'fgf_order_id' => $order->get_id(),
			);

			if ($master_log_id) {
				fgf_update_master_log($master_log_id,
						$meta_data, array(
					'post_parent' => $order->get_customer_id(),
					'post_status' => 'fgf_manual',
						)
				);
			} else {
				$master_log_id = fgf_create_new_master_log(
						$meta_data, array(
					'post_parent' => $order->get_customer_id(),
					'post_status' => 'fgf_manual',
						)
				);
			}

			return $master_log_id;
		}

		/**
		 * Display Gift Products based on pagination.
		 */
		public static function gift_products_pagination() {
			check_ajax_referer('fgf-gift-products-pagination', 'fgf_security');

			try {
				if (!isset($_POST) || !isset($_POST['page_number'])) { // @codingStandardsIgnoreLine.
					throw new exception(__('Invalid Request', 'buy-x-get-y-promo'));
				}

				// Sanitize post values
				$current_page = !empty($_POST['page_number']) ? absint($_POST['page_number']) : 0; // @codingStandardsIgnoreLine.
				$page_url = !empty($_POST['page_url']) ? wc_clean(wp_unslash($_POST['page_url'])) : ''; // @codingStandardsIgnoreLine.

				$per_page = fgf_get_free_gifts_per_page_column_count();
				$offset = ( $current_page - 1 ) * $per_page;

				// Get gift products based on per page count
				$gift_products = FGF_Rule_Handler::get_overall_manual_gift_products();
				$gift_products = array_slice($gift_products, $offset, $per_page);

				// Get gift products table body content
				$html = fgf_get_template_html(
						'gift-products.php', array(
					'gift_products' => $gift_products,
					'permalink' => esc_url($page_url),
						)
				);

				wp_send_json_success(array( 'html' => $html ));
			} catch (Exception $ex) {
				wp_send_json_error(array( 'error' => $ex->getMessage() ));
			}
		}

		/**
		 * Display master log gift products information as Popup.
		 */
		public static function master_log_info_popup() {
			check_ajax_referer('fgf-master-log-info-nonce', 'fgf_security');

			try {
				if (!isset($_POST) || !isset($_POST['master_log_id'])) { // @codingStandardsIgnoreLine.
					throw new exception(__('Invalid Request', 'buy-x-get-y-promo'));
				}

				// Sanitize post values
				$master_log_id = !empty($_POST['master_log_id']) ? absint($_POST['master_log_id']) : 0; // @codingStandardsIgnoreLine.

				$master_log_object = fgf_get_master_log($master_log_id);

				// Get master log popup content
				ob_start();
				include_once 'menu/views/master-log-popup.php';
				$popup = ob_get_clean();

				wp_send_json_success(array( 'popup' => $popup ));
			} catch (Exception $ex) {
				wp_send_json_error(array( 'error' => $ex->getMessage() ));
			}
		}

		/**
		 * Drag Rules.
		 */
		public static function drag_rules_list() {
			check_ajax_referer('fgf-rules-drag-nonce', 'fgf_security');

			try {
				if (!isset($_POST) || !isset($_POST['sort_order'])) { // @codingStandardsIgnoreLine.
					throw new exception(__('Invalid Request', 'buy-x-get-y-promo'));
				}

				$sort_ids = array();
				// Sanitize post values
				$post_sort_order_ids = !empty($_POST['sort_order']) ? wc_clean(wp_unslash(( $_POST['sort_order'] ))) : array(); // @codingStandardsIgnoreLine.
				// prepare sort order post ids
				foreach ($post_sort_order_ids as $key => $post_id) {
					$sort_ids[$key + 1] = str_replace('post-', '', $post_id);
				}

				// update sort order post ids
				foreach ($sort_ids as $menu_order => $post_id) {
					wp_update_post(
							array(
								'ID' => $post_id,
								'menu_order' => $menu_order,
							)
					);
				}

				wp_send_json_success();
			} catch (Exception $ex) {
				wp_send_json_error(array( 'error' => $ex->getMessage() ));
			}
		}

		/**
		 * Reset rule usage count.
		 */
		public static function reset_rule_usage_count() {
			check_ajax_referer('fgf-rules-nonce', 'fgf_security');

			try {
				if (!isset($_POST) || !isset($_POST['rule_id'])) { // @codingStandardsIgnoreLine.
					throw new exception(__('Invalid Request', 'buy-x-get-y-promo'));
				}

				// Sanitize post values
				$rule_id = absint($_POST['rule_id']); // @codingStandardsIgnoreLine.
				// Reset rule usage count
				update_post_meta($rule_id, 'fgf_rule_usage_count', 0);

				wp_send_json_success(array( 'msg' => __('Order usage count reset successfully', 'buy-x-get-y-promo') ));
			} catch (Exception $ex) {
				wp_send_json_error(array( 'error' => $ex->getMessage() ));
			}
		}

		/**
		 * Add a manual gift product to the cart.
		 */
		public static function add_gift_product() {
			check_ajax_referer('fgf-gift-product', 'fgf_security');

			try {
				if (!isset($_POST)) {
					throw new exception(__('Cannot process action', 'buy-x-get-y-promo'));
				}

				// Sanitize post values
				$product_id = !empty($_POST['product_id']) ? absint($_POST['product_id']) : 0;
				$rule_id = !empty($_POST['rule_id']) ? absint($_POST['rule_id']) : 0;
				$buy_product_id = !empty($_POST['buy_product_id']) ? absint($_POST['buy_product_id']) : 0;
				$coupon_id = !empty($_POST['coupon_id']) ? absint($_POST['coupon_id']) : 0;
				$quantity = !empty($_POST['quantity']) ? absint($_POST['quantity']) : 1;
				$quantity = empty($quantity) ? 1 : $quantity;

				if (empty($product_id) || empty($rule_id)) {
					throw new exception(__('Cannot process action', 'buy-x-get-y-promo'));
				}

				// Return if cart object is not initialized.
				if (!is_object(WC()->cart)) {
					throw new exception(__('Cannot process action', 'buy-x-get-y-promo'));
				}

				// return if cart is empty
				if (WC()->cart->get_cart_contents_count() == 0) {
					throw new exception(__('Cannot process action', 'buy-x-get-y-promo'));
				}

				$rule = fgf_get_rule($rule_id);
				$product = wc_get_product($product_id);

				// return if product id is not proper product
				if (!$product) {
					throw new exception(__('Cannot process action', 'buy-x-get-y-promo'));
				}

				// return if rule id is not proper rule
				if (!$rule->exists()) {
					throw new exception(__('Cannot process action', 'buy-x-get-y-promo'));
				}

				// Return if the rule is not valid.
				if (!fgf_rule_product_exists($rule, $product_id, $buy_product_id, $coupon_id)) {
					throw new exception(__('Cannot process action', 'buy-x-get-y-promo'));
				}

				// Restrict adding the gift product if the per order count exists.
				if (!FGF_Rule_Handler::get_remaining_gift_products_count()) {
					throw new exception(__('Cannot process action', 'buy-x-get-y-promo'));
				}

				$gift_products = FGF_Rule_Handler::get_overall_manual_gift_products();
				if (!fgf_check_is_array($gift_products)) {
					throw new exception(__('Cannot process action', 'buy-x-get-y-promo'));
				}

				$cart_item_data = array(
					'fgf_gift_product' => array(
						'mode' => $rule->get_rule_mode(),
						'rule_id' => $rule_id,
						'product_id' => $product_id,
						'buy_product_id' => $buy_product_id,
						'coupon_id' => $coupon_id,
						/**
						 * This hook is used to alter the manual gift product price.
						 * 
						 * @since 1.0
						 */
						'price' => apply_filters('fgf_manual_gift_product_price', fgf_get_free_gift_product_price(), $rule_id, $product_id),
					),
				);

				// Add a gift product in the cart.
				WC()->cart->add_to_cart($product_id, $quantity, 0, array(), $cart_item_data);

				// Success Notice.
				fgf_add_wc_notice(get_option('fgf_settings_free_gift_success_message'));

				FGF_Rule_Handler::reset();

				wp_send_json_success(array( 'reload' => !FGF_Rule_Handler::get_remaining_gift_products_count() ));
			} catch (Exception $ex) {
				wp_send_json_error(array( 'error' => $ex->getMessage() ));
			}
		}

		/**
		 * Update the gift products content.
		 */
		public static function update_gift_products_content() {
			check_ajax_referer('fgf-gift-product', 'fgf_security');

			// Return if data args does not exists.
			$data_args = FGF_Gift_Products_Handler::get_gift_product_data();
			if (!empty($data_args)) {
				$data_args['mode'] = 'popup';
				// Display the gift products layout.
				fgf_get_template($data_args['template'], $data_args);
			}

			wp_die();
		}
	}

	FGF_Admin_Ajax::init();
}
