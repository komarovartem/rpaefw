<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RPAEFW_Postcode_Tracking_Code_Class extends WC_Email {

	/**
	 * Customer note.
	 *
	 * @var string
	 */
	public $customer_note;
	public $recipientname;
	public $ems_field;

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->id             = 'rpaefw_postcode_tracking_number';
		$this->customer_email = true;
		$this->title          = esc_html__( 'Tracking Code', 'russian-post-and-ems-for-woocommerce' );
		$this->description    = esc_html__( 'Letters are sent to the client with the tracking code through the order page.', 'russian-post-and-ems-for-woocommerce' );
		$this->subject        = esc_html__( 'Order sent from site {site_title}', 'russian-post-and-ems-for-woocommerce' );
		$this->heading        = esc_html__( 'Your order has been sent', 'russian-post-and-ems-for-woocommerce' );

		// Triggers
		add_action( 'rpaefw_tracking_code_send', array( $this, 'trigger' ) );

		// Call parent constructor
		parent::__construct();
	}

	/**
	 * Trigger.
	 *
	 * @param array $args
	 */
	public function trigger( $args ) {

		if ( ! empty( $args ) ) {

			$defaults = array(
				'order_id'      => '',
				'customer_note' => '',
				'ems_field' => true,
			);

			$args = wp_parse_args( $args, $defaults );

			extract( $args );

			if ( $order_id && ( $this->object = wc_get_order( $order_id ) ) ) {
				$this->recipient               = $this->object->get_billing_email();
				$this->recipientname           = $this->object->get_billing_first_name();
				$this->customer_note           = $customer_note;
				$this->ems_field               = $ems_field;

				$this->find['order-number']    = '{order_number}';

				$this->replace['order-date']   = date_i18n( wc_date_format(), strtotime( $this->object->get_date_created() ) );
				$this->replace['order-number'] = $this->object->get_order_number();
			} else {
				return;
			}
		}

		if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
			return;
		}

		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
	}

	/**
	 * Get content html.
	 *
	 * @access public
	 * @return string
	 */
	public function get_content_html() {
		return wc_get_template_html( 'post-tracking-code.php', array(
			'order'         => $this->object,
			'email_heading' => $this->get_heading(),
			'customer_note' => $this->customer_note,
			'recipientname' => $this->recipientname,
			'ems_field'     => $this->ems_field,
			'sent_to_admin' => false,
			'plain_text'    => false,
			'email'			=> $this
		), plugin_dir_path( __FILE__ ).'emails/', plugin_dir_path( __FILE__ ).'emails/' );
	}

	/**
	 * Get content plain.
	 *
	 * @access public
	 * @return string
	 */
	public function get_content_plain() {
		return wc_get_template_html( 'post-tracking-code.php', array(
			'order'         => $this->object,
			'email_heading' => $this->get_heading(),
			'customer_note' => $this->customer_note,
			'recipientname' => $this->recipientname,
			'ems_field'     => $this->ems_field,
			'sent_to_admin' => false,
			'plain_text'    => true,
			'email'			=> $this
		), plugin_dir_path( __FILE__ ).'emails/plain/', plugin_dir_path( __FILE__ ).'emails/plain/' );
	}
}

return new RPAEFW_Postcode_Tracking_Code_Class();
