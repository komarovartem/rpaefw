<?php
/**
 * Russian Post Tracking Email
 *
 * @package Russian Post/Email
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'RPAEFW_Tracking_Code', false ) ) :

	/**
	 * Customer Tracking Code Email.
	 */
	class RPAEFW_Tracking_Code extends WC_Email {
		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id             = 'rpaefw_postcode_tracking_number';
			$this->customer_email = true;

			$this->title          = esc_html__( 'Russian Post Tracking Code', 'russian-post-and-ems-for-woocommerce' );
			$this->description    = esc_html__( 'Letters are sent to the client with the tracking code through the order page.', 'russian-post-and-ems-for-woocommerce' );
			$this->template_html  = 'emails/russian-post-customer-tracking-code.php';
			$this->template_plain = 'emails/plain/russian-post-customer-tracking-code.php';
			$this->template_base  = plugin_dir_path( __FILE__ );
			$this->placeholders   = array(
				'{order_date}'   => '',
				'{order_number}' => '',
			);

			parent::__construct();
		}

		/**
		 * Trigger the sending of this email.
		 *
		 * @param int            $order_id The order ID.
		 * @param WC_Order|false $order Order object.
		 */
		public function trigger( $order_id, $order = false ) {
			$this->setup_locale();

			if ( $order_id && ! is_a( $order, 'WC_Order' ) ) {
				$order = wc_get_order( $order_id );
			}

			if ( is_a( $order, 'WC_Order' ) ) {
				$this->object                         = $order;
				$this->recipient                      = $this->object->get_billing_email();
				$this->placeholders['{order_date}']   = wc_format_datetime( $this->object->get_date_created() );
				$this->placeholders['{order_number}'] = $this->object->get_order_number();
			}

			if ( $this->is_enabled() && $this->get_recipient() ) {
				$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
			}

			$this->restore_locale();
		}

		/**
		 * Get content html.
		 *
		 * @return string
		 */
		public function get_content_html() {
			return wc_get_template_html(
				$this->template_html,
				$this->get_template_args( false ),
				'',
				$this->template_base
			);
		}

		/**
		 * Get content plain.
		 *
		 * @return string
		 */
		public function get_content_plain() {
			return wc_get_template_html(
				$this->template_plain,
				$this->get_template_args( true ),
				'',
				$this->template_base
			);
		}

		/**
		 * Get email subject.
		 *
		 * @return string
		 */
		public function get_default_subject() {
			return esc_html__( 'Order sent from site {site_title}', 'russian-post-and-ems-for-woocommerce' );
		}

		/**
		 * Get email heading.
		 *
		 * @return string
		 */
		public function get_default_heading() {
			return esc_html__( 'Your order has been sent', 'russian-post-and-ems-for-woocommerce' );
		}

		/**
		 * Default content to show below main email content.
		 *
		 * @return string
		 */
		public function get_default_additional_content() {
			return __( 'Thanks for using {site_address}!', 'cdek-pro-for-woocommerce' );
		}

		/**
		 * Template arguments for email content.
		 *
		 * @param bool $is_plain Use plain text.
		 *
		 * @return array
		 */
		public function get_template_args( $is_plain ) {
			return array(
				'order'              => $this->object,
				'email_heading'      => $this->get_default_heading(),
				'additional_content' => $this->get_additional_content(),
				'tracking_number'    => get_post_meta( $this->object->get_id(), '_post_tracking_number', true ),
				'sent_to_admin'      => false,
				'plain_text'         => $is_plain,
				'email'              => $this,
			);
		}
	}

endif;

return new RPAEFW_Tracking_Code();
