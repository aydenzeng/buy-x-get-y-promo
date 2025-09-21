<?php
/**
 * HTML- Subtotal Gift total brands search fields.
 * 
 * @since 11.5.0
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
?>
<div class='fgf-field-wrapper'>
	<span class='fgf-field-title'>
		<label><?php esc_html_e('Select Brands', 'buy-x-get-y-promo'); ?><span class='required'>*</span></label>
		<?php fgf_wc_help_tip(__('The products from the selected brands will be displayed to the user', 'buy-x-get-y-promo')); ?>
	</span>
	<span class='fgf-field'>
		<select class='fgf_select2 fgf-rule-total-type-fields fgf-rule-total-type-brands' name='fgf_rule[fgf_subtotal_gift_total_brands][]' multiple='multiple'>
			<?php
			foreach ($brands as $brand_id => $brand_name) :
				$selected = ( in_array($brand_id, $selected_brand_ids) ) ? ' selected="selected"' : '';
				?>
				<option value="<?php echo esc_attr($brand_id); ?>"<?php echo esc_attr($selected); ?>><?php echo esc_html($brand_name); ?></option>
			<?php endforeach; ?>
		</select>
	</span>
</div>
<?php
