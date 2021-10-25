<?php
/**
 * Plugin Name: Russian Post and EMS for WooCommerce
 * Description: The plugin allows you to automatically calculate the shipping cost for "Russian Post" or "EMS"
 * Version: 1.3.8
 * Author: Artem Komarov
 * Plugin URI: https://woocommerce.com/
 * Author URI: mailto:yumecommerce@gmail.com
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: russian-post-and-ems-for-woocommerce
 * WC requires at least: 4.0
 * WC tested up to: 5.8
 *
 * @package Russian Post
 */

defined( 'ABSPATH' ) || exit;

/**
 * Russian Post main class
 */
class RPAEFW {
	/**
	 * Constructor.
	 */
	public function __construct() {
		// apply plugin textdomain.
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		// add new order status.
		add_action( 'init', array( $this, 'register_delivering_status' ) );
		add_filter( 'wc_order_statuses', array( $this, 'add_order_status' ), 10, 1 );

		// add email template for tracking code.
		add_filter( 'woocommerce_email_classes', array( $this, 'expedited_woocommerce_email' ) );

		// tracking code meta box and email sending.
		add_action( 'add_meta_boxes', array( $this, 'add_meta_tracking_code_box' ), 10, 2 );
		add_action( 'woocommerce_process_shop_order_meta', array( $this, 'save_tracking_code' ), 0, 2 );

		// add new shipping method.
		add_action( 'woocommerce_shipping_init', array( $this, 'init_method' ) );
		add_filter( 'woocommerce_shipping_methods', array( $this, 'register_method' ) );

		add_filter( 'woocommerce_get_sections_shipping', array( $this, 'settings_page' ) );
		add_filter( 'woocommerce_get_settings_shipping', array( $this, 'settings' ), 10, 2 );

		add_filter( 'auto_update_plugin', array( $this, 'auto_update_plugin' ), 10, 2 );
		add_action( 'woocommerce_debug_tools', array( $this, 'add_debug_tools' ) );

		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );

