<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RPAEFW_EKOM {
	public function __construct() {
		add_action( 'woocommerce_after_shipping_rate', [ $this, 'add_select' ], 10, 2 );
	}

	public function add_select( $method, $index ) {
//		$method_settings = get_option( 'woocommerce_rpaefw_post_calc_' . $method->instance_id . '_settings' );
		// find EKOM shipping method
//		if ( ! in_array( $method_settings[ 'type' ], [ 53030, 53070 ] ) ) {
//			return;
//		}

//		echo ;
	}
}

new RPAEFW_EKOM();