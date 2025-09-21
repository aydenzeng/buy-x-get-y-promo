<?php
/* Master Log Popup */

if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
?>
<div class="fgf_popup_wrapper">
	<div class="fgf_master_log_info_popup_content">
		<div class="fgf_master_log_info_popup_header">
			<label class="fgf_master_log_info_popup_label"> 
				<?php
				/* translators: %s: number of masterlogs */
				echo wp_kses_post( sprintf( __( 'Free Gifts for Order #%s', 'buy-x-get-y-promo' ), $master_log_object->get_order_id() ) ) ;
				?>
			</label> 
		</div>
		<div class="fgf_master_log_info_popup_close">
			<img src=<?php echo esc_url( FGF_PLUGIN_URL . '/assets/images/close.png' ) ; ?> class="fgf_popup_close">
		</div>
		<div class="fgf_master_log_info_popup_body">
			<div class="fgf_master_log_info_popup_body_content">
				<div class="fgf_master_log_info_status">
					<table class="fgf_master_log_info_table" style="margin-top: 20px;">
						<?php $product_details = $master_log_object->get_product_details() ; ?>
						<tr>
							<th><?php esc_html_e( 'Product Name', 'buy-x-get-y-promo' ) ; ?></th>
							<th><?php esc_html_e( 'Quantity', 'buy-x-get-y-promo' ) ; ?></th>
							<th><?php esc_html_e( 'Original Price', 'buy-x-get-y-promo' ) ; ?></th>
							<th><?php esc_html_e( 'Rule', 'buy-x-get-y-promo' ) ; ?></th>
						</tr>
						<?php
						foreach ( $product_details as $product_detail ) {
							?>
							<tr>
								<td><?php echo esc_html( $product_detail[ 'product_name' ] ) ; ?></td>
								<td><?php echo esc_html( $product_detail[ 'quantity' ] ) ; ?></td>
								<td><?php fgf_price( $product_detail[ 'product_price' ] ) ; ?></td>
								<td>
									<?php
									if ( ! empty( $product_detail[ 'rule_id' ] ) ) {
										echo wp_kses_post(  ! empty( get_the_title( $product_detail[ 'rule_id' ] ) ) ? get_the_title( $product_detail[ 'rule_id' ] ) : __( 'Rule not available', 'buy-x-get-y-promo' )  ) ;
									} else {
										esc_html_e( 'Manual', 'buy-x-get-y-promo' ) ;
									}
									?>
								</td>
							</tr>
						<?php } ?>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
