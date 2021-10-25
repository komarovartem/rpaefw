<?php
/**
 * Russian Post shipping method
 *
 * @package Russian Post/Shipping
 */

defined( 'ABSPATH' ) || exit;

/**
 * RPAEFW_Shipping_Method.
 */
class RPAEFW_Shipping_Method extends WC_Shipping_Method {
	/**
	 * RPAEFW_Shipping_Method constructor.
	 *
	 * @param int $instance_id id.
	 */
	public function __construct( $instance_id = 0 ) {
		$this->id                 = 'rpaefw_post_calc';
		$this->instance_id        = absint( $instance_id );
		$this->method_title       = esc_html__( 'Russian Post', 'russian-post-and-ems-for-woocommerce' );
		$this->method_description = esc_html__( 'The plugin allows you to automatically calculate shipping costs "Russian Post" or "EMS" on the checkout page using official tariff.pochta.ru service.', 'russian-post-and-ems-for-woocommerce' );
		$this->supports           = array(
			'shipping-zones',
			'instance-settings',
		);

		$settings                   = include 'settings/settings-shipping-method.php';
		$this->instance_form_fields = $settings;

		foreach ( $settings as $key => $settings ) {
			$this->{$key} = $this->get_option( $key );
		}

		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	/**
	 * Calculate_shipping function.
	 *
	 * @param array $package (default: array()).
	 */
	public function calculate_shipping( $package = array() ) {
		global $woocommerce;

		$type = $this->type;

		// old version plugin types support.
		if ( $type && ! is_numeric( $type ) ) {
			$new_type = $this->get_new_id_shipping_type( $type );

			$type = $new_type ? $new_type : 27030;
		} else {
			$type = intval( $type );
		}

		$ops_index    = get_option( 'rpaefw_ops_index' ) ? get_option( 'rpaefw_ops_index' ) : get_option( 'woocommerce_store_postcode' );
		$from         = $this->from ? $this->from : $ops_index;
		$is_ekom      = in_array( $type, array( 53030, 53070, 54020 ) );
		$dogovor      = get_option( 'rpaefw_dogovor' );
		$country_code = $package['destination']['country'] ? $package['destination']['country'] : 'RU';
		$postal_code  = wc_format_postcode( $package['destination']['postcode'], $country_code );
		$state        = $package['destination']['state'];
		$city         = $package['destination']['city'];
		$services     = ( isset( $this->service ) && ! empty( $this->service ) ) ? $this->service : array();
		$to           = '';
		$time         = '';
		$time_pro     = ''; // store time here in case PRO.

		// get weight of the cart.
		// normalise weights, unify to g.
		$weight = ceil( wc_get_weight( $woocommerce->cart->cart_contents_weight, 'g' ) );

		// plus pack weight.
		$weight += intval( $this->addpackweight );

		// check weight and set minimum value for Russian Post if it is less than required.
		$weight = $weight < 100 ? 100 : $weight;

		// get total value.
		$total_val = intval( $package['cart_subtotal'] );

		// additional cost.
		$addcost     = intval( $this->addcost );
		$addpackcost = intval( $this->addpackcost );

		// check if default currency is different from RUB.
		$currency = $this->get_store_currency();

		// origin postcode must be set.
		if ( ! $from ) {
			$this->maybe_print_error( __( 'Post code of sender is required. You can set in store address.', 'russian-post-and-ems-for-woocommerce' ) );

			return;
		}

		// force display method before initial calculation.
		if ( isset( $this->force_display ) && $this->force_display === 'yes' && ! $postal_code && ! $city ) {
			$this->add_rate(
				array(
					'id'    => $this->get_rate_id(),
					'label' => $this->title . ( ( trim( $this->force_display_info ) != '' ) ? ' (' . $this->force_display_info . ')' : '' ),
					'cost'  => 0,
				)
			);

			return;
		} elseif ( ! $postal_code && 'RU' === $country_code && ! RPAEFW::is_pro_active() ) {
			return;
		}

		// change type if COD is selected.
		$type = $this->match_shipping_type_based_on_payment( $type );

		// if postal code is empty take city.
		if ( 'RU' === $country_code ) {
			if ( RPAEFW::is_pro_active() ) {
				// if not ekom since it will be calculated later.
				if ( ! $is_ekom ) {
					$to = $this->get_index_based_on_address( $state, $city );
					$to = $to ? $to : $postal_code;

					if ( ! $to ) {
						return;
					}
				}
			} else {
				$to = $postal_code;
			}
		}

		// change postcode to EKOM index if it is selected.
		if ( $is_ekom ) {
			// add SMS service which is required but not calculated for some reason via tariff website.
			$services[] = 44;
			if ( RPAEFW::is_pro_active() ) {
				$to = intval( $this->get_ekom_index( $state, $city, $type, $services ) );
				if ( ! $to ) {
					return;
				}
			} else {
				return;
			}
		}

		// check and avoid package overweight before make api request.
		$is_overweight = false;
		$weight_ranges = array(
			2000  => array( 5001, 5011 ),
			2500  => array( 16010, 16020 ),
			5000  => array( 3000, 3010, 3020, 3001, 3011, 47030, 47020 ),
			10000 => array( 27030, 27020, 29030, 29020, 28030, 28020 ),
			14500 => array( 9001, 9011 ),
			20000 => array( 23030, 23020, 51030, 51020, 34030, 34020, 4031, 4021, 53030, 53070, 54020 ),
			31500 => array( 24030, 24020, 30030, 30020, 7030, 7020, 41030, 41020, 7031 ),
			50000 => array( 4030, 4020 ),
		);

		foreach ( $weight_ranges as $max_val => $types_range ) {
			if ( in_array( $type, $types_range ) && $weight >= $max_val ) {
				$is_overweight = true;
				break;
			}
		}

		// check conditional weights.
		if ( ( $this->cond_min_weight && $weight < intval( $this->cond_min_weight ) ) || ( $this->cond_max_weight && $weight > intval( $this->cond_max_weight ) ) ) {
			return;
		}

		if ( $is_overweight ) {
			$this->maybe_print_error( __( 'Overweight is not allowed. To avoid overweight you can limit the range of allowed weights in settings of this shipping method.', 'russian-post-and-ems-for-woocommerce' ) );

			return;
		}

		// remove method if order has item with specific shipping class.
		if ( isset( $this->cond_has_shipping_class ) && $this->cond_has_shipping_class ) {
			$found_shipping_classes  = $this->find_shipping_classes( $package );
			$is_shipping_class_found = false;
			foreach ( $found_shipping_classes as $shipping_class => $products ) {
				$shipping_class_term = get_term_by( 'slug', $shipping_class, 'product_shipping_class' );
				if ( $shipping_class_term && $shipping_class_term->term_id && in_array( $shipping_class_term->term_id, $this->cond_has_shipping_class ) ) {
					$is_shipping_class_found = true;
					break;
				}
			}

			if ( $is_shipping_class_found ) {
				return;
			}
		}

		// if cost is less than provided in options.
		if ( $this->fixedvalue_disable != '' && intval( $this->fixedvalue_disable ) > 0 && $total_val < $this->fixedvalue_disable ) {
			return;
		}

		// fixed value.
		$fixedvalue = $this->fixedpackvalue;
		if ( $fixedvalue && $total_val > intval( $fixedvalue ) && ! $this->is_cod_used_as_payment() ) {
			$total_val = intval( $fixedvalue );
		}

		if ( 'RUB' !== $currency ) {
			$total_val = $this->get_currency_value( $currency, $total_val, false );
		}

		$total_val = ceil( $total_val * 100 );

		$base_params = array(
			'weight'    => ceil( $weight ),
			'sumoc'     => $total_val,
			'sumin'     => $total_val,
			'sum_month' => $total_val,
			'pack'      => isset( $this->pack ) ? $this->pack : 10,
			'isavia'    => isset( $this->isavia ) ? $this->isavia : 1,
			'from'      => $from,
			'object'    => $type,
			'nds'       => isset( $this->nds ) ? $this->nds : 'yes',
		);

		// add additional services.
		if ( $services ) {
			$base_params['service'] = implode( ',', $services );
		}

		if ( $dogovor ) {
			$base_params['dogovor'] = trim( $dogovor );
			$services[]             = 28; // corporate client.
		}

		// check if shipping goes abroad.
		if ( 'RU' !== $country_code ) {
			if ( ! in_array( $type, array( 4031, 4021, 7031, 5001, 5011, 9001, 9011, 3001, 3011 ) ) ) {
				$this->maybe_print_error( __( 'For international shipping you should select international type in settings.', 'russian-post-and-ems-for-woocommerce' ) );

				return;
			}

			if ( ! $country_code_number = $this->get_country_number( $country_code ) ) {
				$this->maybe_print_error( __( 'Could not find code number for given country.', 'russian-post-and-ems-for-woocommerce' ) );

				return;
			}

			$base_params['country'] = $country_code_number;
		} else {
			$base_params['to'] = $to;
		}

		if ( isset( $this->free_shipping ) && 'yes' === $this->free_shipping && RPAEFW::is_pro_active() ) {
			if ( RPAEFW_PRO_Helper::is_free_shipping_available( $this ) ) {
				$time                = $this->get_delivery_time( $country_code, $is_ekom, $from, $to, $type );
				$free_shipping_title = $this->free_shipping_custom_title ? $this->free_shipping_custom_title : $this->title;
				$this->add_rate(
					array(
						'id'      => $this->get_rate_id(),
						'label'   => $free_shipping_title . $time,
						'taxes'   => false,
						'package' => $package,
						'cost'    => 0,
					)
				);

				return;
			} else {
				if ( 'yes' === $this->free_shipping_hide_if_not_achieved ) {
					return;
				}
			}
		}

		$request      = add_query_arg( $base_params, 'https://tariff.pochta.ru/tariff/v2/calculate?json' );
		$request_hash = 'rpaefw_cache_' . md5( $request );

		if ( ! $shipping_cost = get_transient( $request_hash ) ) {
			if ( ! $shipping_cost = $this->get_data_from_api( $request, 'price', $is_ekom ) ) {
				return;
			}

			set_transient( $request_hash, $shipping_cost, DAY_IN_SECONDS );
		}

		// in case PRO plugin has return data.
		if ( is_array( $shipping_cost ) ) {
			// it could be request for international shipping which has not delivery time option.
			if ( isset( $shipping_cost['delivery-time'] ) ) {
				$time_pro = $shipping_cost['delivery-time']['max-days'];
			}

			if ( 'no' === $this->nds ) {
				$shipping_cost = $shipping_cost['total-rate'] / 100;
			} else {
				$shipping_cost = ( $shipping_cost['total-rate'] + $shipping_cost['total-vat'] ) / 100;
			}
		}

		$shipping_class_cost = $this->get_shipping_class_cost( $package );

		// shipping cost + packages cost + additional cost.
		$cost = ceil( $shipping_cost + $addcost + $addpackcost + $shipping_class_cost );

		if ( 'RUB' !== $currency ) {
			$cost = $this->get_currency_value( $currency, $cost );
		}

		// do not apply if no cost is set.
		if ( ! $cost ) {
			$this->maybe_print_error( __( 'Error: no cost is set.', 'russian-post-and-ems-for-woocommerce' ) );

			return;
		}

		// show delivery time.
		$time = $this->get_delivery_time( $country_code, $is_ekom, $from, $to, $type );

		$this->add_rate(
			array(
				'id'      => $this->get_rate_id(),
				'label'   => $this->title . $time,
				'cost'    => $cost,
				'package' => $package,
			)
		);
	}

	/**
	 * Get delivery time from API
	 *
	 * @param string  $country_code Country code.
	 * @param boolean $is_ekom EKOM.
	 * @param string  $from From index.
	 * @param string  $to To index.
	 * @param int     $type Shipping type.
	 *
	 * @return string
	 */
	public function get_delivery_time( $country_code, $is_ekom, $from, $to, $type ) {
		$time = '';

		if ( 'yes' === $this->time && 'RU' === $country_code && ! $is_ekom ) {
			$request = add_query_arg(
				array(
					'from'   => $from,
					'to'     => $to,
					'object' => $type,
				),
				'https://delivery.pochta.ru/delivery/v2/calculate?json'
			);

			$request_hash = 'rpaefw_cache_' . md5( $request );

			if ( ! $delivery_time = get_transient( $request_hash ) ) {
				if ( $delivery_time = $this->get_data_from_api( $request, 'time' ) ) {
					set_transient( $request_hash, $delivery_time, DAY_IN_SECONDS * 30 );
				}
			}

			if ( $delivery_time ) {
				if ( isset( $this->add_time ) && $this->add_time ) {
					$delivery_time += intval( $this->add_time );
				}

				$time = ' (' . sprintf( _n( '%s day', '%s days', $delivery_time, 'russian-post-and-ems-for-woocommerce' ), number_format_i18n( $delivery_time ) ) . ')';
			}
		}

		return $time;
	}

	/**
	 * Try to get index from PRO plugin base
	 *
	 * @param string $shipping_state state number.
	 * @param string $shipping_city city.
	 *
	 * @return bool|int
	 */
	public function get_index_based_on_address( $shipping_state, $shipping_city ) {
		if ( is_callable( 'RPAEFW_PRO_Ru_Base::get_index_based_on_address' ) ) {
			return RPAEFW_PRO_Ru_Base::get_index_based_on_address( $shipping_state, $shipping_city );
		}

		return false;
	}

	/**
	 * Match non cod like types of shipping and switch it to type with declared value or keep same if no match exists
	 *
	 * @param int $type shipping type.
	 *
	 * @return mixed
	 */
	public function match_shipping_type_based_on_payment( $type ) {
		if ( ! $this->is_cod_used_as_payment() ) {
			return $type;
		}

		$match_types = array(
			2000  => 2020,
			11000 => 2020,
			2010  => 2020,
			11010 => 2020,
			33010 => 2020,
			2020  => 2020,
			15000 => 15020,
			15010 => 15020,
			15020 => 15020,
			3000  => 3020,
			3010  => 3020,
			3020  => 3020,
			16010 => 16020,
			16020 => 16020,
			27030 => 27020,
			27020 => 27020,
			29030 => 29020,
			29020 => 29020,
			28030 => 28020,
			28020 => 28020,
			4030  => 4020,
			4020  => 4020,
			47030 => 47020,
			47020 => 47020,
			23030 => 23020,
			23020 => 23020,
			51030 => 51020,
			51020 => 51020,
			24030 => 24020,
			24020 => 24020,
			30030 => 30020,
			30020 => 30020,
			31030 => 31020,
			31020 => 31020,
			39000 => 39000,
			40000 => 40000,
			54020 => 53070,
			53030 => 53070,
			53070 => 53070,
			7030  => 7020,
			7020  => 7020,
			41030 => 41020,
			41020 => 41020,
			34030 => 34020,
			34020 => 34020,
			52030 => 52020,
			52020 => 52020,
			4031  => 4021,
			4021  => 4021,
			7031  => 7031,
			5001  => 5001,
			5011  => 5011,
			3001  => 3001,
			3011  => 3011,
			9001  => 9001,
			9011  => 9011,
		);

		return isset( $match_types[ $type ] ) ? $match_types[ $type ] : $type;

	}

	/**
	 * Check if COD is chosen for payment
	 *
	 * @return bool
	 */
	public function is_cod_used_as_payment() {
		if ( ! RPAEFW::is_pro_active() ) {
			return false;
		}

		if ( ! $chosen_payment_method = WC()->session->get( 'chosen_payment_method' ) ) {
			return false;
		}

		return in_array( $chosen_payment_method, array( 'cod', 'codpg_russian_post' ), true );
	}

	/**
	 * Find index for EKOM shipping type from PRO plugin
	 *
	 * @param string $shipping_state shipping state.
	 * @param string $shipping_city shipping city.
	 * @param int    $type shipping type.
	 * @param array  $services shipping services.
	 *
	 * @return bool|string
	 */
	public function get_ekom_index( $shipping_state, $shipping_city, $type, $services ) {
		if ( ! $file = fopen( WP_PLUGIN_DIR . '/russian-post-and-ems-pro-for-woocommerce/inc/post-data-base/pvz.txt', 'r' ) ) {
			$this->log_it( __( 'Could not open PVZ file to get EKOM index.', 'russian-post-and-ems-for-woocommerce' ), 'error' );

			return false;
		}

		$shipping_state        = intval( $shipping_state );
		$ekom_index            = '';
		$all_validated_indexes = array();
		$requirements          = array(
			'cash_payment'           => false,
			'contents_checking'      => false,
			'with_fitting'           => false,
			'functionality_checking' => false,
		);

		if ( ! $shipping_state || ! $shipping_city ) {
			$this->log_it( __( 'Shipping state or city is not provided to get EKOM index.', 'russian-post-and-ems-for-woocommerce' ), 'error' );

			return false;
		}

		if ( $type == 53070 ) {
			$requirements['cash_payment'] = true;
		}

		if ( in_array( 81, $services ) ) {
			$requirements['contents_checking'] = true;
		}

		if ( in_array( 82, $services ) ) {
			$requirements['with_fitting'] = true;
		}

		if ( in_array( 83, $services ) ) {
			$requirements['functionality_checking'] = true;
		}

		while ( ( $line = fgets( $file ) ) !== false ) {
			list( $index, $state, $city, $address, $coordinates, $card_payment, $cash_payment, $contents_checking, $functionality_checking, $with_fitting ) = explode( "\t", $line );
			if ( intval( $state ) === $shipping_state && $city === $shipping_city ) {
				$validated = true;

				foreach ( $requirements as $name => $need ) {
					if ( $validated && $need ) {
						if ( ! ${$name} ) {
							$validated = false;
						}
					}
				}

				if ( $validated ) {
					$all_validated_indexes[] = intval( $index );

					if ( '' === $ekom_index ) {
						$ekom_index = intval( $index );
					}
				}
			}
		}

		fclose( $file );

		if ( ! $ekom_index ) {
			/* translators: city and state */
			$this->log_it( sprintf( __( 'Could not find EKOM delivery point for the next address %1$s, type of EKOM %2$s, and services %3$s.', 'russian-post-and-ems-for-woocommerce' ), $shipping_state . ' ' . $shipping_city, $type, wp_json_encode( $services ) ) );
		}

		$post_data = isset( $_POST['post_data'] ) ? wp_unslash( $_POST['post_data'] ) : ''; // phpcs:ignore WordPress.Security

		if ( $ekom_index && $post_data ) {
			parse_str( $post_data, $data_output );
			if ( ! empty( $data_output['rpaefw_ekom_index'] ) ) {
				$posted_index = intval( $data_output['rpaefw_ekom_index'] );
				if ( in_array( $posted_index, $all_validated_indexes, true ) ) {
					return $posted_index;
				}
			}
		}

		return $ekom_index;
	}

	/**
	 * Check store currency and validate it
	 *
	 * @return string
	 */
	public function get_store_currency() {
		$currency       = get_option( 'woocommerce_currency', 'RUB' );
		$all_currencies = get_woocommerce_currencies();

		// validate currency since some users might have issue when it's not set properly.
		if ( isset( $all_currencies[ $currency ] ) ) {
			return $currency;
		}

		return 'RUB';
	}

	/**
	 * Add additional cost based on shipping classes
	 *
	 * @param array $package Shipping package.
	 *
	 * @return int
	 */
	public function get_shipping_class_cost( $package ) {
		$shipping_classes = WC()->shipping()->get_shipping_classes();
		$cost             = 0;

		if ( ! empty( $shipping_classes ) && isset( $this->class_cost_calc_type ) ) {
			$found_shipping_classes = $this->find_shipping_classes( $package );
			$highest_class_cost     = 0;

			foreach ( $found_shipping_classes as $shipping_class => $products ) {
				// Also handles BW compatibility when slugs were used instead of ids.
				$shipping_class_term = get_term_by( 'slug', $shipping_class, 'product_shipping_class' );
				$class_cost_string   = $shipping_class_term && $shipping_class_term->term_id ? $this->get_option( 'class_cost_' . $shipping_class_term->term_id, $this->get_option( 'class_cost_' . $shipping_class, '' ) ) : $this->get_option( 'no_class_cost', '' );

				if ( '' === $class_cost_string ) {
					continue;
				}

				$class_cost = $this->evaluate_cost(
					$class_cost_string,
					array(
						'qty'  => array_sum( wp_list_pluck( $products, 'quantity' ) ),
						'cost' => array_sum( wp_list_pluck( $products, 'line_total' ) ),
					)
				);

				if ( 'class' === $this->class_cost_calc_type ) {
					$cost += $class_cost;
				} else {
					$highest_class_cost = $class_cost > $highest_class_cost ? $class_cost : $highest_class_cost;
				}
			}

			if ( 'order' === $this->class_cost_calc_type && $highest_class_cost ) {
				$cost += $highest_class_cost;
			}
		}

		return $cost;
	}

	/**
	 * Work out fee (shortcode).
	 *
	 * @param array $atts Attributes.
	 *
	 * @return string
	 */
	public function fee( $atts ) {
		$atts = shortcode_atts(
			array(
				'percent' => '',
				'min_fee' => '',
				'max_fee' => '',
			),
			$atts,
			'fee'
		);

		$calculated_fee = 0;

		if ( $atts['percent'] ) {
			$calculated_fee = $this->fee_cost * ( floatval( $atts['percent'] ) / 100 );
		}

		if ( $atts['min_fee'] && $calculated_fee < $atts['min_fee'] ) {
			$calculated_fee = $atts['min_fee'];
		}

		if ( $atts['max_fee'] && $calculated_fee > $atts['max_fee'] ) {
			$calculated_fee = $atts['max_fee'];
		}

		return $calculated_fee;
	}


	/**
	 * Evaluate a cost from a sum/string.
	 *
	 * @param string $sum Sum of shipping.
	 * @param array  $args Args.
	 *
	 * @return string
	 */
	protected function evaluate_cost( $sum, $args = array() ) {
		include_once WC()->plugin_path() . '/includes/libraries/class-wc-eval-math.php';

		// Allow 3rd parties to process shipping cost arguments.
		$args           = apply_filters( 'woocommerce_evaluate_shipping_cost_args', $args, $sum, $this );
		$locale         = localeconv();
		$decimals       = array(
			wc_get_price_decimal_separator(),
			$locale['decimal_point'],
			$locale['mon_decimal_point'],
			',',
		);
		$this->fee_cost = $args['cost'];

		// Expand shortcodes.
		add_shortcode( 'fee', array( $this, 'fee' ) );

		$sum = do_shortcode(
			str_replace(
				array(
					'[qty]',
					'[cost]',
				),
				array(
					$args['qty'],
					$args['cost'],
				),
				$sum
			)
		);

		remove_shortcode( 'fee', array( $this, 'fee' ) );

		// Remove whitespace from string.
		$sum = preg_replace( '/\s+/', '', $sum );

		// Remove locale from string.
		$sum = str_replace( $decimals, '.', $sum );

		// Trim invalid start/end characters.
		$sum = rtrim( ltrim( $sum, "\t\n\r\0\x0B+*/" ), "\t\n\r\0\x0B+-*/" );

		// Do the math.
		return $sum ? WC_Eval_Math::evaluate( $sum ) : 0;
	}

	/**
	 * Finds and returns shipping classes and the products with said class.
	 *
	 * @param mixed $package Package of items from cart.
	 *
	 * @return array
	 */
	public function find_shipping_classes( $package ) {
		$found_shipping_classes = array();

		foreach ( $package['contents'] as $item_id => $values ) {
			if ( $values['data']->needs_shipping() ) {
				$found_class = $values['data']->get_shipping_class();

				if ( ! isset( $found_shipping_classes[ $found_class ] ) ) {
					$found_shipping_classes[ $found_class ] = array();
				}

				$found_shipping_classes[ $found_class ][ $item_id ] = $values;
			}
		}

		return $found_shipping_classes;
	}

	/**
	 * Get currency value based on CBR rate
	 *
	 * @param string  $currency Currency.
	 * @param float   $cost Cost.
	 * @param boolean $from_rub From RUB.
	 *
	 * @return int
	 */
	public function get_currency_value( $currency, $cost, $from_rub = true ) {
		// check if third party plugins are installed.
		if ( in_array( 'woocommerce-currency-switcher/index.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {
			$woocs = get_option( 'woocs' );

			if ( $woocs && is_array( $woocs ) && isset( $woocs['RUB'] ) ) {
				if ( $from_rub ) {
					return round( 1 / $woocs['RUB']['rate'] * $cost );
				} else {
					return round( $woocs['RUB']['rate'] * $cost );
				}
			}
		}

		if ( ! $rates = $this->get_currency_rates_from_api() ) {
			$this->maybe_print_error( __( 'Cannot get currency rates from api.', 'russian-post-and-ems-for-woocommerce' ) );

			return false;
		}

		$valute_obj = null;

		// find obj for provided currency.
		foreach ( $rates->Valute as $valute ) {
			if ( $valute->CharCode == $currency ) {
				$valute_obj = $valute;
				break;
			}
		}

		// if no match is found return false.
		if ( ! $valute_obj ) {
			$this->maybe_print_error( __( 'Could not find currency code in a base.', 'russian-post-and-ems-for-woocommerce' ) );

			return false;
		}

		$rate = str_replace( ',', '.', $valute_obj->Value );

		if ( $from_rub ) {
			return round( 1 / $rate * $cost );
		} else {
			return round( $rate * $cost );
		}
	}

	/**
	 * Get and update currency rate
	 *
	 * @return bool|mixed|SimpleXMLElement
	 */
	public function get_currency_rates_from_api() {
		$transient_name = 'rpaefw_currency_rates';

		if ( ! $rates = get_transient( $transient_name ) ) {
			// try to get rates from CBR
			if ( ! $rates = $this->get_data_from_api( 'https://www.cbr.ru/scripts/XML_daily_eng.asp', 'currency' ) ) {
				return false;
			}

			set_transient( $transient_name, $rates, DAY_IN_SECONDS );
		}

		// parse rates.
		$rates = simplexml_load_string( $rates );

		return $rates;
	}

	/**
	 * Connecting to the api server and get price
	 *
	 * @param string  $request Url for request.
	 * @param string  $get Type of req.
	 * @param boolean $is_ekom EKOM condition.
	 *
	 * @return mixed
	 */
	public function get_data_from_api( $request, $get, $is_ekom = false ) {
		$remote_response = wp_remote_get( $request, array( 'timeout' => 15 ) );
		$this->log_it( __( 'Making request to get:', 'russian-post-and-ems-for-woocommerce' ) . ' ' . $request );

		if ( is_wp_error( $remote_response ) ) {
			$error_message = __( 'Server connection error to get', 'russian-post-and-ems-for-woocommerce' ) . '"' . $get . '". ' . $remote_response->get_error_message();
			$this->log_it( $error_message, 'error' );
			$this->maybe_print_error( $error_message );

			return false;
		}

		$response_code = wp_remote_retrieve_response_code( $remote_response );

		if ( 200 !== $response_code ) {
			$error_message = __( 'Request error for', 'russian-post-and-ems-for-woocommerce' ) . '"' . $get . '". CODE: ' . $response_code . ' ' . wp_remote_retrieve_body( $remote_response );
			$this->log_it( $error_message, 'error' );
			$this->maybe_print_error( $error_message );

			return false;
		}

		$body = wp_remote_retrieve_body( $remote_response );

		if ( 'price' === $get || 'time' === $get ) {
			$response = json_decode( $body, true );

			if ( isset( $response['error'] ) ) {
				$error_message = __( 'Error:', 'russian-post-and-ems-for-woocommerce' ) . ' ' . $response['error'][0];
				$this->log_it( $error_message, 'error' );

				if ( $is_ekom ) {
					if ( $this->is_method_selected() ) {
						$this->maybe_print_error( __( 'Error:', 'russian-post-and-ems-for-woocommerce' ) . ' ' . __( 'could not calculate shipping rate for selected delivery point. Please select another delivery point.', 'russian-post-and-ems-for-woocommerce' ), false, false );
					} else {
						$this->maybe_print_error( '', false, false );
					}
				} else {
					$this->maybe_print_error( $error_message );
				}

				return false;
			}

			if ( 'price' === $get ) {
				if ( 'no' === $this->nds && RPAEFW::is_pro_active() ) {
					return $response['pay'] / 100;
				} else {
					return $response['paynds'] / 100;
				}
			} else {
				return $response['delivery']['max'];
			}
		}

		if ( 'currency' === $get ) {
			return $body;
		}

		return false;
	}


	/**
	 * Check if current method is selected
	 *
	 * @return bool
	 */
	public function is_method_selected() {
		return WC()->session->get( 'chosen_shipping_methods' )[0] === $this->id . ':' . $this->get_instance_id();
	}

	/**
	 * Create an authorized request if PRO plugin is active
	 *
	 * @param array $base_params shipping settings.
	 *
	 * @return bool|mixed|null
	 */
	public function get_data_from_pro_api( $base_params ) {
		$services = isset( $base_params['service'] ) ? explode( ',', $base_params['service'] ) : array();

		$body = array(
			'completeness-checking'  => in_array( 38, $services ),
			'contents-checking'      => in_array( 81, $services ),
			'courier'                => in_array( 26, $services ),
			'declared-value'         => $base_params['sumoc'],
			'goods-value'            => $base_params['sumoc'],
			'entries-type'           => 'SALE_OF_GOODS',
			'payment-method'         => 'CASHLESS',
			'fragile'                => in_array( 4, $services ),
			'index-from'             => $base_params['from'],
			'dimension-type'         => RPAEFW_PRO_Helper::get_pack_type( $base_params['pack'] ),
			'inventory'              => in_array( 23, $services ),
			'mail-direct'            => isset( $base_params['country'] ) ? $base_params['country'] : 643,
			'mass'                   => $base_params['weight'],
			'mail-category'          => RPAEFW_PRO_Helper::get_mail_category( $base_params['object'] ),
			'mail-type'              => RPAEFW_PRO_Helper::get_mail_type( $base_params['object'] ),
			'with-electronic-notice' => in_array( 62, $services ),
			'sms-notice-recipient'   => intval( in_array( 64, $services ) ),
			'with-order-of-notice'   => in_array( 2, $services ),
			'with-simple-notice'     => in_array( 1, $services ),
			'with-fitting'           => in_array( 82, $services ),
			'functionality-checking' => in_array( 83, $services ),
			'vsd'                    => in_array( 66, $services ),
		);

		// add params if shipping within Russia
		if ( $body['mail-direct'] == 643 ) {
			$body['index-to']             = $base_params['to'];
			$body['delivery-point-index'] = $base_params['to']; // for ekom
		}

		$request = RPAEFW_PRO::get_data_from_api( '/1.0/tariff', 'POST', $body );

		if ( isset( $request['error'] ) || isset( $request['code'] ) ) {
			$this->maybe_print_error( __( 'Error during shipping calculation. Check WooCommerce Log for more information', 'russian-post-and-ems-for-woocommerce' ) );
			$this->log_it( __( 'Could not calculate shipping via /1.0/tariff.' ) . ' ' . json_encode( $request ) . ' Body: ' . json_encode( $body, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ), 'error' );

			return false;
		}

		return $request;
	}

	/**
	 * Return country number for Russian Post api base
	 *
	 * @param string $code country code.
	 *
	 * @return false|int|string
	 */
	public function get_country_number( $code ) {
		$countries = array(
			'AU' => 36,
			'AT' => 40,
			'AZ' => 31,
			'AX' => 949,
			'AL' => 8,
			'DZ' => 12,
			'AS' => 16,
			'AI' => 660,
			'AO' => 24,
			'AD' => 20,
			'AQ' => 10,
			'AG' => 28,
			'AR' => 32,
			'AM' => 51,
			'AW' => 533,
			'AF' => 4,
			'BS' => 44,
			'BD' => 50,
			'BB' => 52,
			'BH' => 48,
			'BY' => 112,
			'BZ' => 84,
			'BE' => 56,
			'BJ' => 204,
			'BM' => 60,
			'BG' => 100,
			'BO' => 68,
			'BQ' => 535,
			'BA' => 70,
			'BW' => 72,
			'BR' => 76,
			'VG' => 92,
			'BN' => 96,
			'BF' => 854,
			'BI' => 108,
			'BT' => 64,
			'VU' => 548,
			'VA' => 336,
			'GB' => 826,
			'HU' => 348,
			'VE' => 862,
			'VI' => 850,
			'UM' => 581,
			'TL' => 626,
			'VN' => 704,
			'GA' => 266,
			'HT' => 332,
			'GY' => 328,
			'GM' => 270,
			'GH' => 288,
			'GP' => 312,
			'GT' => 320,
			'GN' => 324,
			'GW' => 624,
			'DE' => 276,
			'GG' => 831,
			'GI' => 292,
			'HN' => 340,
			'HK' => 344,
			'GD' => 308,
			'GL' => 304,
			'GR' => 300,
			'GE' => 268,
			'GU' => 316,
			'DK' => 208,
			'JE' => 832,
			'DJ' => 262,
			'DM' => 212,
			'DO' => 214,
			'EG' => 818,
			'ZM' => 894,
			'EH' => 732,
			'ZW' => 716,
			'IL' => 376,
			'IN' => 356,
			'ID' => 360,
			'JO' => 400,
			'IQ' => 368,
			'IR' => 364,
			'IE' => 372,
			'IS' => 352,
			'ES' => 724,
			'IT' => 380,
			'YE' => 887,
			'CV' => 132,
			'KZ' => 398,
			'KY' => 136,
			'KH' => 116,
			'CM' => 120,
			'CA' => 124,
			'QA' => 634,
			'KE' => 404,
			'CY' => 196,
			'KI' => 296,
			'CN' => 156,
			'CC' => 166,
			'CO' => 170,
			'KM' => 174,
			'CG' => 180,
			'CD' => 178,
			'CR' => 188,
			'CI' => 384,
			'CU' => 192,
			'KW' => 414,
			'KG' => 417,
			'CW' => 531,
			'LA' => 418,
			'LV' => 428,
			'LS' => 426,
			'LR' => 430,
			'LB' => 422,
			'LY' => 434,
			'LT' => 440,
			'LI' => 438,
			'LU' => 442,
			'MU' => 480,
			'MR' => 478,
			'MG' => 450,
			'YT' => 175,
			'MO' => 446,
			'MK' => 807,
			'MW' => 454,
			'MY' => 458,
			'ML' => 466,
			'MV' => 462,
			'MT' => 470,
			'MA' => 504,
			'MQ' => 474,
			'MH' => 584,
			'MX' => 484,
			'FM' => 583,
			'MZ' => 508,
			'MD' => 498,
			'MC' => 492,
			'MN' => 496,
			'MS' => 500,
			'MM' => 104,
			'NA' => 516,
			'NR' => 520,
			'NP' => 524,
			'NE' => 562,
			'NG' => 566,
			'NL' => 528,
			'NI' => 558,
			'NU' => 570,
			'NZ' => 554,
			'NC' => 540,
			'NO' => 578,
			'AE' => 784,
			'OM' => 784,
			'BV' => 74,
			'IM' => 833,
			'NF' => 574,
			'CX' => 162,
			'SH' => 906,
			'HM' => 334,
			'CK' => 184,
			'PK' => 586,
			'PW' => 585,
			'PS' => 275,
			'PA' => 591,
			'PG' => 598,
			'PY' => 600,
			'PE' => 604,
			'PN' => 612,
			'PL' => 616,
			'PT' => 620,
			'PR' => 630,
			'RE' => 638,
			'RW' => 646,
			'RO' => 642,
			'SV' => 222,
			'WS' => 882,
			'SM' => 674,
			'ST' => 678,
			'SA' => 682,
			'SZ' => 748,
			'KP' => 410,
			'MP' => 580,
			'SC' => 690,
			'BL' => 652,
			'SX' => 534,
			'MF' => 663,
			'PM' => 666,
			'SN' => 686,
			'VC' => 670,
			'KN' => 659,
			'LC' => 662,
			'RS' => 688,
			'SG' => 702,
			'SY' => 760,
			'SK' => 703,
			'SI' => 705,
			'US' => 840,
			'SB' => 90,
			'SO' => 706,
			'SD' => 729,
			'SR' => 740,
			'SL' => 694,
			'TJ' => 762,
			'TW' => 158,
			'TH' => 764,
			'TZ' => 834,
			'IO' => 86,
			'TC' => 796,
			'TG' => 768,
			'TK' => 772,
			'TO' => 776,
			'TT' => 780,
			'TV' => 798,
			'TN' => 788,
			'TM' => 795,
			'TR' => 792,
			'UG' => 800,
			'UZ' => 860,
			'UA' => 804,
			'WF' => 876,
			'UY' => 858,
			'FO' => 234,
			'FJ' => 242,
			'PH' => 608,
			'FI' => 246,
			'FK' => 238,
			'FR' => 250,
			'GF' => 254,
			'PF' => 258,
			'TF' => 260,
			'HR' => 258,
			'CF' => 140,
			'TD' => 148,
			'ME' => 499,
			'CZ' => 203,
			'CL' => 152,
			'CH' => 756,
			'SE' => 752,
			'SJ' => 744,
			'LK' => 144,
			'EC' => 218,
			'GQ' => 226,
			'ER' => 232,
			'EE' => 233,
			'ET' => 231,
			'ZA' => 710,
			'GS' => 239,
			'KR' => 410,
			'SS' => 728,
			'JM' => 388,
			'JP' => 392,
		);

		return isset( $countries[ $code ] ) ? $countries[ $code ] : false;
	}


	/**
	 * In case old plugin version of shipping type is presented
	 *
	 * @param string $old_value old plugin shipping type.
	 *
	 * @return bool|mixed
	 */
	public function get_new_id_shipping_type( $old_value ) {
		$old_types = array(
			'ПростаяБандероль'           => 3000,
			'ЗаказнаяБандероль'          => 3010,
			'ЗаказнаяБандероль1Класс'    => 16010,
			'ЦеннаяБандероль'            => 3020,
			'ЦеннаяБандероль1Класс'      => 16020,
			'ПростаяПосылка'             => 27030,
			'ЦеннаяПосылка'              => 27020,
			'Посылка1Класс'              => 47020,
			'EMS'                        => 7020,
			'МждМешокМ'                  => 9001,
			'МждМешокМАвиа'              => 9001,
			'МждМешокМЗаказной'          => 9011,
			'МждМешокМАвиаЗаказной'      => 9011,
			'МждБандероль'               => 3001,
			'МждБандерольАвиа'           => 3001,
			'МждБандерольЗаказная'       => 3011,
			'МждБандерольАвиаЗаказная'   => 3011,
			'МждМелкийПакет'             => 5001,
			'МждМелкийПакетАвиа'         => 5001,
			'МждМелкийПакетЗаказной'     => 5011,
			'МждМелкийПакетАвиаЗаказной' => 5011,
			'МждПосылка'                 => 4021,
			'МждПосылкаАвиа'             => 4021,
			'EMS_МждДокументы'           => 7031,
			'EMS_МждТовары'              => 7031,
		);

		return isset( $old_types[ $old_value ] ) ? $old_types[ $old_value ] : false;
	}

	/**
	 * Print human error only for admin to easy debug errors
	 *
	 * @param string  $message error message.
	 * @param boolean $default_message Default error message condition.
	 * @param boolean $admin_only Only for admin condition.
	 */
	public function maybe_print_error( $message = '', $default_message = true, $admin_only = true ) {
		if ( ! current_user_can( 'administrator' ) && $admin_only ) {
			return;
		}

		$info_msg = $default_message ? __( 'This message and method are visible only for the site Administrator for debugging purposes.', 'russian-post-and-ems-for-woocommerce' ) : '';

		$this->add_rate(
			array(
				'id'    => $this->get_rate_id(),
				'label' => $this->title . '. ' . $message . ' ' . $info_msg,
				'cost'  => 0,
			)
		);
	}

	/**
	 * Log shipping data using WC logger
	 *
	 * @param string $message error message.
	 * @param string $type error type.
	 */
	public function log_it( $message, $type = 'info' ) {
		if ( ! current_user_can( 'administrator' ) ) {
			return;
		}

		$hide_info_messages = get_option( 'rpaefw_hide_info_log', 'no' );

		if ( 'yes' === $hide_info_messages && 'info' === $type ) {
			return;
		}

		wc_get_logger()->{$type}( $message, array( 'source' => 'russian-post' ) );
	}
}
