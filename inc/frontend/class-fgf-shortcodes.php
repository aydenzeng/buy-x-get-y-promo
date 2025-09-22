<?php

/**
 * Shortcodes.
 * */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
if (!class_exists('FGF_Shortcodes')) {

	/**
	 * Class.
	 */
	class FGF_Shortcodes {

		/**
		 * Plugin slug.
		 * 
		 * @var string
		 * */
		private static $plugin_slug = 'fgf';

		/**
		 * Class Initialization.
		 * */
		public static function init() {
			/**
			 * This hook is used to alter the short codes.
			 * 
			 * @since 1.0
			 */
			$shortcodes = apply_filters('fgf_load_shortcodes', array(
				'fgf_gift_products',
				'fgf_cart_eligible_notices',
				'fgf_progress_bar',
				'fgf_promotion_list',
				'fgf_promotion_detail',
				'fgf_promotion_products',
			));

			foreach ($shortcodes as $shortcode_name) {

				add_shortcode($shortcode_name, array( __CLASS__, 'process_shortcode' ));
			}
		}

		/**
		 * Process Shortcode.
		 * */
		public static function process_shortcode( $atts, $content, $tag ) {
			$shortcode_name = str_replace('fgf_', '', $tag);
			$function = 'shortcode_' . $shortcode_name;

			switch ($shortcode_name) {
				case 'gift_products':
				case 'cart_eligible_notices':
				case 'progress_bar':
				case 'promotion_list':
				case 'promotion_detail':
				case 'promotion_products':
					ob_start();
					self::$function($atts, $content); // output for shortcode.
					$content = ob_get_contents();
					ob_end_clean();
					break;

				default:
					ob_start();
					/**
					 * This hook is used to display the short code content.
					 * 
					 * @since 1.0
					 */
					do_action("fgf_shortcode_{$shortcode_name}_content");
					$content = ob_get_contents();
					ob_end_clean();
					break;
			}

			return $content;
		}

		/**
		 * Shortcode for the gift products.
		 * */
		public static function shortcode_gift_products( $atts, $content ) {

			$atts = shortcode_atts(array(
				'per_page' => fgf_get_free_gifts_per_page_column_count(),
				'mode' => 'inline',
				'type' => 'table',
					), $atts, 'fgf_gift_products');

			$atts['data_args'] = self::get_gift_product_data($atts);

			$atts['popup_message'] = fgf_get_gift_products_popup_notice();

			// Display the Gift Products shortcode layout.
			fgf_get_template('shortcode-layout.php', $atts);
		}

		/**
		 * Get Gift Product Data
		 */
		public static function get_gift_product_data( $atts ) {
			// Return if cart object is not initialized.
			if (!is_object(WC()->cart)) {
				return false;
			}

			// return if cart is empty
			if (WC()->cart->get_cart_contents_count() == 0) {
				return false;
			}

			// Restrict the display of the gift products when the maximum gifts count reached.
			if (!FGF_Rule_Handler::manual_product_exists() || FGF_Rule_Handler::check_per_order_count_exists()) {
				return;
			}

			$gift_products = FGF_Rule_Handler::get_overall_manual_gift_products();
			if (!fgf_check_is_array($gift_products)) {
				return false;
			}

			switch ($atts['type']) {
				case 'selectbox':
					$data_args = array(
						'template' => 'dropdown-layout.php',
						'gift_products' => $gift_products,
					);
					break;

				case 'carousel':
					$data_args = array(
						'template' => 'carousel-layout.php',
						'gift_products' => $gift_products,
					);
					break;

				default:
					$per_page = $atts['per_page'];
					$current_page = 1;

					/* Calculate Page Count */
					$default_args['posts_per_page'] = $per_page;
					$default_args['offset'] = ( $current_page - 1 ) * $per_page;
					$page_count = ceil(count($gift_products) / $per_page);

					$data_args = array(
						'template' => 'gift-products-layout.php',
						'gift_products' => array_slice($gift_products, $default_args['offset'], $per_page),
						'pagination' => array(
							'page_count' => $page_count,
							'current_page' => $current_page,
							'next_page_count' => ( ( $current_page + 1 ) > ( $page_count - 1 ) ) ? ( $current_page ) : ( $current_page + 1 ),
						),
					);
					break;
			}

			$data_args['mode'] = $atts['mode'];

			return $data_args;
		}

		/**
		 * Shortcode for the cart eligible notices.
		 * */
		public static function shortcode_cart_eligible_notices( $atts, $content ) {

			/**
			 * This hook is used to validate the eligible notice to show.
			 * 
			 * @since 1.0
			 */
			if (!apply_filters('fgf_is_valid_show_cart_eligible_notice', FGF_Notices_Handler::is_valid_show_eligible_notice())) {
				return '';
			}

			$cart_notices = FGF_Rule_Handler::get_cart_notices();
			foreach ($cart_notices as $cart_notice) {
				// Display the eligible gift product notice.
				$notices = array(
					'notice' =>
					array(
						'notice' => FGF_Notices_Handler::format_eligible_notice($cart_notice),
						'data' => array(),
					),
				);
								
				fgf_get_template('notices/notice.php', $notices);
			}
		}

		/**
		 * Shortcode for the progress bar to the manual gift products.
		 * 
		 * @since 9.8.0
		 * */
		public static function shortcode_progress_bar( $atts, $content ) {
			// Return if the gift products do not exist.
			$gift_products = FGF_Rule_Handler::get_overall_manual_gift_products();
			if (!fgf_check_is_array($gift_products)) {
				return;
			}

			fgf_get_template('progress-bar.php');
		}

		/**
		 * Placeholder for future use
		 * [fgf_promotion_products columns="4"]
		 */
		public static function shortcode_promotion_products($atts = array()){
			$atts = shortcode_atts(array(
				'columns' => 4, // 可选，控制列数
			), $atts, 'promotion_products');
			//獲取所有促銷產品
			$products = fgf_get_rule_valid_gift_products() ;
			if (!fgf_check_is_array($products)) return;
			if (!empty($products)) {
				// 临时设置 WooCommerce 列数
				add_filter('loop_shop_columns', function($columns) use ($atts) {
					return !empty($atts['columns']) ? intval($atts['columns']) : $columns;
				});

				$args = array(
					'post_type' => 'product',
					'post__in'  => $products,
					'orderby'   => 'post__in',
					'posts_per_page' => -1,
				);
				$loop = new WP_Query($args);

				ob_start();
				if ($loop->have_posts()) {
					woocommerce_product_loop_start(); // 输出 <ul class="products columns-4">
					while ($loop->have_posts()) : $loop->the_post();
						wc_get_template_part('content', 'product'); // 使用主题默认商品模板
					endwhile;
					woocommerce_product_loop_end();
				}
				wp_reset_postdata();
				$product_html = ob_get_clean();
			} else {
				$product_html = '';
			}
			echo $product_html;
			return;
		}
		/**
		 * Show promotion detail
		 * 
		 * Attributes:
		 * - id: Promotion post ID
		 * [fgf_promotion_detail id="123"]
		 */
		public static function shortcode_promotion_detail($atts = array()) {
			$atts = shortcode_atts(array(
				'id'      => 0,
				'columns' => 4, // 可选，控制列数
			), $atts, 'promotion_detail');

			// 如果 URL 里有 promo_id，就覆盖掉 $atts['id']
			if (isset($_GET['promo_id']) && is_numeric($_GET['promo_id'])) {
				$atts['id'] = intval($_GET['promo_id']);
			}

			if (empty($atts['id'])) {
				return '<p>没有找到促销活动。</p>';
			}

			$rule = fgf_get_rule($atts['id']);
			if (!$rule) {
				echo '<p>' . __('Promotion not found.', 'buy-x-get-y-promo') . '</p>';
				return;
			}

			$title = esc_html($rule->get_name());
			$from  = !empty($rule->get_parsed_from_date()) ? date_i18n("Y/m/d", strtotime($rule->get_formatted_from_date())) : '-';
			$to    = !empty($rule->get_parsed_to_date()) ? date_i18n("Y/m/d", strtotime($rule->get_formatted_to_date())) : '-';
			$validity = ($from === '-' && $to === '-') ? __('Unlimited', 'buy-x-get-y-promo') : "$from - $to";

			// 获取参与商品
			$buy_products = self::get_rule_products($rule)['buy'];
			$get_products = self::get_rule_products($rule)['get'];
			$products = array_unique(array_merge($buy_products, $get_products));

			if (!empty($products)) {
				// 临时设置 WooCommerce 列数
				add_filter('loop_shop_columns', function($columns) use ($atts) {
					return !empty($atts['columns']) ? intval($atts['columns']) : $columns;
				});

				$args = array(
					'post_type' => 'product',
					'post__in'  => $products,
					'orderby'   => 'post__in',
					'posts_per_page' => -1,
				);
				$loop = new WP_Query($args);

				ob_start();
				if ($loop->have_posts()) {
					woocommerce_product_loop_start(); // 输出 <ul class="products columns-4">
					while ($loop->have_posts()) : $loop->the_post();
						wc_get_template_part('content', 'product'); // 使用主题默认商品模板
					endwhile;
					woocommerce_product_loop_end();
				}
				wp_reset_postdata();
				$product_html = ob_get_clean();
			} else {
				$product_html = '<p>' . __('No products in this promotion.', 'buy-x-get-y-promo') . '</p>';
			}

			// 输出完整内容
			echo '<div class="fgf-promotion-detail">';
			echo '<h2 class="fgf-promotion-title">' . $title . '</h2>';
			echo '<p class="fgf-promotion-validity"><strong>' . __('Validity', 'buy-x-get-y-promo') . ':</strong> ' . $validity . '</p>';
			echo '<div class="fgf-promotion-description">' . wpautop(esc_html($rule->get_description())) . '</div>';
			echo '<div class="fgf-promotion-products-wrapper">';
			echo '<h3>' . __('Products in this Promotion', 'buy-x-get-y-promo') . '</h3>';
			echo $product_html;
			echo '</div>';
			echo '</div>';
		}



		/**
		 * Show all promotions list
		 * 
		 * Attributes:
		 * - mode: scroll | button | pagination (default: scroll)
		 * - per_page: number of promotions per page (default: 3)
		 * [fgf_promotion_list mode="scroll" per_page="3" ids="1,2,3,4"]
		 * [fgf_promotion_list mode="click" per_page="5" ids="1,2,3,4"]
		 * [fgf_promotion_list mode="pagination" per_page="10" ids="1,2,3,4"]
		 */
		public static function shortcode_promotion_list($atts = array()) {
			global $wpdb;

			// 短代码参数
			$atts = shortcode_atts(array(
				'mode'     => 'pagination', // scroll | click | pagination
				'per_page' => 10,   // 每页显示多少条
				'columns'  => 4,        // 网格列数
				'ids'      => '',       // 指定显示的促销ID，逗号分隔
			), $atts, 'promotion_list');

			$per_page = intval($atts['per_page']);
			$mode     = sanitize_text_field($atts['mode']);
			$ids 	= sanitize_text_field($atts['ids']);
			$columns  = intval($atts['columns']);
			$paged    = ($mode === 'pagination' && isset($_GET['fgf_page'])) ? max(1, intval($_GET['fgf_page'])) : 1;

			$rules_post_type = FGF_Register_Post_Types::RULES_POSTTYPE;
			// $statuses        = implode("','", fgf_get_rule_statuses());
			// 如果指定了 IDs，则只查询这些 ID 的促销
			if (!empty($ids)) {
				$id_array = array_map('intval', explode(',', $ids));
				$id_placeholders = implode(',', array_fill(0, count($id_array), '%d'));
				$id_where = $wpdb->prepare("AND ID IN ($id_placeholders)", ...$id_array);
			} else {
				$id_where = '';
			}
			// 总数
			$total_rules = $wpdb->get_var("
				SELECT COUNT(*) 
				FROM $wpdb->posts 
				WHERE post_type = '$rules_post_type' 
				AND post_status = 'fgf_active'
				$id_where
			");
			if (!empty($ids)) {
				// 如果指定了 IDs，则每页显示所有符合条件的促销
				$columns = $per_page = $total_rules; 
			}
			if ($total_rules == 0) {
				echo '<p>' . __('No promotions available.', 'buy-x-get-y-promo') . '</p>';
				return;
			}

			$total_pages = ceil($total_rules / $per_page);
			$offset      = ($paged - 1) * $per_page;

			$rules = $wpdb->get_results($wpdb->prepare("
				SELECT ID, post_title 
				FROM $wpdb->posts 
				WHERE post_type = %s 
				AND post_status = 'fgf_active'
				$id_where
				ORDER BY menu_order ASC, ID ASC
				LIMIT %d OFFSET %d
			", $rules_post_type, $per_page, $offset));

			// 初始内容
			$output  = '<div id="promotion-list" class="fgf-promotions-grid" style="grid-template-columns: repeat(' . intval($columns) . ', 1fr); gap: 20px; margin:20px 0;">';
			$output .= self::render_promotion_cards($rules);
			$output .= '</div>';
			//如果指定了　ids ,直接放弃其它逻辑，直接输出
			if (!empty($ids)) {
				echo $output;
				return;
			}

			// 分页模式：输出分页导航
			if ($mode === 'pagination' && $total_pages > 1) {
				$output .= '<div id="promotion-pagination" class="fgf-pagination">';
				for ($i = 1; $i <= $total_pages; $i++) {
					if ($i == $paged) {
						$output .= "<span class='current-page'>{$i}</span> ";
					} else {
						$output .= "<a href='#' data-page='{$i}'>{$i}</a> ";
					}
				}
				$output .= '</div>';
			}

			// 非分页模式：输出加载提示
			if ($mode !== 'pagination') {
				$output .= '<div id="promotion-loader" style="display:none;text-align:center;margin:20px 0;">'
						. __('Loading...', 'buy-x-get-y-promo')
						. '</div>';
			}

			// 注入 JS 参数
			wp_enqueue_script('fgf-promotion-js', FGF_PLUGIN_URL . '/assets/js/fgf-promotion-list.js', array('jquery'), '1.0', true);
			wp_localize_script('fgf-promotion-js', 'fgf_ajax', array(
				'ajaxurl'  => admin_url('admin-ajax.php'),
				'nonce'    => wp_create_nonce('fgf_promotion_list_nonce'),
				'mode'     => $mode,
				'per_page' => $per_page,
				'maxPage'  => $total_pages,
				'current'  => $paged,
			));
			echo $output;
			return ;
		}

		/**
		 * @param FGF_Rule $rule
		 * @return array
		 */
		public static function get_rule_products($rule){
			$buy_products = [];
			$get_products = [];
			$get_products_categories = [];
			$rules_type   = $rule->get_rule_type();
			switch ($rules_type) {
					case '1':
					case '2':
						$buy_products = $rule->get_buy_product();
						$get_products = $rule->get_gift_products();
						break;
					case '5'://買 X 得 Y - 手動
						$buy_products = $rule->get_buy_product();
						if ('2' === $rule->get_product_type()) {
							$get_products_categories = $rule->get_categories();
						} else {
							$get_products = $rule->get_products();
						}
						break;
					case '3':
						$bogo_gift_type = $rule->get_bogo_gift_type();
						$buy_products   = $rule->get_buy_product();
						$get_products   = ($bogo_gift_type == '1') ? $buy_products : self::$rule->get_products();
						break;
					case '4'://基於優惠券的免費贈品 - 自動
					case '6'://基於優惠券的免費贈品 - 手動
						$get_products = $rule->get_coupon_gift_products();
					break;
					case '7'://基於總額贈品  - 手動
						if ('2' === $rule->get_subtotal_gift_type()) {
							$get_products_categories = $rule->get_subtotal_gift_categories();
						} else {
							$get_products = $rule->get_subtotal_gift_products();
						}
						break;
					case '8'://基於總額贈品  - 自動
						$buy_products = [];
						$get_products = $rule->get_subtotal_gift_products();
						break;
					default:
						if ('2' === $rule->get_gift_type()) {
							$get_products_categories = $rule->get_gift_categories();
						} else {
							$get_products = $rule->get_gift_products();
						}

						break;
				}
			return array('buy'=>$buy_products,'get'=>$get_products,'get_categories'=>isset($get_products_categories)?$get_products_categories:[]);
		}
		

		/**
		 * Render promotion cards HTML
		 * 标记$rules数组中的变量类型
		 * @param array[FGF_Rule] $rules
		 * @return string
		 */
		public static function render_promotion_cards($rules) {
			$html = '';

			foreach ($rules as $rule_post) {
				$rule = fgf_get_rule($rule_post->ID);
				if (!$rule) continue;

				$title       = esc_html($rule->get_name());
				$from        = !empty($rule->get_parsed_from_date()) ? date_i18n("Y/m/d", strtotime($rule->get_formatted_from_date())) : '-';
				$to          = !empty($rule->get_parsed_to_date()) ? date_i18n("Y/m/d", strtotime($rule->get_formatted_to_date())) : '-';
				$validity    = ($from === '-' && $to === '-') ? __('Unlimited', 'buy-x-get-y-promo') : "$from - $to";
				$description = esc_html($rule->get_description());

				$product_buy_links = [];
				$product_get_links = [];
				
				$buy_products = self::get_rule_products($rule)['buy'];
				$get_products = self::get_rule_products($rule)['get'];

				$products = array_unique(array_merge($buy_products, $get_products));
				if (is_array($products)) {
					foreach ($products as $product_id) {
						$product = wc_get_product($product_id);
						if ($product) {
							if (in_array($product_id, $buy_products)) {
								$product_buy_links[] = '<a href="' . get_permalink($product_id) . '">' . $product->get_name() . '</a>';
							}
							if (in_array($product_id, $get_products)) {
								$product_get_links[] = '<a href="' . get_permalink($product_id) . '">' . $product->get_name() . '</a>';
							}
						}
					}
				}
				$html .= '<div class="fgf-promotion-card" data-href="' . esc_url($rule->get_frontend_permalink()) . '">';
				$html .= '<h3 class="fgf-promotion-title">' . $title . '</h3>';
				$html .= '<p class="fgf-promotion-validity"><strong>' . __('Validity', 'buy-x-get-y-promo') . '</strong> ' . $validity . '</p>';
				if (!empty($product_buy_links)) {
					$html .= '<p><strong>' . __('Buy Products', 'buy-x-get-y-promo') . ':</strong> ' . implode(', ', $product_buy_links) . '</p>';
				}
				if (!empty($product_get_links)) {
					$html .= '<p><strong>' . __('Get Products', 'buy-x-get-y-promo') . ':</strong> ' . implode(', ', $product_get_links) . '</p>';
				}
				if ($description) {
					$html .= '<p class="fgf-promotion-description">' . $description . '</p>';
				}
				$html .= '</div>';
			}
			return $html;
		}


	}

	FGF_Shortcodes::init();
}
