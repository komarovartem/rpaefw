<?php
/**
 * Settings for shipping method.
 *
 * @package Russian Post/Settings
 */

defined( 'ABSPATH' ) || exit;

$important_message = '';

if ( ! RPAEFW::is_pro_active() ) {
	$important_message = '<br><br><span style="color: red">Пожалуйста, обратите внимание,</span><span style="color: #007cba"> что расчет доставки происходит только от индекса отправителя до индекса получателя. Убедитесь, что в вашем магазине поле индекс при оформлении заказа не отключено и является обязательным для заполнения, иначе расчет будет невозможно произвести.</span>';
}

$settings_basic = array(
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
			'required' => 'required',
		),
	),
	'from'       => array(
		'title'       => esc_html__( 'Оrigin Postcode', 'russian-post-and-ems-for-woocommerce' ),
		'description' => esc_html__( 'Optional. Postcode of the sender. By default equals to the OPS Service postcode or store postcode. You can specify different from store address one if there is a need.', 'russian-post-and-ems-for-woocommerce' ) . $important_message,
		'type'        => 'number',
	),
	'type'       => array(
		'title'       => esc_html__( 'Type', 'russian-post-and-ems-for-woocommerce' ),
		'description' => esc_html__( 'Select shipping type (in brackets displayed the max allowed weight of shipping method). If this method associated with international shipping zone, make sure you select international type of shipping. If you want to use shipping types for corporate clients you need to add additional info in Russian post plugin settings.', 'russian-post-and-ems-for-woocommerce' ) . '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping&section=rpaefw' ) . '">' . esc_html__( 'Russian Post Settings', 'russian-post-and-ems-for-woocommerce' ) . '</a><br><br> Тарифы Посылка («Стандарт», «Экспресс», «Курьер EMS») относятся к <a href="https://www.pochta.ru/support/parcels/new" target="_blank"> новой продуктовой линейке посылок.</a><br><br> Если вам необходимо выбрать тариф который соответствует способу доставки "Обычный" на сайте pochta.ru и тарифу "Посылка" в личном кабинете вам необходимо выбрать тариф "Посылка нестандартная". Способ доставки "Ускоренный" на сайте pochta.ru соответствует тарифу "Посылка 1 класса"',
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
			27030 => 'Посылка «Стандарт» (10кг)',
			27020 => 'Посылка «Стандарт» с объявленной ценностью (10кг) (новая продуктовая линейка посылок)',
			29030 => 'Посылка «Экспресс» (10кг) (новая продуктовая линейка посылок)',
			29020 => 'Посылка «Экспресс» с объявленной ценностью (10кг) (новая продуктовая линейка посылок)',
			28030 => 'Посылка «Курьер EMS» (10кг) (новая продуктовая линейка посылок)',
			28020 => 'Посылка «Курьер EMS» с объявленной ценностью (10кг) (новая продуктовая линейка посылок)',
			4030  => 'Посылка нестандартная (50кг) (соответствует тарифу "Посылка" в ЛК и способу "Обычный" на pochta.ru)',
			4020  => 'Посылка нестандартная с объявленной ценностью (50кг) (соответствует тарифу "Посылка" в ЛК и способу "Обычный" на pochta.ru)',
			47030 => 'Посылка 1 класса (5кг)',
			47020 => 'Посылка 1 класса с объявленной ценностью (5кг)',
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
			53030 => 'ЕКОМ обыкновенный (20 кг) ' . RPAEFW::only_in_pro_ver_text(),
