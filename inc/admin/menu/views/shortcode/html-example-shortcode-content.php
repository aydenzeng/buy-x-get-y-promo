<?php
/**
 * Content - Example short codes. 
 * 
 * @since 11.4.0
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
?>
<div id='fgf-example-shortcode-content' class='fgf-shortcode-tab-content'>
	<div class='fgf-common-shortcode-examples-wrapper fgf-shortcode-examples-wrapper'>
		<h3><?php esc_html_e('Common Shortcodes', 'buy-x-get-y-promo'); ?></h3>
		<div class='fgf-shortcode-details'>
			<p class='fgf-shortcode-title'><?php printf('<b>%s :</b> [fgf_gift_products]', esc_html__('Shortcode', 'buy-x-get-y-promo')); ?></p>
			<p><?php printf('<b>%s :</b> table, carousel, selectbox', esc_html__('Paramaters', 'buy-x-get-y-promo')); ?></p>
			<p><b>Eg:</b> [fgf_gift_products type="table" mode="inline" per_page="5"]</p>
		</div>

		<div class='fgf-shortcode-details'>
			<p class='fgf-shortcode-title'><?php printf('<b>%s :</b> [fgf_cart_eligible_notices]', esc_html__('Shortcode', 'buy-x-get-y-promo')); ?></p>
			<p class='fgf-shortcode-example'><b>Eg:</b> [fgf_cart_eligible_notices]</p>
		</div>

		<div class='fgf-shortcode-details'>
			<p class='fgf-shortcode-title'><?php printf('<b>%s :</b> [fgf_progress_bar]', esc_html__('Shortcode', 'buy-x-get-y-promo')); ?></p>
			<p class='fgf-shortcode-example'><b>Eg:</b> [fgf_progress_bar]</p>
		</div>
	</div>
</div>
<?php
