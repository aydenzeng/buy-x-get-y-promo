<?php
/**
 * Shortcodes.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
?>
<table class="form-table fgf_parameter_syntax widefat">
	<thead>
		<tr>
			<th><?php esc_html_e( 'Syntax', 'buy-x-get-y-promo' ) ; ?></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?php esc_html_e( 'Syntax', 'buy-x-get-y-promo' ) ; ?></td>
			<td><?php esc_html_e( '[shortcode parameter1 = "value" parameter2 = "value" ]' ) ; ?></td>
		</tr>
	</tbody>
</table>

<h2><?php esc_html_e( 'Example', 'buy-x-get-y-promo' ) ; ?></h2>
<p><b>[fgf_gift_products type="carousel" mode="inline"]</b></p>
<p><b>[fgf_gift_products type="table" per_page="2"]</b></p>
<p><b>[fgf_cart_eligible_notices]</b></p>
<p><b>[fgf_progress_bar]</b></p>

<table class="form-table fgf_parameter_list widefat">
	<thead>
		<tr>
			<th><?php esc_html_e( 'Parameters', 'buy-x-get-y-promo' ) ; ?></th>
			<th><?php esc_html_e( 'Value', 'buy-x-get-y-promo' ) ; ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>type</td>
			<td>carousel, table, selectbox</td>
		</tr>
		<tr>
			<td>mode</td>
			<td>inline, popup</td>
		</tr>
		<tr>
			<td>per_page</td>
			<td>any number</td>
		</tr>
	</tbody>
</table>
<?php


