<?php
/**
 * Russian Post promo
 *
 * @package Russian Post/Promo
 */

defined( 'ABSPATH' ) || exit;

/**
 * Russian Post promo class
 */
class RPAEFW_Promo {
	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_footer', array( $this, 'admin_promo_notice' ) );
	}

	/**
	 * Promo notice
	 */
	public function admin_promo_notice() {
		if ( RPAEFW::is_pro_active() && class_exists( 'CDEKFW' ) ) {
			return;
		}

		if ( isset( $_REQUEST['tab'] ) &&
			 $_REQUEST['tab'] === 'shipping' &&
			 isset( $_REQUEST['page'] ) &&
			 $_REQUEST['page'] === 'wc-settings' ) {
			if ( isset( $_REQUEST['section'] ) && 'rpaefw' === $_REQUEST['section'] ) {

			} else {
				$instance_id = 0;
				if ( isset( $_REQUEST['instance_id'] ) ) {
					$instance_id = intval( $_REQUEST['instance_id'] );
				}

				if ( ! $instance_id ) {
					return;
				}

				if ( ! class_exists( 'WC_Shipping_Zone_Data_Store' ) ) {
					return;
				}

				$WC_Shipping_Zone_Data_Store = new WC_Shipping_Zone_Data_Store();
				$shipping_type               = $WC_Shipping_Zone_Data_Store->get_method( $instance_id );

				if ( 'rpaefw_post_calc' !== $shipping_type->method_id ) {
					return;
				}
			}
		} else {
			return;
		}

		?>
		<div id="rpaefw-promo">
		<?php if ( ! class_exists( 'CDEKFW' ) ) : ?>
			<div class="rpaefw-promo-block">
				<h3 class="wc-settings-sub-title">
					СДЭК и СДЭК PRO
				</h3>
				<p>
					Бесплатный плагин для расчета стоимости и сроков доставки доступен на официальном <a href="https://ru.wordpress.org/plugins/cdek-for-woocommerce/" target="_blank">репозитории плагинов WordPress</a>
				</p>
				<p>
					Плюс коммерческое PRO расширение для корпоративных клиентов СДЭК с поддержкой передачи информации по заказу в систему СДЭК и формированием печатной формы квитанции к заказу и ШК-места.
				</p>
				<br>
				<a href="https://yumecommerce.com/cdek/" target="_blank" class="button-primary">Посмотреть демо</a>
				<a href="https://woocommerce.com/products/cdek-pro-for-woocommerce/" target="_blank"
				   class="button">Купить</a>
				<br>
				<small style="margin-top: 10px">
					Для использования функций интеграции личного кабинета требуется активный договор со СДЭК.
				</small>
			</div>
		<?php endif; ?>
		<?php if ( ! RPAEFW::is_pro_active() ) : ?>
			<div class="rpaefw-promo-block">
				<h3 class="wc-settings-sub-title">Почта России и EMS PRO</h3>
				<p>
					Поддержка отправлений для <b>корпоративных клиентов</b> Почты РФ
					включая ЕКОМ, а так же синхронизацию заказов с <b>личным кабинетом</b>
					для автоматического заполнения бланков, создания партий и ускоренного приема отправлений в
					отделении.
				</p>
				Так же PRO дополнение включает:
				<ul class="ul-disc">
					<li>
						База областей и городов РФ для простого поиска и выбора.
						<img src="<?php echo RPAEFW::plugin_dir_url() . 'assets/img/state-city-select.png'; ?>"
							 style="max-width: 240px">
						<small>Включает 50+ тысяч адресов официального справочника Почты РФ.</small>
					</li>
					<li>
						Автопоиск индекса для области/города.
						<small>индекс больше не является обязательным полем</small>
					</li>
					<li>
						Автоматический пересчет доставки на основе выбора наложенного платежа.

						<small>
							Если используется обычная посылка, но покупатель выбрал наложенный платеж как оплату, доставка пересчитается с объявленной стоимостью и тарифами Почты РФ.
						</small>
					</li>
					<li>
						Автоматическое получение и отправка трек номера.
						<small>
							Так же возможно синхронизировать заказ с трекингом для автоматической перевода статуса
							заказа в
							завершенные после получения отправления покупателем
						</small>
					</li>
					<li>
						Синхронизация и отображение <b>ПВЗ для ЕКОМ отправлений</b>
						<img src="<?php echo RPAEFW::plugin_dir_url() . 'assets/img/pvz-select.png'; ?>" alt="">
						<small>Отображение пунктов выдачи заказа в городе покупателя с возможностью выбора на
							карте</small>
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
						Дополнительные опции для работы с классами доставки и общими параметрами такими как дата,
						стоимость
						и тд.
					</li>
				</ul>
				<br>
				<br>
				<a href="https://yumecommerce.com/pochta/" target="_blank" class="button-primary">Посмотреть
					демо</a>
				<a href="https://woocommerce.com/products/russian-post-and-ems-pro-for-woocommerce/"
				   target="_blank"
				   class="button">Купить</a>
				<br>
				<small style="margin-top: 10px">
					Для использования функций личного кабинета и ЕКОМ требуется активный договор с АО «Почта России» для интернет-магазинов.
				</small>
			</div>
		<?php endif; ?>

		<style>
			#mainform {
				position: relative;
			}

			#rpaefw-promo {
				position: absolute;
				z-index: 999;
				top: 130px;
				right: 0;
				width: 370px;
			}

			#rpaefw-promo .rpaefw-promo-block {
				border: 1px solid #7e8993;
				background: #fff;
				padding: 20px 30px 30px;
				border-radius: 3px;
				margin-bottom: 50px;
			}

			#rpaefw-promo img {
				margin-top: 10px;
			}

			#rpaefw-promo small {
				opacity: .8;
				line-height: 1.5;
				padding: 5px 0 0 0;
				display: block;
			}

			#rpaefw-promo li {
				margin-bottom: 14px !important;
			}

			#rpaefw-promo img {
				width: 100%;
				display: block;
			}

			#wpbody-content form > *:not(.woo-nav-tab-wrapper) {
				max-width: calc(100% - 430px);
			}
		</style>

		<script>
			(function () {
				let promo = document.getElementById('rpaefw-promo');
				let form = document.getElementById('mainform');

				if (promo && form) {
					form.appendChild(promo)
				}
			})()
		</script>
		<?php
	}

}

new RPAEFW_Promo();
