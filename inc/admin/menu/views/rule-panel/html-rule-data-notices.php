<?php
/**
 * Panel- Notices.
 * 
 * @since 1.0.0
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
?>
<div id='fgf_rule_data_notices' class='fgf-rule-options-wrapper'>
	<div class='fgf-rule-notices-fields-wrapper fgf-rule-fields-section'>
		<h2><?php esc_html_e('Eligibility Notice Configuration', 'buy-x-get-y-promo'); ?></h2>
		<div class='fgf-rule-fields-section'>
			<?php
			/**
			 * This hook is used to do extra action before rule notices settings.
			 *
			 * @since 1.0.0
			 */
			do_action('fgf_before_rule_notices_settings', $rule_data);
			?>
			<div class='fgf-field-wrapper'>
				<span class='fgf-field-title'>
					<label><?php esc_html_e('Display Free Gift Eligibility Notice in Cart for this Rule', 'buy-x-get-y-promo'); ?>
						<?php fgf_wc_help_tip(__('When set to "Show", a notice will be displayed to user in cart and checkout if they are not eligible for receiving free gifts from this rule.', 'buy-x-get-y-promo')); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped. ?>
					</label>
				</span>
				<span class='fgf-field'>
					<select name="fgf_rule[fgf_show_notice]" class="fgf_rule_show_notice">
						<option value="1" <?php selected($rule_data['fgf_show_notice'], '1'); ?>><?php esc_html_e('Hide', 'buy-x-get-y-promo'); ?></option>
						<option value="2" <?php selected($rule_data['fgf_show_notice'], '2'); ?>><?php esc_html_e('Show', 'buy-x-get-y-promo'); ?></option>
					</select>
				</span>
			</div>

			<div class='fgf-field-wrapper'>
				<span class='fgf-field-title'>
					<label><?php esc_html_e('Free Gifts Eligibility Notice', 'buy-x-get-y-promo'); ?></label>
				</span>
				<span class='fgf-field'>
					<textarea name="fgf_rule[fgf_notice]" cols="20" rows="5" class="fgf_rule_notice"><?php echo wp_kses_post($rule_data['fgf_notice']); ?></textarea>
				</span>
			</div>

			<div class='fgf-field-wrapper'>
				<span class='fgf-field-title'>
					<label><?php esc_html_e('Image/Icon for Eligibility Notice', 'buy-x-get-y-promo'); ?></label>
				</span>
				<span class='fgf-field'>
					<span class='fgf-upload-img-container fgf_rule_notice'>
						<input type='hidden' class='fgf-upload-img-url' name='fgf_rule[fgf_notice_image_id]' value="<?php echo esc_attr($rule_data['fgf_notice_image_id']); ?>"/>
						<?php $image_url = ( $rule_data['fgf_notice_image_id'] ) ? wp_get_attachment_image_url($rule_data['fgf_notice_image_id']) : ''; ?>
						<div class='fgf-uploaded-img-wrapper'>
							<img id='target' src="<?php echo esc_url($image_url); ?>" class='fgf-uploaded-img-preview'/>
						</div>
						<button type='button' class='fgf-upload-img'><?php esc_html_e('Select a Image', 'buy-x-get-y-promo'); ?></button>
						<button type='button' class='fgf-delete-uploaded-img<?php echo ( '' == $image_url ) ? esc_attr(' fgf-hide') : ''; ?>'><?php esc_html_e('Delete Image', 'buy-x-get-y-promo'); ?></button>
					</span>
				</span>
			</div>

			<?php
			/**
			 * This hook is used to do extra action after rule notices settings.
			 *
			 * @since 1.0
			 */
			do_action('fgf_after_rule_notices_settings', $rule_data);
			?>
		</div>
	</div>
	<div class='fgf-rule-fields-section'>
		<h3><?php esc_html_e('Shortcodes', 'buy-x-get-y-promo'); ?></h3>
		<table class="fgf-shortcode-table">
			<?php
			$shortcode_details = fgf_get_rule_notice_shortcode_details();
			if (fgf_check_is_array($shortcode_details)) :
				foreach ($shortcode_details as $short_detail) :
					?>
					<tr>
						<th><?php echo esc_html($short_detail['shortcode']); ?></th>
						<td><?php echo esc_html($short_detail['desc']); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</table>
	</div>
</div>
<?php
