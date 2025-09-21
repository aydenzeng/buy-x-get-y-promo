<?php
/* Edit Rule Page */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

$categories = fgf_get_wc_categories();
?>
<div class="woocommerce fgf_rule_wrapper fgf_update_rule">
	<h2><?php esc_html_e('Edit Rule', 'buy-x-get-y-promo'); ?></h2>
	<table class="form-table">
		<tbody>

			<tr>
				<th scope='row'>
					<label><?php esc_html_e('Rule Status', 'buy-x-get-y-promo'); ?><span class="required">*</span>
						<?php fgf_wc_help_tip(__('When set to Active, the products from this rule will be listed to the user. The user can choose their Free Gift from the available products.', 'buy-x-get-y-promo')); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped. ?>
					</label>
				</th>
				<td>
					<select name="fgf_rule[fgf_rule_status]">
						<?php
						foreach (fgf_get_rule_statuses_options() as $rule_status_key => $rule_status_name) :
							?>
							<option value="<?php echo esc_attr($rule_status_key); ?>" <?php selected($rule_data['fgf_rule_status'], $rule_status_key); ?>><?php echo esc_html($rule_status_name); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>

			<tr>
				<th scope='row'>
					<label><?php esc_html_e('Rule Name', 'buy-x-get-y-promo'); ?><span class="required">*</span></label>
				</th>
				<td>
					<input type="text" name="fgf_rule[fgf_rule_name]" value="<?php echo esc_attr($rule_data['fgf_rule_name']); ?>"/>
				</td>
			</tr>

			<tr>
				<th scope='row'>
					<label><?php esc_html_e('Description', 'buy-x-get-y-promo'); ?></label>
				</th>
				<td>
					<textarea name="fgf_rule[fgf_rule_description]"><?php echo esc_html($rule_data['fgf_rule_description']); ?></textarea>
				</td>
			</tr>

		</tbody>
	</table>
	<?php
	self::output_panel();
	?>
	<p class="submit">
		<input name='fgf_rule_id' type='hidden' value="<?php echo esc_attr($rule_data['id']); ?>" />
		<input name='fgf_save' class='button-primary fgf_save_btn' type='submit' value="<?php esc_attr_e('Update Rule', 'buy-x-get-y-promo'); ?>" />
		<?php wp_nonce_field('fgf_update_rule', '_fgf_nonce', false, true); ?>
	</p>
</div>
<?php
