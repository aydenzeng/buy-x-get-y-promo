<?php
/**
 * Panel - Restrictions.
 * 
 * @since 1.0.0
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
?>
<div id='fgf_rule_data_restrictions' class='fgf-rule-options-wrapper'>
	<?php
	/**
	 * This hook is used to do extra action before rule restrictions settings.
	 * 
	 * @since 1.0.0
	 */
	do_action('fgf_before_rule_restrictions_settings', $rule_data);
	?>
	<div class='fgf-rule-order-restriction-fields-wrapper fgf-rule-fields-section'>
		<h2><?php esc_html_e('Order Restrictions', 'buy-x-get-y-promo'); ?></h2>
		<?php if ('2' == get_option('fgf_settings_gifts_count_per_order_type', '2')) : ?>
			<div class='fgf-field-wrapper'>
				<span class='fgf-field-title'>
					<label><?php esc_html_e('Maximum Gifts in an Order from this Rule', 'buy-x-get-y-promo'); ?>
						<?php fgf_wc_help_tip(__('If left empty / when the rule value is more the Global Restriction, the Global Restriction will apply.', 'buy-x-get-y-promo')); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped. ?>
					</label>
				</span>
				<span class='fgf-field'>
					<input type="number" class="fgf_rule_type fgf_manual_rule_type fgf_manual_coupon_rule_type" name="fgf_rule[fgf_rule_gifts_count_per_order]" min="1" value="<?php echo esc_attr($rule_data['fgf_rule_gifts_count_per_order']); ?>"/>
				</span>				
			</div>
		<?php endif; ?>

		<div class='fgf-field-wrapper'>
			<span class='fgf-field-title'>
				<label><?php esc_html_e('Maximum Number of Orders', 'buy-x-get-y-promo'); ?>
					<?php fgf_wc_help_tip(__('The gift products will be restricted once the value given in this field has been reached. If left empty, the rule will be applicable for unlimited orders. ', 'buy-x-get-y-promo')); ?>
				</label>
			</span>
			<span class='fgf-field'>
				<input type="number" name="fgf_rule[fgf_rule_restriction_count]" min="1" value="<?php echo esc_attr($rule_data['fgf_rule_restriction_count']); ?>"/>
			</span>
			<?php if ($rule_data['fgf_rule_restriction_count']) : ?>
				<span class='fgf-field'>    
					<?php
					$remaining_count = max(floatval($rule_data['fgf_rule_restriction_count']) - floatval($rule_data['fgf_rule_usage_count']), 0);
					/* translators: %s: number of orders and rule usage count */
					echo wp_kses_post(sprintf(__('Orders (%1$s used %2$d remaining)', 'buy-x-get-y-promo'), floatval($rule_data['fgf_rule_usage_count']), $remaining_count));
					?>
					<input type="button" class="fgf_reset_rule_usage_count button-primary" data-rule-id="<?php echo esc_attr($rule_data['id']); ?>" value="<?php esc_attr_e('Reset used count', 'buy-x-get-y-promo'); ?>"/>
				</span>
			<?php endif; ?>
		</div>
	</div>
	<div class='fgf-rule-user-restriction-fields-wrapper fgf-rule-fields-section'>
		<h2><?php esc_html_e('User Restrictions', 'buy-x-get-y-promo'); ?></h2>
		<div class='fgf-field-wrapper'>
			<span class='fgf-field-title'>
				<label><?php esc_html_e('Order Restriction per User', 'buy-x-get-y-promo'); ?><span class="required">*</span>
					<?php fgf_wc_help_tip(__('When set to Enable, registered users can be restricted to receive free gift(s) from this rule for a fixed number of times.', 'buy-x-get-y-promo')); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped. ?>
				</label>
			</span>
			<span class='fgf-field'>
				<select class="fgf_rule_allowed_user_type" name="fgf_rule[fgf_rule_allowed_user_type]">
					<option value="1" <?php selected($rule_data['fgf_rule_allowed_user_type'], '1'); ?>><?php esc_html_e('Disable', 'buy-x-get-y-promo'); ?></option>
					<option value="2" <?php selected($rule_data['fgf_rule_allowed_user_type'], '2'); ?>><?php esc_html_e('Enable - For Registered Users Only', 'buy-x-get-y-promo'); ?></option>
				</select>
			</span>
		</div>

		<div class='fgf-field-wrapper'>
			<span class='fgf-field-title'>
				<label><?php esc_html_e('Number of Order(s) per User', 'buy-x-get-y-promo'); ?>
					<?php fgf_wc_help_tip(__('The number of order(s) for which each registered users can receive gift product(s) from this rule.', 'buy-x-get-y-promo')); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped. ?>
				</label>
			</span>
			<span class='fgf-field'>
				<input type="number" class="fgf_rule_allowed_user_count fgf-rule-user-count-field" name="fgf_rule[fgf_rule_allowed_user_count]" min="0" value="<?php echo esc_attr($rule_data['fgf_rule_allowed_user_count']); ?>"/>
			</span>				
		</div>

		<div class='fgf-field-wrapper'>
			<span class='fgf-field-title'>
				<label><?php esc_html_e("Award Gift based on User's Purchase History", 'buy-x-get-y-promo'); ?><span class="required">*</span></label>
			</span>
			<span class='fgf-field'>
				<select class='fgf_rule_user_purchased_order_count_type fgf-rule-user-count-field' name='fgf_rule[fgf_rule_user_purchased_order_count_type]'>
					<option value='1' <?php selected($rule_data['fgf_rule_user_purchased_order_count_type'], '1'); ?>><?php esc_html_e('Disable', 'buy-x-get-y-promo'); ?></option>
					<option value='2' <?php selected($rule_data['fgf_rule_user_purchased_order_count_type'], '2'); ?>><?php esc_html_e('No Purchase History Available Case Only', 'buy-x-get-y-promo'); ?></option>
					<option value='3' <?php selected($rule_data['fgf_rule_user_purchased_order_count_type'], '3'); ?>><?php esc_html_e('Specific Number of Orders', 'buy-x-get-y-promo'); ?></option>
				</select>
			</span>
		</div>

		<div class='fgf-field-wrapper'>
			<span class='fgf-field-title'>
				<label><?php esc_html_e('Previously Purchased Order Count', 'buy-x-get-y-promo'); ?><span class="required">*</span></label>
			</span>
			<span class='fgf-field'>
				<?php esc_html_e('Min', 'buy-x-get-y-promo'); ?>
				<input type='number' name='fgf_rule[fgf_rule_user_purchased_order_min_count]' class='fgf_rule_user_purchased_order_min_count fgf-rule-user-count-field' min='1' value='<?php echo esc_attr($rule_data['fgf_rule_user_purchased_order_min_count']); ?>'/>
			</span>
			<span class='fgf-field'>
				<?php esc_html_e('Max', 'buy-x-get-y-promo'); ?>
				<input type='number' name='fgf_rule[fgf_rule_user_purchased_order_max_count]' class='fgf_rule_user_purchased_order_max_count fgf-rule-user-count-field' min='1' value='<?php echo esc_attr($rule_data['fgf_rule_user_purchased_order_max_count']); ?>'/>
			</span>
		</div>
	</div>
	<div class='fgf-rule-date-restriction-fields-wrapper fgf-rule-fields-section'>
		<h2><?php esc_html_e('Validity Restrictions', 'buy-x-get-y-promo'); ?></h2>
		<div class='fgf-field-wrapper'>
			<span class='fgf-field-title'>
				<label><?php esc_html_e('Rule Validity', 'buy-x-get-y-promo'); ?>
					<?php fgf_wc_help_tip(__('If left empty, the rule will be valid on all days.', 'buy-x-get-y-promo')); ?>
				</label>
			</span>
			<span class='fgf-field'>
				<?php esc_html_e('From', 'buy-x-get-y-promo'); ?>
				<?php
				fgf_get_datepicker_html(array(
					'name' => 'fgf_rule[fgf_rule_valid_from_date]',
					'value' => $rule_data['fgf_rule_valid_from_date'],
					'wp_zone' => false,
					'with_time' => true,
					'placeholder' => FGF_Date_Time::get_wp_datetime_format(),
				));
				?>
			</span>
			<span class='fgf-field'>
				<?php esc_html_e('To', 'buy-x-get-y-promo'); ?>
				<?php
				fgf_get_datepicker_html(array(
					'name' => 'fgf_rule[fgf_rule_valid_to_date]',
					'value' => $rule_data['fgf_rule_valid_to_date'],
					'wp_zone' => false,
					'with_time' => true,
					'placeholder' => FGF_Date_Time::get_wp_datetime_format(),
				));
				?>
			</span>
		</div>

		<div class='fgf-field-wrapper'>
			<span class='fgf-field-title'>
				<label><?php esc_html_e('Week Day(s) Restrictions', 'buy-x-get-y-promo'); ?>
					<?php fgf_wc_help_tip(__('The rule will be valid for the selected Days. If left empty, the rule will be valid for all days of the week.', 'buy-x-get-y-promo')); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped. ?>
				</label>
			</span>

			<span class='fgf-field'>
				<select class="fgf-rule-week-days-validation fgf_select2" multiple="multiple" name="fgf_rule[fgf_rule_week_days_validation][]">
					<?php foreach (fgf_get_rule_week_days_options() as $week_days_id => $week_days_name) : ?>
						<option value="<?php echo esc_attr($week_days_id); ?>" <?php echo in_array($week_days_id, $rule_data['fgf_rule_week_days_validation']) ? 'selected="selected"' : ''; ?>><?php echo esc_html($week_days_name); ?></option>
					<?php endforeach; ?>
				</select>
			</span>
		</div>
	</div>

	<div class='fgf-rule-coupon-restriction-fields-wrapper fgf-rule-fields-section'>
		<h2><?php esc_html_e('Coupon Restrictions', 'buy-x-get-y-promo'); ?></h2>
		<div class='fgf-field-wrapper'>
			<span class='fgf-field-title'>
				<label><?php esc_html_e('WooCommerce Coupon usage based Gift restriction type', 'buy-x-get-y-promo'); ?></label>
			</span>
			<span class='fgf-field'>
				<select class='fgf-rule-restrict-by-wocommerce-coupon-type' name='fgf_rule[fgf_rule_restrict_by_wocommerce_coupon_type]'>
					<option value='1' <?php selected($rule_data['fgf_rule_restrict_by_wocommerce_coupon_type'], '1'); ?>><?php esc_html_e('Global Level', 'buy-x-get-y-promo'); ?></option>
					<option value='2' <?php selected($rule_data['fgf_rule_restrict_by_wocommerce_coupon_type'], '2'); ?>><?php esc_html_e('Rule Level', 'buy-x-get-y-promo'); ?></option>
				</select>
			</span>
		</div>

		<div class='fgf-field-wrapper'>
			<span class='fgf-field-title'>
				<label><?php esc_html_e('Restrict Free Gift if WooCommerce Coupon is used', 'buy-x-get-y-promo'); ?></label>
			</span>
			<span class='fgf-field'>
				<select class='fgf-rule-restrict-by-wocommerce-coupon' name='fgf_rule[fgf_rule_restrict_by_wocommerce_coupon]'>
					<option value='1' <?php selected($rule_data['fgf_rule_restrict_by_wocommerce_coupon'], '1'); ?>><?php esc_html_e('No', 'buy-x-get-y-promo'); ?></option>
					<option value='2' <?php selected($rule_data['fgf_rule_restrict_by_wocommerce_coupon'], '2'); ?>><?php esc_html_e('Yes', 'buy-x-get-y-promo'); ?></option>
				</select>
			</span>
		</div>
	</div>

	<?php
	/**
	 * This hook is used to do extra action after rule restrictions settings.
	 * 
	 * @since 1.0
	 */
	do_action('fgf_after_rule_restrictions_settings', $rule_data);
	?>
</div>
<?php
