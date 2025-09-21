<?php
/**
 * Rule Panel.
 */
if (!defined('ABSPATH')) {
	exit;
}
?>
<div id="fgf-rule-data-panel-wrapper">
	<div class="fgf-rule-data-panel-header">

		<p class="form-field">
			<label><?php esc_html_e('Free Gift Type', 'buy-x-get-y-promo'); ?><span class="required">* </span>
				<?php fgf_wc_help_tip(__("When set to Manual Gifts, the users can choose their gift product(s). When set to Automatic Gifts, the gift product(s) set in this rule will be automatically added to the user's cart. When set to Buy X Get Y, the user will get the specified quantities of the product for free if they purchase the specified quantities of the product. When set to Coupon based Free Gift, the user will eligible for receiving gift product(s) once the required coupon is applied.", 'buy-x-get-y-promo')); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped. ?>
			</label>

			<select name="fgf_rule[fgf_rule_type]" class="fgf_rule_types">
				<option value="1" <?php selected($rule_data['fgf_rule_type'], '1'); ?>><?php esc_html_e('Manual Gifts', 'buy-x-get-y-promo'); ?></option>
				<option value="2" <?php selected($rule_data['fgf_rule_type'], '2'); ?>><?php esc_html_e('Automatic Gifts', 'buy-x-get-y-promo'); ?></option>
				<optgroup label="<?php esc_attr_e('Buy X Get Y(Buy One Get One)', 'buy-x-get-y-promo'); ?>">
					<option value="5" <?php selected($rule_data['fgf_rule_type'], '5'); ?>><?php esc_html_e('Buy X Get Y - Manual', 'buy-x-get-y-promo'); ?></option>
					<option value="3" <?php selected($rule_data['fgf_rule_type'], '3'); ?>><?php esc_html_e('Buy X Get Y - Automatic', 'buy-x-get-y-promo'); ?></option>
				</optgroup>
				<optgroup label="<?php esc_attr_e('Total Based Free Gifts', 'buy-x-get-y-promo'); ?>">
					<option value='7' <?php selected($rule_data['fgf_rule_type'], '7'); ?>><?php esc_html_e('Total-based Free Gift - Manual', 'buy-x-get-y-promo'); ?></option>
					<option value='8' <?php selected($rule_data['fgf_rule_type'], '8'); ?>><?php esc_html_e('Total-based Free Gift - Automatic', 'buy-x-get-y-promo'); ?></option>
				</optgroup>
				<!-- Ayden zeng update -->
				<!-- <optgroup label="<?php esc_attr_e('Coupon Based Free Gifts', 'buy-x-get-y-promo'); ?>">
					<option value="6" <?php selected($rule_data['fgf_rule_type'], '6'); ?>><?php esc_html_e('Coupon based Free Gift - Manual', 'buy-x-get-y-promo'); ?></option>
					<option value="4" <?php selected($rule_data['fgf_rule_type'], '4'); ?>><?php esc_html_e('Coupon based Free Gift - Automatic', 'buy-x-get-y-promo'); ?></option>
				</optgroup> -->
			</select>
		</p>

		<p class="form-field fgf-rule-consider-type-field">
			<label><?php esc_html_e('Apply this Rule Along with Other Matching Rules', 'buy-x-get-y-promo'); ?><span class="required">* </span></label>
			<select class='fgf-rule-consider-type' name='fgf_rule[fgf_rule_consider_type]'>
				<option value='1' <?php selected($rule_data['fgf_rule_consider_type'], '1'); ?>><?php esc_html_e('Enable', 'buy-x-get-y-promo'); ?></option>
				<option value='2' <?php selected($rule_data['fgf_rule_consider_type'], '2'); ?>><?php esc_html_e('Disable', 'buy-x-get-y-promo'); ?></option>
			</select>
		</p>
	</div>

	<div class="fgf-rule-data-panel-content">

		<ul class="fgf-rule-data-tabs">
			<?php foreach (self::get_rule_data_tabs() as $key => $panel_tab) : ?>
				<li class="fgf-rule-data-tab <?php echo esc_attr($key); ?>_tab <?php echo esc_attr(isset($panel_tab['class']) ? implode(' ', (array) $panel_tab['class']) : '' ); ?>">
					<a href="#<?php echo esc_attr($panel_tab['target']); ?>" class="fgf-rule-data-tab-link"><span><?php echo esc_html($panel_tab['label']); ?></span></a>
				</li>
			<?php endforeach; ?>
		</ul>

		<?php
		self::output_tabs();
		/**
		 * This hook is used to display the extra panel contents.
		 *
		 * @since 1.0
		 */
		do_action('fgf_rule_data_panels');
		?>
		<div class="clear"></div>
	</div>
</div>
<?php
