# Установка через OCMOD
=======================

## Установка {#installation}


* **Перед установкой SeoPro надо установить "OCMOD Multiline fix"**

    См.
    <http://www.opencartjazz.com/ru/opencart-bugfix/oc2011-bugfix-extension-installer>

    Он требуется для правильной установки наших OCMOD-модулей.

    Если вы получаете ошибку

        Fatal error: Call to undefined method ControllerCatalogProduct::getAllCategories()
        in ***/system/modification/admin/controller/catalog/product.php on line 1036

    она может быть вызвана отсутствием "Multiline OCMOD fix" при установке
    модуля. Установите его (там всего 2-3 строки добавить) и повторите процедуру
    установки.



* Зайдите в phpMyAdmin, выберите базу своего опенкарт-магазина. Выполните запрос:

        ALTER TABLE `product_to_category` ADD `main_category` tinyint(1) NOT NULL DEFAULT '0';

    **если вы используете префикс таблиц**, добавьте его к имени таблицы
    (например: для префикса `oc_` используйте `oc_product_to_category`);

    Если вы получите сообщение об ошибке `Error Code: 1060. Duplicate column name 'main_category'`,
    ничего страшного: значит, эта колонка уже есть в таблице. Продолжайте установку.

* Внесите изменения в файл `index.php` в корневой папке магазина: найдите строку

        $controller->addPreAction(new Action('common/seo_url'));

    и замените её на следующие строки:

        if (!$seo_type = $config->get('config_seo_url_type')) {
            $seo_type = 'seo_url';
        }
        $controller->addPreAction(new Action('common/' . $seo_type));

    НЕ ИСПОЛЬЗУЙТЕ ДЛЯ РЕДАКТИРОВАНИЯ WINDOWS NOTEPAD!
    Пользуйтесь, например, Notepad++ <http://notepad-plus-plus.org/>.
    Файлы должны быть сохранены в кодировке UTF-8 без BOM маркера.

Мини-FAQ

- Q: почему не включить эти изменения в OCMOD?
- A: Потому что если включить, инсталлятор рухнет в процессе обновлений или
    установки на систему, где попытки установки SeoPro уже были. У OCMOD
    инсталлятора нет механизмов обработки таких ошибок. Возможности игнорировать
    эту ошибку тоже.

    Альтернативное решение - удалить эту колонку и создать её заново.
    Непродуманность инсталлятора это позволит обойти, но ценой потери ваших
    данных. Кому это надо? Включать такой вариант в автоустановщик нельзя.

    Поэтому несколько действий надо произвести вручную.

    `index.php` также недоступен для механизма внесения модификаций через OCMOD,
    поэтому его в любом случае придётся редактировать вручную.



## Проверка персональных настроек и переключение на SeoPro

* переименуйте `.htaccess.txt` в `.htaccess`, если это не было сделано ранее.
    Проверьте правило `RewriteBase`. Если магазин установлен на свой домен или
    субдомен, директива должна выглядеть как `RewriteBase /`. Если магазин
    установлен в подпапку, например "www.abc.ru/shop/", то здесь надо написать
    `RewriteBase /shop/`. Пример:

        RewriteBase /
        # RewriteBase /shop/

        RewriteRule ^sitemap.xml$       index.php?route=feed/google_sitemap [L]
        RewriteRule ^ru/sitemap.xml$ ru/index.php?route=feed/google_sitemap [L]
        RewriteRule ^en/sitemap.xml$ en/index.php?route=feed/google_sitemap [L]

    также добавьте здесь правила для всех языков, которые используются в магазине;

* Теперь заходим в админку магазина и устанавливаем OCMOD:

    *   установите расширение `ocjazz-seopro-v2.0.1.ocmod.zip`
        меню (Extensions / Extension installer),
    *   откройте меню "Extensions / Modifications" и нажмите кнопку ообновления,
    *   откройте пункт меню "Extensions / Modules" и установите модуль
        **[OCJazz] SeoPro**,
    *   отредактируйте товары: на вкладке "Links" (Связи) надо установить
        "Main category" (главную категорию) и сохранить,
    *   отредактируйте категории: поле SEO URL (ЧПУ) должно быть заполнено во
        всех категориях,
    *   откройте настройки магазина, там перейдтите во вкладку "Server" (Сервер):

        -   включите переключатель "Use SEO URLs" (использовать SEO URL) в позицию
            "Включено",

        -   переключите тип "SEO URL Type" в выпадающем списке с "default (SeoUrl)"
            на SeoPro,

        -   выберите, включать или нет категории в SEO URL "SEO URL for product
            with categories":

            - если ДА, путь к товару будет выглядеть примерно так:
                `example.com/category-subcategory/subcategory/product.html`,
            - если НЕТ - сразу идёт сеокейворд товара: `example.com/product.html`

        -   выберите окончание ссылок "SEO URL ending" (например, ".html") или
            оставьте это поле пустым.

## Красивые ЧПУ на любой роут

Вы можете получить красивый ЧПУ на любой адрес, просто внеся необходимые синонимы
в таблицу `url_alias` в базе данных (БД).

Для этого откройте phpMyAdmin или аналогичную программу для работы с БД.
Выполните следующий запрос.

Если у вас используется префикс таблиц, добавьте его перед именем таблицы.
Напрмиер, если префикс "oc_", имя таблицы `url_alias` в запросе надо заенить на
`oc_url_alias`.

    INSERT INTO url_alias (query, keyword) VALUES
    ('common/home',           ''),
    ('account/wishlist',      'wishlist'),
    ('account/account',       'my-account'),
    ('checkout/cart',         'shopping-cart'),
    ('checkout/checkout',     'checkout'),
    ('account/login',         'login'),
    ('account/logout',        'logout'),
    ('account/order',         'order-history'),
    ('account/newsletter',    'newsletter'),
    ('product/special',       'specials'),
    ('affiliate/account',     'affiliates'),
    ('checkout/voucher',      'gift-vouchers'),
    ('product/manufacturer',  'brands'),
    ('information/contact',   'contact-us'),
    ('account/return/insert', 'request-return'),
    ('information/sitemap',   'sitemap'),
    ('account/forgotten',     'forgot-password'),
    ('account/download',      'downloads'),
    ('account/return',        'returns'),
    ('account/transaction',   'transactions'),
    ('account/register',      'create-account'),
    ('product/compare',       'compare-products'),
    ('product/search',        'search'),
    ('account/edit',          'edit-account'),
    ('account/password',      'change-password'),
    ('account/address',       'address-book'),
    ('account/reward',        'reward-points'),
    ('affiliate/edit',        'edit-affiliate-account'),
    ('affiliate/password',    'change-affiliate-password'),
    ('affiliate/payment',     'affiliate-payment-options'),
    ('affiliate/tracking',    'affiliate-tracking-code'),
    ('affiliate/transaction', 'affiliate-transactions'),
    ('affiliate/logout',      'affiliate-logout'),
    ('affiliate/forgotten',   'affiliate-forgot-password'),
    ('affiliate/register',    'create-affiliate-account'),
    ('affiliate/login',       'affiliate-login');

Это внесёт в базу большинство синонимов для адресов страниц, используемых в Опенкарт.
Вы можете изменить их на свои или добавить новые, если здесь что-то забыто.

Очистите системмный кеш (удалите в папке "system/cache/" все файлы кроме index.html)
и обновите главную страницу магазина в браузере.
