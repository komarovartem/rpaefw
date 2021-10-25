=== Russian Post and EMS for WooCommerce ===
Contributors: artemkomarov
Tags: woocommerce, woocommerce shipping, ecommerce, shipping
Requires at least: 5.4
Tested up to: 5.8
Stable tag: 1.3.8
Requires PHP: 7.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

The plugin allows you to automatically calculate shipping costs of "Russian Post" or "EMS"

== Description ==

The plugin allows you to automatically calculate shipping costs of "Russian Post" or "EMS" using official API [tariff.pochta.ru](https://tariff.pochta.ru/).

* Calculate shipping costs based on weight and cost of cart
* Display time of delivery for shipments
* Prices for different types of delivery
* Save and send email with tracking number to customer

It is also possible to specify an additional fixed cost.

= PRO extension  =

Special shipping methods and options for corporate clients of the Russian Post, with real-time shipping rates and dashboard synchronization. Special EKOM method which is synchronized with the official Russian Post database let your clients choose delivery points based on their address.

Moreover, the extension adds useful functionality which helps your clients to choose shipping methods more easily.

* One click synchronization of orders with Russian Post dashboard
* Automatic getting and sending track-number
* Automatic changing order status based on tracking
* Options for creating free shipping
* Include database with all Russian regions and cities
* Automatically select postcode based on client address
* Normalize client address, avoiding mistypes and not existing addresses
* Recalculate shipping cost based on COD payment method selection
* Additional options for shipping classes and shipping method adjustments
* Support for dashboards with several OPS points
* Support two different dashboards for different types of shipments
* Flexible options for shipping classes

[Demo website](https://yumecommerce.com/pochta/). PRO extension can be purchased on the [official WooCommerce marketplace](https://woocommerce.com/products/russian-post-and-ems-pro-for-woocommerce/)


== Installation ==

= From your WordPress dashboard =

Visit 'Plugins > Add New'
Search for 'Russian Post and EMS'
Activate Cash on Delivery of Russian Post from your Plugins page.

Then create new Shipping Zone and add Russian Post as a method.

== Frequently Asked Questions ==

= How accurate is this plugin? =

The plugin by itself has no methods to calculate the shipping price. All data comes from official Russian Post API tariff.pochta.ru


== Screenshots ==

1. Основные настройки
2. PRO дополнение: выбор области и города из списка
3. PRO дополнение: выбор пунктов выдачи заказа ЕКОМ в городе покупателя
4. PRO дополнение: автоматический трекинг отправлений и отслеживание статуса

== Changelog ==

= 1.3.8 =

* Совместимость с новымим версиями WP и WC

= 1.3.7 =

* Исправлены коды стран для международных отправлений
* Добавлено более подробное описания для некоторых тарифов

= 1.3.6 =

* Исправлен перевод с английского языка для некоторых строк
* Добавлена валидация и сообщения об ошибке для EKOM индекса ПВЗ в случае невозможности расчета.
* Добавлены админ правила для логов

= 1.3.5 =

* Исправлен расчет для COD (card on delivery)
* Добавлен полностью автоматизированный трекинг отправлений для PRO версии

= 1.3.4 =

* Новые опции для бесплатной доставки

= 1.3.3 =

* Исправлена логика отображения для заказов созданных через админ панель
* Добавлена возможность переопределять шаблоны писем с трек номером

= 1.3.2 =

* Добавлено обновление цены для ЕКОМ отправлений
* Добавлена возможность удаления кэша

= 1.3.1 =

* WooCommerce 4 совместимость
* Исправлена ссылка трек номера в email
* Удалены устаревшие списки городов и индексов
* Мелкие фиксы и улучшения

= 1.3.0 =

* Добавлено отображение трек кода для бесплатной доставки
* Добавлена синхронизация с PRO дополнением

= 1.2.9 =

* Добавлены отправления вида: Письмо
* Добавлен выбор вида отправления наземный или воздушный
* Удалено поле EMS из email письма трекинга
* Мелкие фиксы и улучшения

= 1.2.8 =

* Фикс проблемы с локальным поиском индекса через fopen на некоторых хостингах

= 1.2.7 =

* Удалено ограничение расчета только на страницах оформления заказа

= 1.2.6 =

* Исправлена ошибка с ЕКОМ упаковкой
* Исправлено округление суммы для отправлений c объявленой стоимостью
* Добавлены все коды дополнительных услуг для внутренних и международных отправлений

= 1.2.5 =

* Переход на оффицинальный API Почты России
* Добавлены методы для расчета отправлений без объявленной ценности
* Добавлены методы для корпоративных клиентов ПР
* Добавлены опции дополнительных услуг
* Добавлены опция налогообложения
* Добавлена возможность ограничивать расчет заказа по весу
* Добавлены сообщения о возможных ошибках для администратора
* Добавлено логирование запросов плагина

= 1.2 =

* Добавлена возможность установить нулевую стоимость для максимально фиксированной суммы оценки вложения
* Добавлена поддержка мультивалютности (для магазинов использующих валюту по умолчанию отличную от рубля)
* Добавлена полная ссылка отслеживания отправления при отправке email уведомления

= 1.1.6 =

Добавлен метод: Посылка 1 Класс
Добавлен метод: Заказная бандероль 1 класс
Добавлен метод: Международная авиапосылка

Удален метод: Ценная Авиа Посылка (больше не является методом почты)
Удален метод: Ценная Авиа Бандероль (больше не является методом почты)
Удалено не поддерживаемой сообщение о перевесе

Исправлена возможная ошибка неправильного расчета при международном отправлении для некоторых стран

= 1.1.5 =

Исправлена ошибка с отправкой трек кодов
Добавлена проверка на чтение индексных файлов

= 1.1 =

Добавлено кэширование

= 1.0 =

Исправлена ошибка при неверном индексе
Исправлена ошибка при отсутствии международного отправления EMS для определенных стран
Исправлено имя класса доставки
Добавлена проверка на выполнение функции gzinflate()
Улучшена валидация индекса для России
Улучшена совместимость отправки трек номеров с WC

= 0.9 =

Исправлена ошибка с id методами
Исправлена ошибка при осутствии даты доставки (спасибо @evanre)
Исправлен расчет доставки для цифровых (виртуальных) товаров

Добавлена валидация индекса для России
Добавлена валидация веса отправления
Добавлена опция ввода максимальной фиксированной суммы объявленной стоимости
Добавлена возможность отключить метод доставки если вес превышает допустимый для отправления
Добавлена опция показывать метод только если сумма заказа выше уканной

= 0.8 =

Исправлена ошибка file_get_contents для тестового сервера postcalc

= 0.7 =

Добавлен функционал для отправки трек-номеров Почты России и EMS. При отправке номер отсылается на почту клиента с соответствующими комментариями.
Добавлен статус заказа - Доставляется.

= 0.6 =

Добавлены опции для международной доставки. Добавлена опция простая посылка.

= 0.5 =

Добавленно склонение для сроков доставки и пофиксен стиль копирайта

= 0.4 =

Устранена проблема с символом рубля

= 0.3 =

Добавлена возможность указать дополнительный вес и стоимость упаковки.

= 0.2 =

Если поле индекс отсутствует то берется введенный город получателя за конечный пункт.

= 0.1 =

Первая версия.

