<?php
/*
Plugin Name: Russian Post and EMS for WooCommerce
Description: The plugin allows you to automatically calculate the shipping cost for "Russian Post" or "EMS"
Version: 1.2.6
Author: Artem Komarov
Author URI: mailto:yumecommerce@gmail.com
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: russian-post-and-ems-for-woocommerce
WC requires at least: 3.0.0
WC tested up to: 3.8
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RPAEFW {
	public function __construct() {
		// apply plugin textdomain
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		// add new order status
		add_action( 'init', array( $this, 'register_order_confirmed_order_status' ) );
		add_filter( 'wc_order_statuses', array( $this, 'add_order_confirmed_to_order_statuses' ), 10, 1 );

		// add email template for tracking code
		add_filter( 'woocommerce_email_classes', array( $this, 'expedited_woocommerce_email' ) );
		add_filter( 'woocommerce_email_actions', array( $this, 'woocommerce_email_add_actions' ) );

		// tracking code meta box and email sending
		add_action( 'add_meta_boxes', array( $this, 'add_meta_tracking_code_box' ) );
		add_action( 'woocommerce_process_shop_order_meta', array( $this, 'save_tracking_code' ), 0, 2 );

		// add new shipping method
		add_action( 'woocommerce_shipping_init', array( $this, 'init_method' ) );
		add_filter( 'woocommerce_shipping_methods', array( $this, 'register_method' ) );

		// validate postcode for Russia
		add_filter( 'woocommerce_validate_postcode', array( $this, 'validate_postcode' ), 10, 3 );

		add_filter( 'woocommerce_get_sections_shipping', array( $this, 'settings_page' ) );
		add_filter( 'woocommerce_get_settings_shipping', array( $this, 'settings' ), 10, 2 );
	}

	/**
	 * Load textdomain for a plugin
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'russian-post-and-ems-for-woocommerce' );

//		include_once( dirname( __FILE__ ) . '/inc/class-rpaefw-ekom.php' );
	}

	/**
	 * Register new status
	 */
	public function register_order_confirmed_order_status() {
		register_post_status( 'wc-delivering', array(
			'label'                     => esc_html__( 'Delivering', 'russian-post-and-ems-for-woocommerce' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => false
		) );
	}

	/**
	 * Post Data shortcode
	 *
	 * @param $order_statuses
	 *
	 * @return array
	 */
	public function add_order_confirmed_to_order_statuses( $order_statuses ) {
		$new_order_statuses = array();
		// add new order status after processing
		foreach ( $order_statuses as $key => $status ) {
			$new_order_statuses[ $key ] = $status;
			if ( 'wc-processing' === $key ) {
				$new_order_statuses[ 'wc-delivering' ] = esc_html__( 'Delivering', 'russian-post-and-ems-for-woocommerce' );
			}
		}

		return $new_order_statuses;
	}

	/**
	 * Add new email class to woocommerce emails tab
	 *
	 * @param $email_classes
	 *
	 * @return array
	 */
	public function expedited_woocommerce_email( $email_classes ) {
		if ( ! class_exists( 'RPAEFW_Postcode_Tracking_Code_Class' ) ) {
			$email_classes[ 'RPAEFW_Postcode_Tracking_Code_Class' ] = include_once( dirname( __FILE__ ) . '/inc/class-postcode-tracking-code.php' );
		}

		return $email_classes;
	}

	/**
	 * Add trigger action
	 *
	 * @param $actions
	 *
	 * @return array
	 */
	function woocommerce_email_add_actions( $actions ) {
		$actions[] = 'rpaefw_tracking_code_send';

		return $actions;
	}

	/**
	 * Add meta box with tracking code
	 */
	function add_meta_tracking_code_box() {
		add_meta_box( 'rpaefw_meta_tracking_code', esc_html__( 'Tracking Code', 'russian-post-and-ems-for-woocommerce' ), array(
			$this,
			'tracking_code_meta_box'
		), 'shop_order', 'side', 'default' );
	}

	/**
	 * Print HTML form for meta box
	 */
	function tracking_code_meta_box() {
		global $post;

		$post_tracking_number = sanitize_text_field( get_post_meta( $post->ID, 'post_tracking_number', true ) );
		$ems_tracking_number  = sanitize_text_field( get_post_meta( $post->ID, 'ems_tracking_number', true ) );

		echo '<p><label for="rpaefw_postcode_tracking_provider" style="width: 50px; display: inline-block;">' . esc_html__( 'Russian Post', 'russian-post-and-ems-for-woocommerce' ) . ':</label>';
		echo '<input type="text" id="rpaefw_postcode_tracking_provider" name="rpaefw_postcode_tracking_provider" value="' . $post_tracking_number . '"/></p>';
		echo '<p><label for="rpaefw_ems_tracking_provider" style="width: 50px; display: inline-block;">EMS:</label>';
		echo '<input type="text" id="rpaefw_ems_tracking_provider" name="rpaefw_ems_tracking_provider" value="' . $ems_tracking_number . '"/></p>';
		echo '<p><input type="submit" class="add_note button" name="save" value="' . esc_html__( 'Save and send', 'russian-post-and-ems-for-woocommerce' ) . '"></p>';
	}

	/**
	 * Save tracking code and send email
	 *
	 * @param $post_id
	 *
	 */
	function save_tracking_code( $post_id ) {
		if ( isset( $_POST[ 'save' ] ) && $_POST[ 'save' ] != esc_html__( 'Save and send', 'russian-post-and-ems-for-woocommerce' ) ) {
			return;
		}

		if ( $_POST[ 'rpaefw_postcode_tracking_provider' ] != '' || $_POST[ 'rpaefw_ems_tracking_provider' ] != '' ) {
			$post_tracking_number = sanitize_text_field( $_POST[ 'rpaefw_postcode_tracking_provider' ] );
			$ems_tracking_number  = sanitize_text_field( $_POST[ 'rpaefw_ems_tracking_provider' ] );

			update_post_meta( $post_id, 'post_tracking_number', $post_tracking_number );
			update_post_meta( $post_id, 'ems_tracking_number', $ems_tracking_number );

			$comment_post_ID    = $post_id;
			$comment_author_url = '';
			$comment_content    = sprintf( esc_html__( 'Email with tracking number: %s, was sent to customer', 'russian-post-and-ems-for-woocommerce' ), $post_tracking_number . $ems_tracking_number );
			$comment_agent      = 'WooCommerce';
			$comment_type       = 'order_note';
			$comment_parent     = 0;
			$comment_approved   = 1;
			$commentdata        = apply_filters( 'woocommerce_new_order_note_data', compact( 'comment_post_ID', 'comment_author_url', 'comment_content', 'comment_agent', 'comment_type', 'comment_parent', 'comment_approved' ), array(
				'order_id'         => $post_id,
				'is_customer_note' => 0
			) );

			wp_insert_comment( $commentdata );

			WC()->mailer();

			do_action( 'rpaefw_tracking_code_send', array(
				'order_id'      => $post_id,
				'customer_note' => $post_tracking_number . $ems_tracking_number,
				'ems_field'     => $ems_tracking_number == '' ? false : true
			) );
		}
	}

	/**
	 * Add shipping method
	 *
	 * @param $methods
	 *
	 * @return array
	 *
	 */
	function init_method() {
		if ( ! class_exists( 'RPAEFW_Shipping_Method' ) ) {
			include_once( dirname( __FILE__ ) . '/inc/class-rpaefw-shipping-method.php' );
		}
	}

	/**
	 * Register shipping method
	 *
	 * @param $methods
	 *
	 * @return array
	 *
	 */
	function register_method( $methods ) {
		$methods[ 'rpaefw_post_calc' ] = 'RPAEFW_Shipping_Method';

		return $methods;
	}


	/**
	 * Check if postcode is valid for Russia
	 *
	 * @param $valid
	 * @param $postcode
	 * @param $country
	 *
	 * @return bool
	 *
	 */
	function validate_postcode( $valid, $postcode, $country ) {
		if ( $country == 'RU' ) {
			$valid = (bool) preg_match( '/^([0-9]{6})$/i', $postcode );
		}

		return $valid;
	}

	public function settings_page( $sections ) {
		$sections[ 'rpaefw' ] = esc_html__( 'Russian Post', 'russian-post-and-ems-for-woocommerce' );

		return $sections;
	}

	public function settings( $settings, $current_section ) {
		if ( $current_section == 'rpaefw' ) {
			$settings = [
				[
					'title' => esc_html__( 'Russian Post', 'russian-post-and-ems-for-woocommerce' ),
					'desc'  => esc_html__( 'Title', 'russian-post-and-ems-for-woocommerce' ),
					'type'  => 'title',
					'id'    => 'rpaefw_shipping_options'
				],
				[
					'title' => 'Регион-ИНН-Договор',
					'desc'  => '<br> Укажите данные если вы используете методы предназначеные для корпоративных клиентов.  Договор, заключенный между корпоративным клиентом и ФГУП “Почта России”. Строка состоит из кода региона по Конституции РФ, ИНН предприятия и номера договора, разделенных знаком “-” (дефис).',
					'type'  => 'text',
					'id'    => 'rpaefw_dogovor'
				],
			];

			$settings[] = [
				'title' => 'Токен авторизации приложения',
				'desc'  => '<br> Доступно только в PRO версии. <br>Для интеграции с API Онлайн-сервиса «Отправка». Токен можно узнать в настройках <a href="https://otpravka.pochta.ru/settings#/api-settings" target="_blank">личного кабинета</a>',
				'type'  => 'text',
				'id'    => 'rpaefw_token',
				'custom_attributes' => [
					'disabled' => 'disabled'
				]
			];

			$settings[] = [
				'title' => 'Ключ авторизации пользователя',
				'desc'  => '<br> Доступно только в PRO версии. <br>Для интеграции с API Онлайн-сервиса «Отправка». Вы можете сгенерировать ключ авторизации <a href="https://otpravka.pochta.ru/specification#/authorization-key" target="_blank">здесь</a>',
				'type'  => 'text',
				'id'    => 'rpaefw_key',
				'custom_attributes' => [
					'disabled' => 'disabled'
				]
			];

			$settings = apply_filters( 'rpaefw_settings', $settings );

			$settings[] = array(
				'type' => 'sectionend',
				'id'   => 'rpaefw_shipping_options',
			);
		}

		return $settings;
	}

	public static function is_pro_active() {
		if ( in_array( 'russian-post-and-ems-pro-for-woocommerce/russian-post-and-ems-pro-for-woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			return true;
		}

		return false;
	}
}

// init plugin if woo is active
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	new RPAEFW();
}