//			53070 => 'ЕКОМ с обязательным платежом (20 кг) ' . RPAEFW::only_in_pro_ver_text(),
//			54020 => 'ЕКОМ Маркетплейс с объявленной ценностью (20 кг) ' . RPAEFW::only_in_pro_ver_text(),
			7030  => 'EMS (31.5кг)',
			7020  => 'EMS с объявленной ценностью (31.5кг)',
			41030 => 'EMS РТ (31.5кг)',
			41020 => 'EMS РТ с объявленной ценностью (31.5кг)',
			// currently no PVZ lists are available
			// 34030 => 'EMS оптимальное (20кг)',
			// 34020 => 'EMS оптимальное с объявленной ценностью (20кг)',
			52030 => 'EMS Тендер обыкновенное ',
			52020 => 'EMS Тендер с объявленной ценностью',
			4031  => 'Международные отправления. Посылка обыкновенная (20кг)',
			4021  => 'Международные отправления. Посылка с объявленной ценностью (20кг)',
			7031  => 'Международные отправления. EMS обыкновенное (31.5кг)',
			5001  => 'Международные отправления. Мелкий пакет простой (2кг)',
			5011  => 'Международные отправления. Мелкий пакет заказной (2кг)',
			3001  => 'Международные отправления. Бандероль простая (5кг)',
			3011  => 'Международные отправления. Бандероль заказная (5кг)',
			// hide it no matching type for https://otpravka.pochta.ru/specification#/enums-base-mail-type
			// 9001  => 'Международные отправления. Мешок М простой  (14.5кг)',
			// 9011  => 'Международные отправления. Мешок М заказной  (14.5кг)',
		),
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
		),
	),
	'service'    => array(
		'type'              => 'multiselect',
		'class'             => 'wc-enhanced-select',
		'title'             => __( 'Services', 'russian-post-and-ems-for-woocommerce' ),
		'custom_attributes' => array(
			'data-placeholder' => __( 'Additional services', 'russian-post-and-ems-for-woocommerce' ),
		),
		'description'       => __( 'Please note that different types of shipping support different types of services. To get more information regardless shipping types and services check official websites.', 'russian-post-and-ems-for-woocommerce' ) . ' <a href="https://www.pochta.ru/support/post-rules/sending-types" target="_blank">' . __( 'Russian Post services and rules', 'russian-post-and-ems-for-woocommerce' ) . '</a> ' . __( 'or on the Russian Post service tariffing website', 'russian-post-and-ems-for-woocommerce' ) . ' <a href="https://tariff.pochta.ru" target="_blank">tariff.pochta.ru</a>',
		'options'           => array(
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
		),
	),
	'isavia'     => array(
		'title'       => __( 'Preferred shipping option', 'russian-post-and-ems-for-woocommerce' ),
		'description' => __( 'Note it takes effect only for some shipping types', 'russian-post-and-ems-for-woocommerce' ),
		'type'        => 'select',
		'default'     => '1',
		'options'     => array(
			'0' => __( 'Ground delivery', 'russian-post-and-ems-for-woocommerce' ),
			'1' => __( 'Air delivery', 'russian-post-and-ems-for-woocommerce' ),
		),
	),
	'tax_status' => array(
		'title'   => __( 'Tax status', 'russian-post-and-ems-for-woocommerce' ),
		'type'    => 'select',
		'default' => 'taxable',
		'options' => array(
			'taxable' => __( 'Taxable', 'russian-post-and-ems-for-woocommerce' ),
			'none'    => _x( 'None', 'Tax status', 'russian-post-and-ems-for-woocommerce' ),
		),
	),
	'nds'        => array(
		'title'             => __( 'VAT', 'russian-post-and-ems-for-woocommerce' ),
		'description'       => RPAEFW::only_in_pro_ver_text() . __( 'Select how final shipping cost will be calculated with or without VAT rate', 'russian-post-and-ems-for-woocommerce' ),
		'type'              => 'select',
		'default'           => 'yes',
		'custom_attributes' => array(
			RPAEFW::is_pro_active() ? '' : 'disabled' => '',
		),
		'options'           => array(
			'yes' => __( 'Include VAT in final shipping cost', 'russian-post-and-ems-for-woocommerce' ),
			'no'  => _x( 'Without VAT', 'Tax status', 'russian-post-and-ems-for-woocommerce' ),
		),
	),
);

$settings_additional = array(
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
		'custom_attributes' => array( 'min' => 1 ),
	),

	// packaging.
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
	'time'           => array(
		'title'       => esc_html__( 'Delivery Time', 'russian-post-and-ems-for-woocommerce' ),
		'type'        => 'checkbox',
		'label'       => esc_html__( 'Show time of delivery.', 'russian-post-and-ems-for-woocommerce' ),
		'description' => esc_html__( 'Displayed next to the title. Note, it does not work for international shipping and EKOM. The time calculated via official Russian Post service.', 'russian-post-and-ems-for-woocommerce' ) . '<a href="https://delivery.pochta.ru/" target="_blank">delivery.pochta.ru</a>',
		'default'     => 'no',
	),
);

