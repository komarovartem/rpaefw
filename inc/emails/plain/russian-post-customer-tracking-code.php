<?php
/**
 * Russian Post Tracking Email
 *
 * @package Russian Post/Email
 */

defined( 'ABSPATH' ) || exit;

echo "= " . $email_heading . " =\n\n";

printf( esc_html__( 'Hi %s,', 'russian-post-and-ems-for-woocommerce' ), esc_html( $order->get_billing_first_name() ) );
echo "\n\n";

	esc_html_e( 'Your order was sent by Russian Post', 'russian-post-and-ems-for-woocommerce' );

	echo ' ';
	esc_html_e( 'and it has the following tracking number:', 'russian-post-and-ems-for-woocommerce' );

echo "\n\n";

echo "----------\n\n";

echo "№: $tracking_number" . "\n\n";

echo "----------\n\n";

esc_html_e( 'You can track your package on the official website.', 'russian-post-and-ems-for-woocommerce' );
echo "\n\n";
echo 'https://www.pochta.ru/tracking#' . $tracking_number;
echo "\n\n";

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

// do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

if ( $additional_content ) {
	echo esc_html( wp_strip_all_tags( wptexturize( $additional_content ) ) );
	echo '\n\n----------------------------------------\n\n';
}

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
