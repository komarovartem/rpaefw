<?php
/**
 * Russian Post Tracking Email
 *
 * @package Russian Post/Email
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php /* translators: %s: Customer first name */ ?>
<p><?php printf( esc_html__( 'Hi %s,', 'russian-post-and-ems-for-woocommerce' ), esc_html( $order->get_billing_first_name() ) ); ?></p>
<p>
<?php
	esc_html_e( 'Your order was sent by Russian Post', 'russian-post-and-ems-for-woocommerce' );

	echo ' ';
		esc_html_e( 'and it has the following tracking number:', 'russian-post-and-ems-for-woocommerce' );
?>
</p>
<h3>№: <?php echo esc_html( $tracking_number ); ?></h3>
<p></p>
<p><?php esc_html_e( 'You can track your package on the official website.', 'russian-post-and-ems-for-woocommerce' ); ?> <a href="https://www.pochta.ru/tracking#<?php echo esc_html( $tracking_number ); ?>"><?php esc_html_e( 'Track Shipments', 'russian-post-and-ems-for-woocommerce' ); ?></a></p>
<br><br>

<?php

do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

if ( $additional_content ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

do_action( 'woocommerce_email_footer', $email );
