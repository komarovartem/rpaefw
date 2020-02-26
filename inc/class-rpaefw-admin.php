<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RPAEFW_Admin {
	public function __construct() {
		add_action( 'admin_footer', [ $this, 'admin_promo_notice' ] );
	}

	public function admin_promo_notice() {
		if ( RPAEFW::is_pro_active() ) {
			return;
		}

		$instance_id = 0;
		if ( isset( $_REQUEST['instance_id'] ) &&
		     isset( $_REQUEST['tab'] ) && $_REQUEST['tab'] == 'shipping' &&
		     isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'wc-settings'
		) {
			$instance_id = intval( $_REQUEST['instance_id'] );
		}

		if ( ! $instance_id ) {
			return;
		}

		?>
        <div class="rpaefw-promo">
            <h3 class="wc-settings-sub-title">Почта России и EMS - PRO</h3>
            <p>
                Поддержка отправлений для <b>корпоративных клиентов</b> Почты РФ
                включая ЕКОМ, а так же синхронизацию заказов с <b>личным кабинетом</b>
                для автоматического заполнения бланков, создания партий и ускоренного приема отправлений в отделении.
            </p>
            Так же PRO дополнение включает:
            <ul class="ul-disc">
                <li>
                    База областей и городов РФ для простого поиска и выбора.
                    <img src="<?php echo RPAEFW::plugin_dir_url() . 'assets/img/state-city-select.png'; ?>"
                         style="max-width: 240px">
                    <small>Включает 50+ тысяч адресов официального справочника Почты РФ</small>
                </li>
                <li>
                    Автопоиск индекса для области/города
                    <small>индекс больше не является обязательным полем</small>
                </li>
                <li>
                    Автоматический пересчет доставки на основе выбора наложенного платежа.

                    <small>если используется обычная посылка, но покупатель выбрал наложенный платеж как оплату,
                        доставка
                        пересчитается с объявленной стоимостью и включенной услугой COD в соответствии с тарифами
                        Почты РФ</small>
                </li>
                <li>
                    Синхронизация и отображение <b>ПВЗ для ЕКОМ отправлений</b>
                    <img src="<?php echo RPAEFW::plugin_dir_url() . 'assets/img/pvz-select.png'; ?>" alt="">
                    <small>Отображение пунктов выдачи заказа в городе покупателя с возможностью выбора на карте</small>
                </li>

                <li>
                    Синхронизация заказов с личным кабинетом в один клик
                    <img src="<?php echo RPAEFW::plugin_dir_url() . 'assets/img/otpravka.png'; ?>"
                         style="max-width: 200px">
                    <small>Автоматическое заполнение полей при создании отправлений</small>
                </li>

                <li>
                    Отображение доставки для покупателя до непосредственного расчета
                </li>
                <li>
                    Расчет стоимости и времени доставки с помощью личного кабинета, а не общедоступного сервиса.
                </li>
                <li>
                    Дополнительные опции для работы с классами доставки и общими параметрами такими как дата, стоимость
                    и тд.
                </li>
                <br>
                <br>
                <a href="https://yumecommerce.com/pochta/" target="_blank" class="button-primary">Посмотреть демо</a>
                <a href="https://woocommerce.com/products/russian-post-and-ems-pro-for-woocommerce/" target="_blank"
                   class="button">Купить</a>
                <br>
                <small style="margin-top: 10px">
                    Для использования функций личного кабинета и ЕКОМ требуется активный договор с АО «Почта России» для
                    интернет-магазинов.
                </small>
            </ul>
        </div>

        <style>
            .rpaefw-promo {
                position: absolute;
                right: 20px;
                z-index: 999;
                top: 150px;
                width: 300px;
                border: 1px solid #7e8993;
                background: #fff;
                padding: 30px;
                border-radius: 3px;
            }

            .rpaefw-promo img {
                margin-top: 10px;
            }

            .rpaefw-promo small {
                opacity: .8;
                line-height: 1.5;
                padding: 5px 0 0 0;
                display: block;
            }

            .rpaefw-promo li {
                margin-bottom: 14px !important;
            }

            .rpaefw-promo img {
                width: 100%;
                display: block;
            }

            #wpbody-content form > *:not(.woo-nav-tab-wrapper) {
                max-width: calc(100% - 400px);
            }
        </style>
		<?php
	}

}

new RPAEFW_Admin();