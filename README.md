# Seo Pro: INSTALLATION

* execute SQL query

        ALTER TABLE `product_to_category` ADD `main_category` tinyint(1) NOT NULL DEFAULT '0';

    if you use prefix, add it to the table name.

* copy file `seo_pro.php` into the folder `catalog/controller/common`

* Open file `index.php`, find and replace this line

        $controller->addPreAction(new Action('common/seo_url'));

    with following

        if (!$seo_type = $config->get('config_seo_url_type')) {
            $seo_type = 'seo_url';
        }
        $controller->addPreAction(new Action('common/' . $seo_type));