$settings_conditions = array(
	'cond_settings'      => array(
		'title'       => esc_html__( 'Conditions', 'russian-post-and-ems-for-woocommerce' ),
		'description' => esc_html__( 'Turn off and on a method based on some conditions. By default, the method will be turned off when the weight of the order is greater than allowed in a selected type of shipping.', 'russian-post-and-ems-for-woocommerce' ),
		'type'        => 'title',
	),

	'fixedvalue_disable' => array(
		'title'       => esc_html__( 'Min. cost of order in RUB', 'russian-post-and-ems-for-woocommerce' ),
		'description' => esc_html__( 'Disable this method if the cost of the order is less than inputted value. Leave this field empty to allow any order cost.', 'russian-post-and-ems-for-woocommerce' ),
		'type'        => 'number',
	),

	'cond_min_weight'    => array(
		'title'       => esc_html__( 'Min. weight of order in grams', 'russian-post-and-ems-for-woocommerce' ),
		'description' => esc_html__( 'Disable this method if the weight of the order is less than inputted value. Leave this field empty to allow any order weight.', 'russian-post-and-ems-for-woocommerce' ),
		'type'        => 'number',
	),
	'cond_max_weight'    => array(
		'title'       => esc_html__( 'Max. weight of order in grams', 'russian-post-and-ems-for-woocommerce' ),
		'description' => esc_html__( 'Disable this method if the weight of the order is more than inputted value. Leave this field empty to allow any order weight.', 'russian-post-and-ems-for-woocommerce' ),
		'type'        => 'number',
	),
);

if ( ! RPAEFW::is_pro_active() ) {
	$shipping_classes = WC()->shipping()->get_shipping_classes();

	$settings_additional[ uniqid() ] = array(
		'type'        => 'number',
		'disabled'    => true,
		'description' => 'Доступно только в PRO версии. <br>Опционально. Укажите на сколько дней необходимо увеличить отображаемое время доставки',
	);

	$settings_additional[ uniqid() ] = array(
		'title'       => 'Показывать до расчета',
		'type'        => 'checkbox',
		'disabled'    => true,
		'label'       => 'Отображать метод до того как введен адрес доставки',
		'description' => 'Доступно только в PRO версии. <br>По умолчанию метод будет отображаться только после того как произойдет расчет доставки на основе введённого адреса',
		'default'     => 'no',
	);

	$settings_additional[ uniqid() ] = array(
		'title'       => 'Бесплатная доставка',
		'type'        => 'checkbox',
		'disabled'    => true,
		'label'       => 'Сделать стоимость доставки равной нулю',
		'description' => 'Доступно только в PRO версии. <br> Вы можете сделать бесплатную доставку и при этом показывать время доставки для информирования клиентов.',
		'default'     => 'no',
	);

	$settings_additional[ uniqid() ] = array(
		'type'        => 'select',
		'disabled'    => true,
		'label'       => 'Условия для бесплатной доставки',
		'description' => 'Выберите условия при которых бесплатная доставка станет активной',
		'default'     => 'no',
		'options'     => array(
			'' => 'Без дополнительных условий',
		),
	);

	if ( ! empty( $shipping_classes ) ) {
		$settings_additional[ uniqid() ] = array(
			'title'       => 'Для спец. классов доставки',
			'description' => 'Доступно только в PRO версии. <br>Скрыть метод если заказ содержит хотя бы один товар с выбранным классом доставки.',
			'type'        => 'text',
			'disabled'    => true,
		);

		$settings_conditions[ uniqid() ] = array(
			'title'       => 'Дополнительные расходы для классов доставки',
			'type'        => 'title',
			'default'     => '',
			'description' => 'Эти расходы могут быть дополнительно добавлены на основе <a href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping&section=classes' ) . '">классов доставки</a>.',
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
				'description' => 'Доступно только в PRO версии.',
			);
		}

		$settings_conditions[ uniqid() ] = array(
			'title'             => 'Стоимости доставки без класса',
			'type'              => 'text',
			'placeholder'       => 'N/A',
			'sanitize_callback' => array( $this, 'sanitize_cost' ),
			'disabled'          => true,
			'description'       => 'Доступно только в PRO версии.',
		);

		$settings_conditions[ uniqid() ] = array(
			'title'       => 'Тип расчета',
			'type'        => 'select',
			'disabled'    => true,
			'options'     => array(
				'class' => 'За класс: платная доставка для каждого класса доставки отдельно',
				'order' => 'За заказ: платная доставка для самого дорогого класса доставки',
			),
			'description' => 'Доступно только в PRO версии.',
		);
	}
}

$settings_basic      = apply_filters( 'rpaefw_basic_shipping_settings', $settings_basic );
$settings_additional = apply_filters( 'rpaefw_additional_shipping_settings', $settings_additional );
$settings_conditions = apply_filters( 'rpaefw_conditions_shipping_settings', $settings_conditions );

return apply_filters( 'rpaefw_shipping_settings', array_merge( $settings_basic, $settings_additional, $settings_conditions ) );