		$this->init();
	}

	/**
	 * Load textdomain for a plugin
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'russian-post-and-ems-for-woocommerce' );
	}

	/**
	 * Register new status
	 */
	public function register_delivering_status() {
		register_post_status(
			'wc-delivering',
			array(
				'label'                     => esc_html__( 'Delivering', 'russian-post-and-ems-for-woocommerce' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => false,
			)
		);
	}

	/**
	 * Add new order status
	 *
	 * @param array $order_statuses Order statuses.
	 *
	 * @return array
	 */
	public function add_order_status( $order_statuses ) {
		$new_order_statuses = array();
		// add new order status after processing.
		foreach ( $order_statuses as $key => $status ) {
			$new_order_statuses[ $key ] = $status;
			if ( 'wc-processing' === $key ) {
				$new_order_statuses['wc-delivering'] = esc_html__( 'Delivering', 'russian-post-and-ems-for-woocommerce' );
			}
		}

		return $new_order_statuses;
	}

	/**
	 * Add new email class to woocommerce emails tab
	 *
	 * @param array $email_classes All email classes.
	 *
	 * @return array
	 */
	public function expedited_woocommerce_email( $email_classes ) {
		if ( ! class_exists( 'RPAEFW_Tracking_Code' ) ) {
			$email_classes['RPAEFW_Tracking_Code'] = include_once dirname( __FILE__ ) . '/inc/class-rpaefw-tracking-code.php';
		}

		return $email_classes;
	}

	/**
	 * Add meta box with tracking code
	 *
	 * @param string $post_type Post type.
	 * @param object $post Order object.
	 */
	public function add_meta_tracking_code_box( $post_type, $post ) {
		if ( 'shop_order' !== $post_type ) {
			return;
		}

		$order            = wc_get_order( $post );
		$shipping_methods = $order->get_shipping_methods();

		if ( ! $shipping_methods ) {
			return;
		}

		// do not display box if russian post shipping is not used.
		foreach ( $shipping_methods as $shipping ) {
			if ( ! in_array( $shipping->get_method_id(), array( 'rpaefw_post_calc', 'free_shipping' ), true ) ) {
				return;
			}
		}

		add_meta_box(
			'rpaefw_meta_tracking_code',
			esc_html__( 'Russian Post Tracking', 'russian-post-and-ems-for-woocommerce' ),
			array(
				$this,
				'tracking_code_meta_box',
			),
			'shop_order',
			'side',
			'default'
		);
	}

	/**
	 * Print HTML form for meta box
	 */
	public function tracking_code_meta_box() {
		global $post;

		$post_tracking_number = sanitize_text_field( get_post_meta( $post->ID, '_post_tracking_number', true ) );
		$option               = get_option( 'rpaefw_use_auto_email_tracking_code' );

		if ( $option && self::is_pro_active() ) {
			/* translators: links */
			echo sprintf( esc_html__( 'The email with track number is sending automatically based on your %1$s settings. %2$s', 'russian-post-and-ems-for-woocommerce' ), '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping&section=rpaefw' ) . '" target="_blank">', '</a>' );
		} else {
			echo '<p><label for="rpaefw_postcode_tracking_provider" style="width: 50px; display: inline-block;">' . esc_html__( 'Code', 'russian-post-and-ems-for-woocommerce' ) . ':</label>';
			echo '<input type="text" id="rpaefw_postcode_tracking_provider" name="rpaefw_postcode_tracking_provider" value="' . esc_attr( $post_tracking_number ) . '"/></p>';
			echo '<p><input type="submit" class="add_note button" name="save" value="' . esc_html__( 'Save and send', 'russian-post-and-ems-for-woocommerce' ) . '"></p>';
			if ( ! self::is_pro_active() ) {
				echo '<p>';
				/* translators: links */
				echo sprintf( esc_html__( '%1$s Get PRO version %2$s to automatically synchronize order with the tracking.', 'russian-post-and-ems-for-woocommerce' ), '<a href="https://woocommerce.com/products/russian-post-and-ems-pro-for-woocommerce/" target="_blank">', '</a>' );
				echo '</p>';
			}
		}

		do_action( 'rpaefw_after_tracking_meta_box', $post->ID );
	}

	/**
	 * Save tracking code
	 *
	 * @param int $post_id Post ID.
	 */
	public function save_tracking_code( $post_id ) {
		$tracking_number = isset( $_POST['rpaefw_postcode_tracking_provider'] ) ? sanitize_text_field( wp_unslash( $_POST['rpaefw_postcode_tracking_provider'] ) ) : false; // phpcs:ignore WordPress.Security.NonceVerification -- Nonce already verified

		if ( ! $tracking_number ) {
			return;
		}

		$saved_tracking_number = get_post_meta( $post_id, '_post_tracking_number', true );

		if ( $saved_tracking_number === $tracking_number ) {
			return;
		}

		update_post_meta( $post_id, '_post_tracking_number', $tracking_number );

		self::send_tracking_code( $post_id, $tracking_number );
	}

	/**
	 * Send tracking code
	 *
	 * @param int $post_id Order ID.
	 * @param int $tracking_number Tracking code.
	 */
	public static function send_tracking_code( $post_id, $tracking_number ) {
		$order = wc_get_order( $post_id );

		/* translators: tracking number */
		$order->add_order_note( sprintf( esc_html__( 'Email with tracking number: %s, was sent to customer', 'russian-post-and-ems-for-woocommerce' ), $tracking_number ) );

		WC()->mailer()->emails['RPAEFW_Tracking_Code']->trigger( $post_id );
	}

	/**
	 * Add shipping method
	 */
	public function init_method() {
		if ( ! class_exists( 'RPAEFW_Shipping_Method' ) ) {
			include_once dirname( __FILE__ ) . '/inc/class-rpaefw-shipping-method.php';
		}
	}

	/**
	 * Register shipping method
	 *
	 * @param array $methods Shipping methods.
	 *
	 * @return array
	 */
	public function register_method( $methods ) {
		$methods['rpaefw_post_calc'] = 'RPAEFW_Shipping_Method';

		return $methods;
	}

	/**
	 * Register settings page
	 *
	 * @param array $sections Admin sections.
	 *
	 * @return mixed
	 */
	public function settings_page( $sections ) {
		$sections['rpaefw'] = esc_html__( 'Russian Post', 'russian-post-and-ems-for-woocommerce' );

		return $sections;
	}

	/**
	 * Main settings page
	 *
	 * @param array  $settings Admin settings.
	 * @param string $current_section Current section.
	 *
	 * @return array|mixed
	 */
	public function settings( $settings, $current_section ) {
		if ( 'rpaefw' === $current_section ) {
			$settings = include 'inc/settings/settings-admin-section.php';
		}

		return $settings;
	}

	/**
	 * Auto update plugin
	 *
	 * @param bool   $should_update If should update.
	 * @param object $plugin Plugin data.
	 *
	 * @return bool
	 */
	public function auto_update_plugin( $should_update, $plugin ) {
		if ( 'russian-post-and-ems-for-woocommerce/russian-post-and-ems-for-woocommerce.php' === $plugin->plugin ) {
			return true;
		}

		return $should_update;
	}

	/**
	 * Add debug tools
	 *
	 * @param array $tools List of available tools.
	 *
	 * @return array
	 */
	public function add_debug_tools( $tools ) {
		$tools['rpaefw_clear_transients'] = array(
			'name'     => __( 'Russian Post transients', 'russian-post-and-ems-for-woocommerce' ),
			'button'   => __( 'Clear transients', 'russian-post-and-ems-for-woocommerce' ),
			'desc'     => __( 'This tool will clear the request transients cache.', 'russian-post-and-ems-for-woocommerce' ),
			'callback' => array( $this, 'clear_transients' ),
		);

		return $tools;
	}

	/**
	 * Callback to clear transients
	 *
	 * @return string
	 */
	public function clear_transients() {
		global $wpdb;
		$transient_names = array( 'rpaefw_currency_rates', 'rpaefw_auto_sync_pvz', 'rpaefw_auto_change_order_status' );

		foreach ( $transient_names as $name ) {
			delete_transient( $name );
		}

		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '%_rpaefw_cache_%'" );

		return __( 'Transients cleared', 'russian-post-and-ems-for-woocommerce' );
	}

	/**
	 * Display helpful links
	 *
	 * @param array $links Key - link pair.
	 *
	 * @return array
	 */
	public function plugin_action_links( $links ) {
		$settings = array( 'settings' => '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping&section=rpaefw' ) . '">' . esc_html__( 'Settings', 'russian-post-and-ems-for-woocommerce' ) . '</a>' );

		$links = $settings + $links;

		if ( self::is_pro_active() ) {
			return $links;
		}

		$links['pro'] = '<a href="https://woocommerce.com/products/russian-post-and-ems-pro-for-woocommerce/" target="_blank" style="color: #96588a">' . esc_html__( 'Buy PRO version', 'russian-post-and-ems-for-woocommerce' ) . '</a>';

		return $links;
	}

	/**
	 * Check if PRO plugin active
	 * Used in many places to load PRO content and functionality
	 *
	 * @return bool
	 */
	public static function is_pro_active() {
		if ( in_array( 'russian-post-and-ems-pro-for-woocommerce/russian-post-and-ems-pro-for-woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Helper function to avoid typing same strings
	 *
	 * @return string
	 */
	public static function only_in_pro_ver_text() {
		return self::is_pro_active() ? '' : 'Доступно только в PRO версии. ';
	}

	/**
	 * Plugin dir url helper
	 *
	 * @return string
	 */
	public static function plugin_dir_url() {
		return plugin_dir_url( __FILE__ );
	}

	/**
	 * Add plugin partials
	 */
	public function init() {
		include_once dirname( __FILE__ ) . '/inc/class-rpaefw-promo.php';
	}
}

// Init plugin if woo is active.
if ( in_array(
	'woocommerce/woocommerce.php',
	apply_filters( 'active_plugins', get_option( 'active_plugins' ) ),
	true
) ) {
	new RPAEFW();
}
