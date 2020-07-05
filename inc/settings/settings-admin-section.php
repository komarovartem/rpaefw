<?php
/**
 * Settings for shipping method.
 *
 * @package Russian Post/Settings
 */

defined( 'ABSPATH' ) || exit;

$settings = array(
	array(
		'title' => __( 'Russian Post', 'russian-post-and-ems-for-woocommerce' ),
		'type'  => 'title',
		'id'    => 'rpaefw_shipping_options',
	),
	array(
		'title' => __( 'Region-TIN-Agreement', 'russian-post-and-ems-for-woocommerce' ),
		'desc'  => __( 'Provide data if you use methods intended for corporate clients. An agreement concluded between a corporate client and Russian Post. The line consists of the region code according to the Constitution of the Russian Federation, the TIN of the enterprise and the agreement number, separated by a “-” sign (hyphen).', 'russian-post-and-ems-for-woocommerce' ),
		'type'  => 'text',
		'id'    => 'rpaefw_dogovor',
	),
	array(
		'title' => __( 'OPS Service', 'russian-post-and-ems-for-woocommerce' ),
		/* translators: links */
		'desc'  => sprintf( __( 'OPS service index. If you use synchronization with your dashboard, then the index should be identical to that specified in %1$sthe settings of your account.%2$s Under the conditions for receiving cash on delivery and the presence of synchronization with your Russian Post dashboard, it is necessary to set the UTP number in the settings of the account on the Russian Post website.', 'russian-post-and-ems-for-woocommerce' ), '<a href="https://otpravka.pochta.ru/settings#/service-settings" target="_blank">', '</a><br>' ),
		'type'  => 'number',
		'id'    => 'rpaefw_ops_index',
	),
	array(
		'title'             => __( 'Application Authorization Token', 'russian-post-and-ems-for-woocommerce' ),
		/* translators: links */
		'desc'              => RPAEFW::only_in_pro_ver_text() . sprintf( __( 'To integrate with the API of the Russian Post Online Service. Token can be found in the %1$ssettings of your account%2$s', 'russian-post-and-ems-for-woocommerce' ), '<a href="https://otpravka.pochta.ru/settings#/api-settings" target="_blank">', '</a>' ),
		'type'              => 'text',
		'id'                => 'rpaefw_token',
		'custom_attributes' => array(
			RPAEFW::is_pro_active() ? '' : 'disabled' => '',
		),
	),
	array(
		'title'             => __( 'User Authorization Key', 'russian-post-and-ems-for-woocommerce' ),
		/* translators: links */
		'desc'              => RPAEFW::only_in_pro_ver_text() . sprintf( __( 'To integrate with the API of the Russian Post Online Service. You can generate an authorization key %1$shere%2$s', 'russian-post-and-ems-for-woocommerce' ), '<a href="https://otpravka.pochta.ru/specification#/authorization-key" target="_blank">', '</a>' ),
		'type'              => 'text',
		'id'                => 'rpaefw_key',
		'custom_attributes' => array(
			RPAEFW::is_pro_active() ? '' : 'disabled' => '',
		),
	),
);

if ( ! RPAEFW::is_pro_active() ) {
	$settings[] = array(
		'type' => 'sectionend',
		'id'   => 'rpaefw_shipping_options',
	);

	$settings[] = array(
		'title' => 'Отслеживание',
		'type'  => 'title',
	);

	$settings[] = array(
		'title'             => 'Отправка кода отслеживания',
		'desc'              => RPAEFW::only_in_pro_ver_text() . 'Вы можете отправить код отслеживания сразу после создания нового отправления или при изменении статуса заказа на "Доставляется".',
		'type'              => 'text',
		'custom_attributes' => array(
			RPAEFW::is_pro_active() ? '' : 'disabled' => '',
		),
	);

	$settings[] = array(
		'title'             => 'Синхронизировать заказ со статусом отслеживания',
		'desc'              => RPAEFW::only_in_pro_ver_text() . 'Выберите, как часто проверять статус отслеживания. Автоматически устанавливает статус заказа с «Обработка» на «Доставляется» после того, как отправление будет принято в ОПС. И изменяет статуса заказа на «Выполнен», когда отправление будет получено покупателем.',
		'type'              => 'text',
		'custom_attributes' => array(
			RPAEFW::is_pro_active() ? '' : 'disabled' => '',
		),
	);

	$settings[] = array(
		'title'             => 'Блок отслеживания',
		'desc'              => RPAEFW::only_in_pro_ver_text() . 'Отображение информации о трек номере на странице учетной записи клиента.',
		'type'              => 'text',
		'custom_attributes' => array(
			RPAEFW::is_pro_active() ? '' : 'disabled' => '',
		),
	);
}

$settings = apply_filters( 'rpaefw_settings', $settings );


$additional_settings   = array();
$additional_settings[] = array(
	'type' => 'sectionend',
	'id'   => 'rpaefw_shipping_options',
);
$additional_settings[] = array(
	'title' => __( 'Other', 'russian-post-and-ems-for-woocommerce' ),
	'type'  => 'title',
);

$additional_settings = apply_filters( 'rpaefw_additional_settings', $additional_settings );

$additional_settings[] = array(
	'title'    => __( 'Log Messages', 'russian-post-and-ems-for-woocommerce' ),
	'type'     => 'checkbox',
	'id'       => 'rpaefw_hide_info_log',
	'desc'     => __( 'Hide Info Log Messages', 'russian-post-and-ems-for-woocommerce' ),
	'default'  => 'no',
	'desc_tip' => __( 'By default all requests stored in WooCommerce logs. You can hide info messages and keep only errors and warnings.', 'russian-post-and-ems-for-woocommerce' ),
);

$additional_settings[] = array(
	'type' => 'sectionend',
	'id'   => 'rpaefw_shipping_options',
);

return array_merge( $settings, $additional_settings );
