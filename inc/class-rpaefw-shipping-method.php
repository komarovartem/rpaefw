<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RPAEFW_Shipping_Method extends WC_Shipping_Method {
	public function __construct( $instance_id = 0 ) {
		$this->id                 = 'rpaefw_post_calc';
		$this->instance_id        = absint( $instance_id );
		$this->method_title       = esc_html__( 'Russian Post', 'russian-post-and-ems-for-woocommerce' );
		$this->method_description = esc_html__( 'The plugin allows you to automatically calculate shipping costs "Russian Post" or "EMS" on the checkout page using official tariff.pochta.ru service.', 'russian-post-and-ems-for-woocommerce' );
		$this->supports           = array(
			'shipping-zones',
			'instance-settings'
		);

		$settings_basic      = [
			'basic'      => array(
				'title' => esc_html__( 'Basic Settings', 'russian-post-and-ems-for-woocommerce' ),
				'type'  => 'title',
			),
			'title'      => array(
				'title'             => esc_html__( 'Title', 'russian-post-and-ems-for-woocommerce' ),
				'description'       => esc_html__( 'This title will be displayed in checkout', 'russian-post-and-ems-for-woocommerce' ),
				'type'              => 'text',
				'default'           => esc_html__( 'Russian Post', 'russian-post-and-ems-for-woocommerce' ),
				'custom_attributes' => array(
					'required' => 'required'
				),
			),
			'from'       => array(
				'title'       => esc_html__( 'Оrigin Postcode', 'russian-post-and-ems-for-woocommerce' ),
				'description' => esc_html__( 'Optional. Postcode of the sender. By default equals to the OPS Service postcode or store postcode. You can specify different from store address one if there is a need.', 'russian-post-and-ems-for-woocommerce' ),
				'type'        => 'number',
			),
			'type'       => array(
				'title'       => esc_html__( 'Type', 'russian-post-and-ems-for-woocommerce' ),
				'description' => esc_html__( 'Select shipping type (in brackets displayed the max allowed weight of shipping method). If this method associated with international shipping zone, make sure you select international type of shipping. If you want to use shipping types for corporate clients you need to add additional info in Russian post plugin settings.', 'russian-post-and-ems-for-woocommerce' ) . '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping&section=rpaefw' ) . '">' . esc_html__( 'Russian Post Settings', 'russian-post-and-ems-for-woocommerce' ) . '</a>',
				'type'        => 'select',
				'default'     => 27030,
				'options'     => array(
					2000  => 'Письмо простое',
					11000 => 'Письмо простое 2.0',
					2010  => 'Письмо заказное',
					11010 => 'Письмо заказное 2.0',
					33010 => 'Письмо курьерское заказное',
					2020  => 'Письмо с объявленной ценностью',
					15000 => 'Письмо 1 класса простое',
					15010 => 'Письмо 1 класса заказное',
					15020 => 'Письмо 1 класса с объявленной ценностью',
					3000  => 'Бандероль простая (5кг)',
					3010  => 'Бандероль заказная (5кг)',
					3020  => 'Бандероль с объявленной ценностью (5кг)',
					16010 => 'Бандероль 1 класса заказная (2.5кг)',
					16020 => 'Бандероль 1 класса заказная с объявленной ценностью (2.5кг)',
					27030 => 'Посылка стандарт (10кг)',
					27020 => 'Посылка стандарт с объявленной ценностью (10кг)',
					29030 => 'Посылка экспресс (10кг)',
					29020 => 'Посылка экспресс с объявленной ценностью (10кг)',
					28030 => 'Посылка курьер EMS (10кг)',
					28020 => 'Посылка курьер EMS с объявленной ценностью (10кг)',
					4030  => 'Посылка нестандартная  (50кг)',
					4020  => 'Посылка нестандартная с объявленной ценностью  (50кг)',
					47030 => 'Посылка 1 класса (2.5кг)',
					47020 => 'Посылка 1 класса с объявленной ценностью (2.5кг)',
					23030 => 'Посылка онлайн обыкновенная (20кг)',
					23020 => 'Посылка онлайн с объявленной ценностью (20кг)',
					51030 => 'Посылка Легкий возврат обыкновенная (20кг)',
					51020 => 'Посылка Легкий возврат с объявленной ценностью (20кг)',
					24030 => 'Курьер онлайн обыкновенный (31.5кг)',
					24020 => 'Курьер онлайн с объявленной ценностью (31.5кг)',
					30030 => 'Бизнес курьер (31.5кг)',
					30020 => 'Бизнес курьер с объявленной ценностью (31.5кг)',
					31030 => 'Бизнес курьер экспресс (31.5кг)',
					31020 => 'Бизнес курьер экспресс с объявленной ценностью (31.5кг)',
					39000 => 'КПО-стандарт',
					40000 => 'КПО-эконом',
					53030 => 'ЕКОМ (15 кг) ' . RPAEFW::only_in_pro_ver_text(),
					7030  => 'EMS (31.5кг)',
					7020  => 'EMS с объявленной ценностью (31.5кг)',
					41030 => 'EMS РТ (31.5кг)',
					41020 => 'EMS РТ с объявленной ценностью (31.5кг)',
//                  currently no PVZ lists are available
//					34030 => 'EMS оптимальное (20кг)',
//					34020 => 'EMS оптимальное с объявленной ценностью (20кг)',
					52030 => 'EMS Тендер обыкновенное ',
					52020 => 'EMS Тендер с объявленной ценностью',
					4031  => 'Международные отправления. Посылка обыкновенная (20кг)',
					4021  => 'Международные отправления. Посылка с объявленной ценностью (20кг)',
					7031  => 'Международные отправления. EMS обыкновенное (31.5кг)',
					5001  => 'Международные отправления. Мелкий пакет простой (2кг)',
					5011  => 'Международные отправления. Мелкий пакет заказной (2кг)',
					3001  => 'Международные отправления. Бандероль простая (5кг)',
					3011  => 'Международные отправления. Бандероль заказная (5кг)',
//                  hide it no matching type for https://otpravka.pochta.ru/specification#/enums-base-mail-type
//					9001  => 'Международные отправления. Мешок М простой  (14.5кг)',
//					9011  => 'Международные отправления. Мешок М заказной  (14.5кг)',
				)
			),
			'pack'       => array(
				'title'       => esc_html__( 'Package Type', 'russian-post-and-ems-for-woocommerce' ),
				'description' => esc_html__( 'Only for shipping types: Посылка (стандарт, экспресс, курьер EMS) and EKOM package is required. Note there are weight limitations for packages (in brackets displayed the max allowed weight).', 'russian-post-and-ems-for-woocommerce' ),
				'type'        => 'select',
				'default'     => '10',
				'options'     => array(
					10 => 'Коробка «S» (макс. 10кг, до 260х170х80 мм)',
					11 => 'Пакет полиэтиленовый «S» (макс. 7кг)',
					12 => 'Конверт с воздушно-пузырчатой пленкой «S» (макс. 2кг)',
					20 => 'Коробка «М» (макс. 10кг, до 300х200х150 мм)',
					21 => 'Пакет полиэтиленовый «М»  (макс. 7кг)',
					22 => 'Конверт с воздушно-пузырчатой пленкой «М» (макс. 2кг)',
					30 => 'Коробка «L» (макс. 10кг, до 400х270х180 мм)',
					31 => 'Пакет полиэтиленовый «L»  (макс. 7кг)',
					40 => 'Коробка «ХL» (макс. 10кг, 530х260х220 мм)',
					41 => 'Пакет полиэтиленовый «ХL»  (макс. 7кг)',
					99 => 'Нестандартная упаковка (негабарит - сумма сторон не более 1400 мм, сторона не более 600 мм))',
				)
			),
			'service'    => [
				'type'              => 'multiselect',
				'class'             => 'wc-enhanced-select',
				'title'             => __( 'Services', 'russian-post-and-ems-for-woocommerce' ),
				'custom_attributes' => [
					'data-placeholder' => __( 'Additional services', 'russian-post-and-ems-for-woocommerce' )
				],
				'description'       => __( 'Please note that different types of shipping support different types of services. To get more information regardless shipping types and services check official websites.', 'russian-post-and-ems-for-woocommerce' ) . ' <a href="https://www.pochta.ru/support/post-rules/sending-types" target="_blank">' . __( 'Russian Post services and rules', 'russian-post-and-ems-for-woocommerce' ) . '</a> ' . __( 'or on the Russian Post service tariffing website', 'russian-post-and-ems-for-woocommerce' ) . ' <a href="https://tariff.pochta.ru" target="_blank">tariff.pochta.ru</a>',
				'options'           => [
					1   => 'Простое уведомление о вручении',
					2   => 'Заказное уведомление о вручении',
					4   => 'Отметка «Осторожно/Хрупкая»',
					6   => 'Громоздкая посылка',
					7   => 'Доставка нарочным',
					8   => 'Вручение лично в руки',
					9   => 'Доставка документов',
					10  => 'Доставка товаров',
					12  => 'Нестандартный размер',
					14  => 'Страхование отправления',
					22  => 'Проверка соответствия вложения описи',
					23  => 'Составление описи вложения',
					24  => 'Оплата наложенного платежа отправителем',
					25  => 'Таможенный сбор',
					26  => 'Доставка курьером',
					27  => 'Упаковка «Почта России»',
					28  => 'Корпоративный клиент «Почта России»',
					29  => 'Доставка почтового перевода на дом',
					30  => 'Уведомление о вручении почтового перевода',
					31  => 'Заверительный пакет',
					32  => 'Гарантия сохранности',
					33  => 'Отчёт о недоставленных отправлениях',
					34  => 'Нанесение ШК',
					35  => 'Упаковка вложений',
					36  => 'Нанесение стикера',
					37  => 'Перевозка и сдача',
					38  => 'Проверка комплектности',
					39  => 'Заявление о возврате, изменении или исправлении адреса',
					41  => 'Пакет SMS уведомлений отправителю при единичном приеме',
					42  => 'Пакет SMS уведомлений получателю при единичном приеме',
					43  => 'Пакет SMS уведомлений отправителю при партионном приеме',
					44  => 'Пакет SMS уведомлений получателю при партионном приеме',
					45  => 'Пролонгация договора (например, для услуг абонирования ячейки)',
					46  => 'Оплата в момент доставки (COD)',
					57  => 'Межоператорское',
					58  => 'Вручение в ОПС',
					59  => 'Предпочтовая подготовка',
					60  => 'Агентские функции третьим лицам',
					61  => 'Доставка по звонку',
					62  => 'Электронное уведомление о вручении',
					63  => 'Обслуживание консолидаторов',
					64  => 'Пакет SMS-сервис',
					65  => 'Курьерский сбор',
					66  => 'Возврат сопроводительных документов',
					67  => 'Выдача через АПС',
					68  => 'Доставка и вручение почтальонами мелких пакетов на дому',
					69  => 'Забор возврата курьером по адресу получателя',
					70  => 'Наличие индивидуального договора с предприятием почтовой связи',
					71  => 'СМС-уведомление об истечении срока хранения',
					72  => 'СМС-уведомление о продлении срока хранения',
					73  => 'СМС-уведомление об истечении второго срока хранения',
					74  => 'СМС-уведомление о возврате отправления',
					75  => 'СМС-уведомление о поступлении отправления в курьерскую службу',
					76  => 'СМС-уведомление о передачи отправления курьеру для доставки',
					77  => 'СМС-уведомление о неудачной попытке доставки отправления адресату',
					78  => 'СМС-уведомление о второй неудачной попытке доставки отправления адресату',
					80  => 'Забор отправления у отправителя',
					81  => 'Простая проверка вложения',
					82  => 'Проверка вложения примеркой',
					83  => 'Проверка вложения на работоспособность',
					85  => 'Возврат после проверки полный',
					86  => 'Возврат после проверки частичный',
					87  => 'Возврат ранее полученного товара',
					90  => 'Продление срока хранения',
					91  => 'Переадресация',
					92  => 'Прием на территории отправителя',
					93  => 'Доставка возврата отправителю',
					94  => 'Виджет пунктов выдачи',
					95  => 'Бесплатное хранение до 7 дней',
					96  => 'Время работы ПВЗ и почтаматов в вечернее время',
					97  => 'Доставка до ОПС и партнерских ПВЗ и почтаматов',
					98  => 'Доставка и выдача в выходные дни',
					99  => 'Погрузка/разгрузка отправлений при сборе со склада',
					100 => 'Получение информации о движении отправления в реальном времени',
					101 => 'Уведомление отправителя',
					102 => 'Управление доставкой',
					103 => 'Идентификация получателя по ПИН-коду',
				]
			],
			'isavia'     => [
				'title'       => __( 'Preferred shipping option', 'russian-post-and-ems-for-woocommerce' ),
				'description' => __( 'Note it takes effect only for some shipping types', 'russian-post-and-ems-for-woocommerce' ),
				'type'        => 'select',
				'default'     => '1',
				'options'     => [
					'0' => __( 'Ground delivery', 'russian-post-and-ems-for-woocommerce' ),
					'1' => __( 'Air delivery', 'russian-post-and-ems-for-woocommerce' ),
				]
			],
			'tax_status' => [
				'title'   => __( 'Tax status', 'russian-post-and-ems-for-woocommerce' ),
				'type'    => 'select',
				'default' => 'taxable',
				'options' => [
					'taxable' => __( 'Taxable', 'russian-post-and-ems-for-woocommerce' ),
					'none'    => _x( 'None', 'Tax status', 'russian-post-and-ems-for-woocommerce' ),
				]
			],
			'nds'        => [
				'title'             => __( 'VAT', 'russian-post-and-ems-for-woocommerce' ),
				'description'       => RPAEFW::only_in_pro_ver_text() . __( 'Select how final shipping cost will be calculated with or without VAT rate', 'russian-post-and-ems-for-woocommerce' ),
				'type'              => 'select',
				'default'           => 'yes',
				'custom_attributes' => [
					RPAEFW::is_pro_active() ? '' : 'disabled' => ''
				],
				'options'           => [
					'yes' => __( 'Include VAT in final shipping cost', 'russian-post-and-ems-for-woocommerce' ),
					'no'  => _x( 'Without VAT', 'Tax status', 'russian-post-and-ems-for-woocommerce' ),
				]
			]
		];
		$settings_additional = [
			'add_settings'   => array(
				'title' => esc_html__( 'Additional Settings', 'russian-post-and-ems-for-woocommerce' ),
				'type'  => 'title',
			),
			'addcost'        => array(
				'title'       => esc_html__( 'Additional Сost', 'russian-post-and-ems-for-woocommerce' ),
				'description' => esc_html__( 'Additional flat rate in RUB for shipping method. This may be the average value of the package or the cost of fuel, spent on the road to the post;)', 'russian-post-and-ems-for-woocommerce' ),
				'type'        => 'number',
				'default'     => 0,
			),
			'fixedpackvalue' => array(
				'title'             => esc_html__( 'Max. Fixed Package Value', 'russian-post-and-ems-for-woocommerce' ),
				'description'       => esc_html__( 'You can set max. declared value in RUB for some types of departure. Min possible value is 1 RUB. When this fields has no value the declared value equals sum of the order.', 'russian-post-and-ems-for-woocommerce' ) . ' ' . esc_html__( 'This will be applied only if COD is not selected as payment since the COD payment cannot be bigger than declared value.', 'russian-post-and-ems-for-woocommerce' ),
				'type'              => 'number',
				'custom_attributes' => [ 'min' => 1 ],
			),

			// packaging
			'addpackweight'  => array(
				'title'       => esc_html__( 'Packaging', 'russian-post-and-ems-for-woocommerce' ),
				'description' => esc_html__( 'Weight of the one packaging in grams. This weight will be added to the total weight of the order.', 'russian-post-and-ems-for-woocommerce' ),
				'type'        => 'number',
				'default'     => 0,
			),
			'addpackcost'    => array(
				'description' => esc_html__( 'Cost of the one packaging. This cost will be added to the final amount of delivery.', 'russian-post-and-ems-for-woocommerce' ),
				'type'        => 'number',
				'default'     => 0,
			),

			'time' => array(
				'title'       => esc_html__( 'Delivery Time', 'russian-post-and-ems-for-woocommerce' ),
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Show time of delivery.', 'russian-post-and-ems-for-woocommerce' ),
				'description' => esc_html__( 'Displayed next to the title. Note, it does not work for international shipping and EKOM. The time calculated via official Russian Post service.', 'russian-post-and-ems-for-woocommerce' ) . '<a href="https://delivery.pochta.ru/" target="_blank">delivery.pochta.ru</a>',
				'default'     => 'no',
			),
		];

		$settings_conditions = [
			'cond_settings' => array(
				'title'       => esc_html__( 'Conditions', 'russian-post-and-ems-for-woocommerce' ),
				'description' => esc_html__( 'Turn off and on a method based on some conditions. By default, the method will be turned off when the weight of the order is greater than allowed in a selected type of shipping.', 'russian-post-and-ems-for-woocommerce' ),
				'type'        => 'title',
			),

			'fixedvalue_disable' => array(
				'title'       => esc_html__( 'Min. cost of order in RUB', 'russian-post-and-ems-for-woocommerce' ),
				'description' => esc_html__( 'Disable this method if the cost of the order is less than inputted value. Leave this field empty to allow any order cost.', 'russian-post-and-ems-for-woocommerce' ),
				'type'        => 'number',
			),

			'cond_min_weight' => array(
				'title'       => esc_html__( 'Min. weight of order in grams', 'russian-post-and-ems-for-woocommerce' ),
				'description' => esc_html__( 'Disable this method if the weight of the order is less than inputted value. Leave this field empty to allow any order weight.', 'russian-post-and-ems-for-woocommerce' ),
				'type'        => 'number',
			),
			'cond_max_weight' => array(
				'title'       => esc_html__( 'Max. weight of order in grams', 'russian-post-and-ems-for-woocommerce' ),
				'description' => esc_html__( 'Disable this method if the weight of the order is more than inputted value. Leave this field empty to allow any order weight.', 'russian-post-and-ems-for-woocommerce' ),
				'type'        => 'number',
			),
		];

		if ( ! RPAEFW::is_pro_active() ) {
			$shipping_classes = WC()->shipping()->get_shipping_classes();

			$settings_additional[ uniqid() ] = [
				'type'        => 'number',
				'disabled'    => true,
				'description' => 'Доступно только в PRO версии. <br>Опционально. Укажите на сколько дней необходимо увеличить отображаемое время доставки',
			];

			$settings_additional[ uniqid() ] = [
				'title'       => 'Показывать до расчета',
				'type'        => 'checkbox',
				'disabled'    => true,
				'label'       => 'Отображать метод до того как введен адрес доставки',
				'description' => 'Доступно только в PRO версии. <br>По умолчанию метод будет отображаться только после того как произойдет расчет доставки на основе введённого адреса',
				'default'     => 'no',
			];

			if ( ! empty( $shipping_classes ) ) {
				$settings_additional[ uniqid() ] = [
					'title'       => 'Для спец. классов доставки',
					'description' => 'Доступно только в PRO версии. <br>Скрыть метод если заказ содержит хотя бы один товар с выбранным классом доставки.',
					'type'        => 'text',
					'disabled'    => true,
				];

				$settings_conditions[ uniqid() ] = array(
					'title'       => 'Дополнительные расходы для классов доставки',
					'type'        => 'title',
					'default'     => '',
					'description' => 'Эти расходы могут быть дополнительно добавлены на основе <a href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping&section=classes' ) . '">классов доставки</a>.'
				);

				foreach ( $shipping_classes as $shipping_class ) {
					if ( ! isset( $shipping_class->term_id ) ) {
						continue;
					}

					$settings_conditions[ uniqid() ] = array(
						'title'       => sprintf( '"%s" стоимость доставки класса', esc_html( $shipping_class->name ) ),
						'type'        => 'text',
						'placeholder' => 'N/A',
						'disabled'    => true,
						'description' => 'Доступно только в PRO версии.'
					);
				}

				$settings_conditions[ uniqid() ] = array(
					'title'             => 'Стоимости доставки без класса',
					'type'              => 'text',
					'placeholder'       => 'N/A',
					'sanitize_callback' => array( $this, 'sanitize_cost' ),
					'disabled'          => true,
					'description'       => 'Доступно только в PRO версии.'
				);

				$settings_conditions[ uniqid() ] = array(
					'title'       => 'Тип расчета',
					'type'        => 'select',
					'disabled'    => true,
					'options'     => array(
						'class' => 'За класс: платная доставка для каждого класса доставки отдельно',
						'order' => 'За заказ: платная доставка для самого дорогого класса доставки',
					),
					'description' => 'Доступно только в PRO версии.'
				);
			}
		}

		$settings_basic      = apply_filters( 'rpaefw_basic_shipping_settings', $settings_basic );
		$settings_additional = apply_filters( 'rpaefw_additional_shipping_settings', $settings_additional );
		$settings_conditions = apply_filters( 'rpaefw_conditions_shipping_settings', $settings_conditions );

		$settings = apply_filters( 'rpaefw_shipping_settings', array_merge( $settings_basic, $settings_additional, $settings_conditions ) );


		$this->instance_form_fields = $settings;

		foreach ( $settings as $key => $settings ) {
			$this->{$key} = $this->get_option( $key );
		}

		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	/**
	 * Print human error only for admin to easy debug errors
	 *
	 * @param string $message
	 */
	public function maybe_print_error( $message = '' ) {
		if ( ! current_user_can( 'administrator' ) ) {
			return;
		}

		$this->add_rate( array(
			'id'    => $this->get_rate_id(),
			'label' => $this->title . '. ' . $message . ' ' . __( 'This message visible only for site administrator.', 'russian-post-and-ems-for-woocommerce' ),
			'cost'  => 0,
		) );
	}

	/**
	 * Log some data using WC logger
	 *
	 * @param $message
	 * @param string $type
	 */
	public function log_it( $message, $type = 'info' ) {
		$logger = wc_get_logger();
		$logger->{$type}( $message, array( 'source' => 'russian-post' ) );
	}

	/**
	 * calculate_shipping function.
	 *
	 * @param array $package (default: array())
	 */
	public function calculate_shipping( $package = array() ) {
		global $woocommerce;

		$ops_index    = get_option( 'rpaefw_ops_index' ) ? : get_option( 'woocommerce_store_postcode' );
		$from         = $this->from ? $this->from : $ops_index;
		$type         = $this->type;
		$is_ekom      = $type == 53030;
		$dogovor      = get_option( 'rpaefw_dogovor' );
		$country_code = $package[ 'destination' ][ 'country' ] ? $package[ 'destination' ][ 'country' ] : 'RU';
		$postal_code  = wc_format_postcode( $package[ 'destination' ][ 'postcode' ], $country_code );
		$state        = $package[ 'destination' ][ 'state' ];
		$city         = $package[ 'destination' ][ 'city' ];
		$services     = ( isset( $this->service ) && ! empty( $this->service ) ) ? $this->service : [];
		$to           = '';
		$time         = '';
		$time_pro     = ''; // store time here in case PRO

		// get weight of the cart
		// normalise weights, unify to g
		$weight = ceil( wc_get_weight( $woocommerce->cart->cart_contents_weight, 'g' ) );

		// plus pack weight
		$weight += intval( $this->addpackweight );

		// check weight and set minimum value for Russian Post if it is less than required
		$weight = $weight < 100 ? 100 : $weight;

		// get total value
		$total_val = intval( $package[ 'cart_subtotal' ] );

		// additional cost
		$addcost     = intval( $this->addcost );
		$addpackcost = intval( $this->addpackcost );

		// check if default currency is different from RUB
		$currency = $this->get_store_currency();

		// origin postcode must be set
		if ( ! $from ) {
			$this->maybe_print_error( __( 'Post code of sender is required. You can set in store address.', 'russian-post-and-ems-for-woocommerce' ) );

			return;
		}

		// force display method before initial calculation
		if ( isset( $this->force_display ) && $this->force_display === 'yes' && ! $postal_code && ! $city ) {
			$this->add_rate( array(
				'id'    => $this->get_rate_id(),
				'label' => $this->title . ( ( trim( $this->force_display_info ) != '' ) ? ' (' . $this->force_display_info . ')' : '' ),
				'cost'  => 0,
			) );

			return;
		} elseif ( ! $postal_code && ! $city && $country_code == 'RU' ) {
			return;
		}

		// old version plugin types support
		if ( $type && ! is_numeric( $type ) ) {
			$new_type = $this->get_new_id_shipping_type( $type );

			$type = $new_type ? $new_type : 27030;
		}

		// change type if COD is selected
		$type = $this->match_shipping_type_based_on_payment( $type );

		// if postal code is empty take city
		if ( $country_code == 'RU' ) {
			if ( RPAEFW::is_pro_active() ) {
				// if not ekom since it will be calculated later
				if ( ! $is_ekom ) {
					if ( ! $to = $this->get_index_based_on_address( $state, $city, $postal_code ) ) {
						$to = $postal_code;
					}
				}
			} else {
				if ( $postal_code != '' ) {
					// check if post code in a russian post base
					$validated_post_code = $this->get_default_ops( $postal_code );
					if ( ! $validated_post_code ) {
						$this->maybe_print_error( __( 'Post code of customer is not valid. Could not find any matches in a base.', 'russian-post-and-ems-for-woocommerce' ) );

						return;
					}

					$to = $postal_code;
				} else {
					// check if city in a russian post base
					$validated_city = $this->get_default_ops( $city );
					if ( ! $validated_city ) {
						$this->maybe_print_error( __( 'The city is not valid. Could not find any matches in a base.', 'russian-post-and-ems-for-woocommerce' ) );

						return;
					}

					$to = $validated_city;
				}
			}
		}

		// change postcode to EKOM index if it is selected
		if ( $is_ekom ) {
			// add 10 rub for sms which is required but not calculated for some reason
			$addcost += 10;
			if ( RPAEFW::is_pro_active() ) {
				if ( ! $to = intval( $this->get_ekom_index( $state, $city, $type, $services ) ) ) {
					return;
				}
			} else {
				return;
			}
		}

		// check and avoid package overweight before make api request
		$is_overweight = false;
		$weight_ranges = array(
			2000  => array( 5001, 5011 ),
			2500  => array( 16010, 16020, 47030, 47020 ),
			5000  => array( 3000, 3010, 3020, 3001, 3011 ),
			10000 => array( 27030, 27020, 29030, 29020, 28030, 28020, ),
			14500 => array( 9001, 9011 ),
			15000 => array( 53030 ),
			20000 => array( 23030, 23020, 51030, 51020, 34030, 34020, 4031, 4021 ),
			31500 => array( 24030, 24020, 30030, 30020, 7030, 7020, 41030, 41020, 7031 ),
			50000 => array( 4030, 4020 ),
		);

		foreach ( $weight_ranges as $max_val => $types_range ) {
			if ( in_array( $type, $types_range ) && $weight >= $max_val ) {
				$is_overweight = true;
				break;
			}
		}

		// check conditional weights
		if ( ( $this->cond_min_weight && $weight < intval( $this->cond_min_weight ) ) || ( $this->cond_max_weight && $weight > intval( $this->cond_max_weight ) ) ) {
			return;
		}

		if ( $is_overweight ) {
			$this->maybe_print_error( __( 'Overweight is not allowed. To avoid overweight you can limit the range of allowed weights in settings of this shipping method.', 'russian-post-and-ems-for-woocommerce' ) );

			return;
		}

		// remove method if order has item with specific shipping class
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

		// if cost is less than provided in options
		if ( $this->fixedvalue_disable != '' && intval( $this->fixedvalue_disable ) > 0 && $total_val < $this->fixedvalue_disable ) {
			return;
		}

		// fixed value
		$fixedvalue = $this->fixedpackvalue;
		if ( $fixedvalue && $total_val > intval( $fixedvalue ) && ! $this->is_cod_used_as_payment() ) {
			$total_val = intval( $fixedvalue );
		}

		if ( $currency != 'RUB' ) {
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
			'nds'       => isset( $this->nds ) ? $this->nds : 'yes'
		);

		// add COD as service if it is selected as payment method
		if ( $this->is_cod_used_as_payment() ) {
			$services[] = 46;
		}

		// add additional services
		if ( $services ) {
			$base_params[ 'service' ] = implode( ',', $services );
		}

		if ( $dogovor ) {
			$base_params[ 'dogovor' ] = trim( $dogovor );
			$services[]               = 28; // corporate client
		}

		// check if shipping goes abroad
		if ( $country_code != 'RU' ) {
			if ( ! in_array( $type, array( 4031, 4021, 7031, 5001, 5011, 9001, 9011 ) ) ) {
				$this->maybe_print_error( __( 'For international shipping you should select international type in settings.', 'russian-post-and-ems-for-woocommerce' ) );

				return;
			}

			if ( ! $country_code_number = $this->get_country_number( $country_code ) ) {
				$this->maybe_print_error( __( 'Could not find code number for given country.', 'russian-post-and-ems-for-woocommerce' ) );

				return;
			}

			$base_params[ 'country' ] = $country_code_number;
		} else {
			$base_params[ 'to' ] = $to;
		}

		$request      = add_query_arg( $base_params, 'https://tariff.pochta.ru/tariff/v1/calculate?json' );
		$request_hash = 'rpaefw_' . md5( $request );

		if ( ! $shipping_cost = get_transient( $request_hash ) ) {
			if ( ! $shipping_cost = $this->get_data_from_api( $request, 'price', $base_params ) ) {
				return;
			}

			set_transient( $request_hash, $shipping_cost, DAY_IN_SECONDS );
		}

		// in case PRO plugin has return data
		if ( is_array( $shipping_cost ) ) {
			// it could be request for international shipping which has not delivery time option
			if ( isset( $shipping_cost[ 'delivery-time' ] ) ) {
				$time_pro = $shipping_cost[ 'delivery-time' ][ 'max-days' ];
			}
			if ( $this->nds == 'no' ) {
				$shipping_cost = $shipping_cost[ 'total-rate' ] / 100;
			} else {
				$shipping_cost = ( $shipping_cost[ 'total-rate' ] + $shipping_cost[ 'total-vat' ] ) / 100;
			}
		}

		$shipping_class_cost = $this->get_shipping_class_cost( $package );

		// shipping cost + packages cost + additional cost
		$cost = ceil( $shipping_cost + $addcost + $addpackcost + $shipping_class_cost );

		if ( $currency != 'RUB' ) {
			$cost = $this->get_currency_value( $currency, $cost );
		}

		// do not apply if no cost is set
		if ( ! $cost ) {
			$this->maybe_print_error( __( 'Error: no cost is set.', 'russian-post-and-ems-for-woocommerce' ) );

			return;
		}

		// show delivery time
		if ( $this->time === 'yes' && $country_code == 'RU' && ! $is_ekom ) {
			if ( ! $delivery_time = $time_pro ) {
				$request = add_query_arg( array(
					'from'   => $from,
					'to'     => $to,
					'object' => $type,
				), 'https://delivery.pochta.ru/delivery/v1/calculate?json' );

				$request_hash = 'rpaefw_' . md5( $request );

				if ( ! $delivery_time = get_transient( $request_hash ) ) {
					if ( $delivery_time = $this->get_data_from_api( $request, 'time' ) ) {
						set_transient( $request_hash, $delivery_time, DAY_IN_SECONDS * 30 );
					}
				}
			}

			if ( $delivery_time ) {
				if ( isset( $this->add_time ) && $this->add_time ) {
					$delivery_time += intval( $this->add_time );
				}

				$time = ' (' . sprintf( _n( '%s day', '%s days', $delivery_time, 'russian-post-and-ems-for-woocommerce' ), number_format_i18n( $delivery_time ) ) . ')';
			}
		}

		$this->add_rate( array(
			'id'    => $this->get_rate_id(),
			'label' => $this->title . $time,
			'cost'  => $cost,
		) );
	}

	/**
	 * Try to get index from PRO plugin base
	 *
	 * @param $shipping_state
	 * @param $shipping_city
	 *
	 * @return bool|int
	 */
	public function get_index_based_on_address( $shipping_state, $shipping_city ) {
		if ( ! $file = fopen( WP_PLUGIN_DIR . '/russian-post-and-ems-pro-for-woocommerce/inc/post-data-base/index-state-city-base.txt', 'r' ) ) {
			return false;
		}

		$shipping_state    = intval( $shipping_state );
		$shipping_postcode = 0;

		while ( ( $line = fgets( $file ) ) !== false ) {
			list( $index, $state, $city ) = explode( "\t", $line );
			if ( $shipping_state == $state && $shipping_city == $city ) {
				$shipping_postcode = $index;
				break;
			}
		}

		fclose( $file );

		return $shipping_postcode ? $shipping_postcode : false;
	}

	/**
	 * Match non cod like types of shipping and switch it to type with declared value or keep same if no match exists
	 *
	 * @param $type
	 *
	 * @return mixed
	 */
	public function match_shipping_type_based_on_payment( $type ) {
		if ( ! $this->is_cod_used_as_payment() ) {
			return $type;
		}

		$match_types = [
			2000 => 2020, 11000 => 2020, 2010 => 2020, 11010 => 2020, 33010 => 2020, 2020 => 2020, 15000 => 15020, 15010 => 15020, 15020 => 15020, 3000 => 3020, 3010 => 3020, 3020 => 3020, 16010 => 16020, 16020 => 16020, 27030 => 27020, 27020 => 27020, 29030 => 29020, 29020 => 29020, 28030 => 28020, 28020 => 28020, 4030 => 4020, 4020 => 4020, 47030 => 47020, 47020 => 47020, 23030 => 23020, 23020 => 23020, 51030 => 51020, 51020 => 51020, 24030 => 24020, 24020 => 24020, 30030 => 30020, 30020 => 30020, 31030 => 31020, 31020 => 31020, 39000 => 39000, 40000 => 40000, 53030 => 53070, 53070 => 53070, 7030 => 7020, 7020 => 7020, 41030 => 41020, 41020 => 41020, 34030 => 34020, 34020 => 34020, 52030 => 52020, 52020 => 52020, 4031 => 4021, 4021 => 4021, 7031 => 7031, 5001 => 5001, 5011 => 5011, 3001 => 3001, 3011 => 3011, 9001 => 9001, 9011 => 9011,
		];

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

		return in_array( $chosen_payment_method, [ 'cod', 'codpg_russian_post' ] );
	}

	/**
	 * Find index for EKOM shipping type from PRO plugin
	 *
	 * @param $shipping_state
	 * @param $shipping_city
	 *
	 * @param $type
	 * @param $services
	 *
	 * @return bool|string
	 */
	public function get_ekom_index( $shipping_state, $shipping_city, $type, $services ) {
		if ( ! $file = fopen( WP_PLUGIN_DIR . '/russian-post-and-ems-pro-for-woocommerce/inc/post-data-base/pvz.txt', 'r' ) ) {
			return false;
		}

		$shipping_state = intval( $shipping_state );
		$ekom_index     = '';
		$requirements   = [
			'cash_payment'           => false,
			'contents_checking'      => false,
			'with_fitting'           => false,
			'functionality_checking' => false,
		];

		if ( $type == 53070 ) {
			$requirements[ 'cash_payment' ] = true;
		}

		if ( in_array( 81, $services ) ) {
			$requirements[ 'contents_checking' ] = true;
		}

		if ( in_array( 82, $services ) ) {
			$requirements[ 'with_fitting' ] = true;
		}

		if ( in_array( 83, $services ) ) {
			$requirements[ 'functionality_checking' ] = true;
		}


		while ( ( $line = fgets( $file ) ) !== false ) {
			list( $index, $state, $city, $address, $coordinates, $card_payment, $cash_payment, $contents_checking, $functionality_checking, $with_fitting ) = explode( "\t", $line );
			if ( $shipping_state == $state && $shipping_city == $city ) {
				$validated = true;

				foreach ( $requirements as $name => $need ) {
					if ( $validated && $need ) {
						if ( ! ${$name} ) {
							$validated = false;
						}
					}
				}

				if ( $validated ) {
					$ekom_index = $index;
					break;
				}
			}
		}

		fclose( $file );

		if ( ! $ekom_index ) {
			$this->log_it( sprintf( __( 'Could not find EKOM delivery point for the next address %s, type of EKOM %s, and services %s.', 'russian-post-and-ems-for-woocommerce' ), $shipping_state . ' ' . $shipping_city, $type, json_encode( $services ) ) );
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

		// validate currency since some users might have issue when it's not set properly
		if ( isset( $all_currencies[ $currency ] ) ) {
			return $currency;
		}

		return 'RUB';
	}

	/**
	 * Add additional cost based on shipping classes
	 *
	 * @param $package
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

		if ( $atts[ 'percent' ] ) {
			$calculated_fee = $this->fee_cost * ( floatval( $atts[ 'percent' ] ) / 100 );
		}

		if ( $atts[ 'min_fee' ] && $calculated_fee < $atts[ 'min_fee' ] ) {
			$calculated_fee = $atts[ 'min_fee' ];
		}

		if ( $atts[ 'max_fee' ] && $calculated_fee > $atts[ 'max_fee' ] ) {
			$calculated_fee = $atts[ 'max_fee' ];
		}

		return $calculated_fee;
	}


	/**
	 * Evaluate a cost from a sum/string.
	 *
	 * @param string $sum Sum of shipping.
	 * @param array $args Args.
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
			$locale[ 'decimal_point' ],
			$locale[ 'mon_decimal_point' ],
			','
		);
		$this->fee_cost = $args[ 'cost' ];

		// Expand shortcodes.
		add_shortcode( 'fee', array( $this, 'fee' ) );

		$sum = do_shortcode(
			str_replace(
				array(
					'[qty]',
					'[cost]',
				),
				array(
					$args[ 'qty' ],
					$args[ 'cost' ],
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

		foreach ( $package[ 'contents' ] as $item_id => $values ) {
			if ( $values[ 'data' ]->needs_shipping() ) {
				$found_class = $values[ 'data' ]->get_shipping_class();

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
	 * @param $currency
	 * @param $cost
	 * @param $from_rub
	 *
	 * @return int
	 */
	public function get_currency_value( $currency, $cost, $from_rub = true ) {
		// check if third party plugins are installed
		if ( in_array( 'woocommerce-currency-switcher/index.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			$woocs = get_option( 'woocs' );

			if ( $woocs && is_array( $woocs ) && isset( $woocs[ 'RUB' ] ) ) {
				if ( $from_rub ) {
					return round( 1 / $woocs[ 'RUB' ][ 'rate' ] * $cost );
				} else {
					return round( $woocs[ 'RUB' ][ 'rate' ] * $cost );
				}
			}
		}

		if ( ! $rates = $this->get_currency_rates_from_api() ) {
			$this->maybe_print_error( __( 'Cannot get currency rates from api.', 'russian-post-and-ems-for-woocommerce' ) );

			return false;
		}

		$valute_obj = null;

		// find obj for provided currency
		foreach ( $rates->Valute as $valute ) {
			if ( $valute->CharCode == $currency ) {
				$valute_obj = $valute;
				break;
			}
		}

		// if no match is found return false
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

		// parce rates
		$rates = simplexml_load_string( $rates );

		return $rates;
	}

	/**
	 * Try to find destination based on provided type of address
	 *
	 * @param $to
	 *
	 * @return bool
	 */
	public function get_default_ops( $to ) {
		if ( preg_match( '/^[1-9][0-9]{5}$/', $to ) ) {
			// if it is a post code
			if ( $arr = $this->arr_from_txt( 'postcalc_light_post_indexes.txt', $to ) ) {
				return true;
			}
		} else {
			// any other symbols
			if ( $arr = $this->arr_from_txt( 'postcalc_light_cities.txt', $to ) ) {
				// return first match post index based on city
				return array_values( $arr )[ 0 ];
			}
		}

		return false;
	}

	/**
	 * Try to find destination based on provided type of address
	 *
	 * @param $src_txt
	 * @param $search
	 *
	 * @return mixed
	 */
	public function arr_from_txt( $src_txt, $search = '' ) {
		$arr       = array();
		$config_cs = 'utf-8';

		if ( ! $search ) {
			return $arr;
		}

		$src_txt = plugin_dir_path( __FILE__ ) . 'post-calc/' . $src_txt;

		if ( ! $fp = fopen( $src_txt, 'r' ) ) {
			return $arr;
		}

		while ( ( $line = fgets( $fp ) ) !== false ) {
			list( $key, $value ) = explode( "\t", $line );
			if ( mb_stripos( $key, $search ) !== false ) {
				$arr[ $key ] = trim( $value );
				break;
			}
		}
		fclose( $fp );

		return $arr;
	}

	/**
	 * Connecting to the api server and get price
	 *
	 * @param $request
	 * @param $get
	 *
	 * @param array $base_params
	 *
	 * @return mixed
	 */
	public function get_data_from_api( $request, $get, $base_params = [] ) {

//		if ( RPAEFW::is_pro_active() &&
//		     ( $get === 'price' ) &&
//		     get_option( 'rpaefw_token' ) &&
//		     class_exists( 'RPAEFW_PRO' ) &&
//		     class_exists( 'RPAEFW_PRO_Helper' ) ) {
//
//			return $this->get_data_from_pro_api( $base_params );
//		}

		$remote_response = wp_remote_get( $request, [ 'timeout' => 15 ] );
		$this->log_it( __( 'Making request to get:', 'russian-post-and-ems-for-woocommerce' ) . ' ' . $request );

		if ( is_wp_error( $remote_response ) ) {
			$error_message = __( 'Server connection error to get', 'russian-post-and-ems-for-woocommerce' ) . '"' . $get . '". ' . $remote_response->get_error_message();
			$this->log_it( $error_message, 'error' );
			$this->maybe_print_error( $error_message );

			return false;
		}

		if ( $response_code = wp_remote_retrieve_response_code( $remote_response ) !== 200 ) {
			$error_message = __( 'Request error for', 'russian-post-and-ems-for-woocommerce' ) . '"' . $get . '". CODE: ' . $response_code . ' ' . wp_remote_retrieve_body( $remote_response );
			$this->log_it( $error_message, 'error' );
			$this->maybe_print_error( $error_message );

			return false;
		}

		$body = wp_remote_retrieve_body( $remote_response );

		if ( $get === 'price' || $get === 'time' ) {
			$response = json_decode( $body, true );

			if ( isset( $response[ 'error' ] ) ) {
				$error_message = __( 'Error:', 'russian-post-and-ems-for-woocommerce' ) . ' ' . $response[ 'error' ][ 0 ];
				$this->log_it( $error_message, 'error' );
				$this->maybe_print_error( $error_message );

				return false;
			}

			if ( $get === 'price' ) {
				if ( $this->nds == 'no' && RPAEFW::is_pro_active() ) {
					return $response[ 'pay' ] / 100;
				} else {
					return $response[ 'paynds' ] / 100;
				}
			} else {
				return $response[ 'delivery' ][ 'max' ];
			}
		}

		if ( $get === 'currency' ) {
			return $body;
		}

		return false;
	}

	/**
	 * create an authorized request if PRO plugin is active
	 *
	 * @return bool|mixed|null
	 */
	public function get_data_from_pro_api( $base_params ) {
		$services = isset( $base_params[ 'service' ] ) ? explode( ',', $base_params[ 'service' ] ) : [];

		$body = [
			'completeness-checking'  => in_array( 38, $services ),
			'contents-checking'      => in_array( 81, $services ),
			'courier'                => in_array( 26, $services ),
			'declared-value'         => $base_params[ 'sumoc' ],
			'goods-value'            => $base_params[ 'sumoc' ],
			'entries-type'           => 'SALE_OF_GOODS',
			'payment-method'         => 'CASHLESS',
			'fragile'                => in_array( 4, $services ),
			'index-from'             => $base_params[ 'from' ],
			'dimension-type'         => RPAEFW_PRO_Helper::get_pack_type( $base_params[ 'pack' ] ),
			'inventory'              => in_array( 23, $services ),
			'mail-direct'            => isset( $base_params[ 'country' ] ) ? $base_params[ 'country' ] : 643,
			'mass'                   => $base_params[ 'weight' ],
			'mail-category'          => RPAEFW_PRO_Helper::get_mail_category( $base_params[ 'object' ] ),
			'mail-type'              => RPAEFW_PRO_Helper::get_mail_type( $base_params[ 'object' ] ),
			'with-electronic-notice' => in_array( 62, $services ),
			'sms-notice-recipient'   => intval( in_array( 64, $services ) ),
			'with-order-of-notice'   => in_array( 2, $services ),
			'with-simple-notice'     => in_array( 1, $services ),
			'with-fitting'           => in_array( 82, $services ),
			'functionality-checking' => in_array( 83, $services ),
			'vsd'                    => in_array( 66, $services ),
		];

		// add params if shipping within Russia
		if ( $body[ 'mail-direct' ] == 643 ) {
			$body[ 'index-to' ]             = $base_params[ 'to' ];
			$body[ 'delivery-point-index' ] = $base_params[ 'to' ]; // for ekom
		}

		$request = RPAEFW_PRO::get_data_from_api( '/1.0/tariff', 'POST', $body );

		if ( isset( $request[ 'error' ] ) || isset( $request[ 'code' ] ) ) {
			$this->maybe_print_error( __( 'Error during shipping calculation. Check WooCommerce Log for more information', 'russian-post-and-ems-for-woocommerce' ) );
			$this->log_it( __( 'Could not calculate shipping via /1.0/tariff.' ) . ' ' . json_encode( $request ) . ' Body: ' . json_encode( $body, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ), 'error' );

			return false;
		}

		return $request;
	}

	/**
	 * Return country number for Russian Post api base
	 *
	 * @param $code
	 *
	 * @return false|int|string
	 */
	public function get_country_number( $code ) {
		$countries = array(
			'AU' => 36, 'AT' => 40, 'AZ' => 31, 'AX' => 949, 'AL' => 8, 'DZ' => 12, 'AS' => 16, 'AI' => 660, 'AO' => 24, 'AD' => 20, 'AQ' => 10, 'AG' => 28, 'AR' => 32, 'AM' => 51, 'AW' => 533, 'AF' => 4, 'BS' => 44, 'BD' => 50, 'BB' => 52, 'BH' => 48, 'BY' => 112, 'BZ' => 84, 'BE' => 56, 'BJ' => 204, 'BM' => 60, 'BG' => 100, 'BO' => 68, 'BQ' => 535, 'BA' => 70, 'BW' => 72, 'BR' => 76, 'VG' => 92, 'BN' => 96, 'BF' => 854, 'BI' => 108, 'BT' => 64, 'VU' => 548, 'VA' => 336, 'GB' => 826, 'HU' => 348, 'VE' => 862, 'VI' => 850, 'UM' => 581, 'TL' => 626, 'VN' => 704, 'GA' => 266, 'HT' => 332, 'GY' => 328, 'GM' => 270, 'GH' => 288, 'GP' => 312, 'GT' => 320, 'GN' => 324, 'GW' => 624, 'DE' => 276, 'GG' => 831, 'GI' => 292, 'HN' => 340, 'HK' => 344, 'GD' => 308, 'GL' => 304, 'GR' => 300, 'GE' => 268, 'GU' => 316, 'DK' => 208, 'JE' => 832, 'DJ' => 262, 'DM' => 212, 'DO' => 214, 'EG' => 818, 'ZM' => 894, 'EH' => 732, 'ZW' => 716, 'IL' => 376, 'IN' => 356, 'ID' => 360, 'JO' => 400, 'IQ' => 368, 'IR' => 364, 'IE' => 372, 'IS' => 352, 'ES' => 724, 'IT' => 380, 'YE' => 887, 'CV' => 132, 'KZ' => 398, 'KY' => 136, 'KH' => 116, 'CM' => 120, 'CA' => 124, 'QA' => 634, 'KE' => 404, 'CY' => 196, 'KI' => 296, 'CN' => 156, 'CC' => 166, 'CO' => 170, 'KM' => 174, 'CG' => 180, 'CD' => 178, 'CR' => 188, 'CI' => 384, 'CU' => 192, 'KW' => 414, 'KG' => 417, 'CW' => 531, 'LA' => 418, 'LV' => 428, 'LS' => 426, 'LR' => 430, 'LB' => 422, 'LY' => 434, 'LT' => 440, 'LI' => 438, 'LU' => 442, 'MU' => 480, 'MR' => 478, 'MG' => 450, 'YT' => 175, 'MO' => 446, 'MK' => 807, 'MW' => 454, 'MY' => 458, 'ML' => 466, 'MV' => 462, 'MT' => 470, 'MA' => 504, 'MQ' => 474, 'MH' => 584, 'MX' => 484, 'FM' => 583, 'MZ' => 508, 'MD' => 498, 'MC' => 492, 'MN' => 496, 'MS' => 500, 'MM' => 104, 'NA' => 516, 'NR' => 520, 'NP' => 524, 'NE' => 562, 'NG' => 566, 'NL' => 528, 'NI' => 558, 'NU' => 570, 'NZ' => 554, 'NC' => 540, 'NO' => 578, 'AE' => 784, 'OM' => 784, 'BV' => 74, 'IM' => 833, 'NF' => 574, 'CX' => 162, 'SH' => 906, 'HM' => 334, 'CK' => 184, 'PK' => 586, 'PW' => 585, 'PS' => 275, 'PA' => 591, 'PG' => 598, 'PY' => 600, 'PE' => 604, 'PN' => 612, 'PL' => 616, 'PT' => 620, 'PR' => 630, 'RE' => 638, 'RW' => 646, 'RO' => 642, 'SV' => 222, 'WS' => 882, 'SM' => 674, 'ST' => 678, 'SA' => 682, 'SZ' => 748, 'KP' => 410, 'MP' => 580, 'SC' => 690, 'BL' => 652, 'SX' => 534, 'MF' => 534, 'PM' => 666, 'SN' => 686, 'VC' => 670, 'KN' => 659, 'LC' => 662, 'RS' => 662, 'SG' => 702, 'SY' => 760, 'SK' => 760, 'SI' => 705, 'US' => 840, 'SB' => 90, 'SO' => 706, 'SD' => 729, 'SR' => 729, 'SL' => 694, 'TJ' => 762, 'TW' => 158, 'TH' => 764, 'TZ' => 834, 'TC' => 796, 'TG' => 768, 'TK' => 772, 'TO' => 776, 'TT' => 780, 'TV' => 798, 'TN' => 788, 'TM' => 795, 'TR' => 792, 'UG' => 800, 'UZ' => 860, 'UA' => 804, 'WF' => 876, 'UY' => 858, 'FO' => 234, 'FJ' => 242, 'PH' => 608, 'FI' => 246, 'FK' => 238, 'FR' => 250, 'GF' => 254, 'PF' => 258, 'TF' => 260, 'HR' => 258, 'CF' => 140, 'TD' => 140, 'ME' => 499, 'CZ' => 203, 'CL' => 152, 'CH' => 756, 'SE' => 752, 'SJ' => 744, 'LK' => 144, 'EC' => 218, 'GQ' => 226, 'ER' => 232, 'EE' => 233, 'ET' => 231, 'ZA' => 710, 'GS' => 239, 'KR' => 410, 'SS' => 728, 'JM' => 388, 'JP' => 392,
		);

		return isset( $countries[ $code ] ) ? $countries[ $code ] : false;
	}


	/**
	 * In case old plugin version of shipping type is presented
	 *
	 * @param $old_value
	 *
	 * @return bool|mixed
	 */
	public function get_new_id_shipping_type( $old_value ) {
		$old_types = [
			'ПростаяБандероль' => 3000, 'ЗаказнаяБандероль' => 3010, 'ЗаказнаяБандероль1Класс' => 16010, 'ЦеннаяБандероль' => 3020, 'ЦеннаяБандероль1Класс' => 16020, 'ПростаяПосылка' => 27030, 'ЦеннаяПосылка' => 27020, 'Посылка1Класс' => 47020, 'EMS' => 7020, 'МждМешокМ' => 9001, 'МждМешокМАвиа' => 9001, 'МждМешокМЗаказной' => 9011, 'МждМешокМАвиаЗаказной' => 9011, 'МждБандероль' => 3001, 'МждБандерольАвиа' => 3001, 'МждБандерольЗаказная' => 3011, 'МждБандерольАвиаЗаказная' => 3011, 'МждМелкийПакет' => 5001, 'МждМелкийПакетАвиа' => 5001, 'МждМелкийПакетЗаказной' => 5011, 'МждМелкийПакетАвиаЗаказной' => 5011, 'МждПосылка' => 4021, 'МждПосылкаАвиа' => 4021, 'EMS_МждДокументы' => 7031, 'EMS_МждТовары' => 7031,
		];

		return isset( $old_types[ $old_value ] ) ? $old_types[ $old_value ] : false;
	}
}
