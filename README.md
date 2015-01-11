# Seo Pro for OC2 (Opencart v2)

Tested on Opencart v2.0.1.1 (oc2011).
Should work on oc2010, maybe oc2000. Using oc2000 not recommended: upgrade to oc2011 instead.

This repository contains all modified files, so you can compare them with yours.
You can use Total Commander (Windows), WinMerge (Windows), Meld (Linux) to compare
files and folders. The `upload` folder contains new module files, and `dev-modified`
folder - all modified core files.

If you are looking for older version (for Opencart v1564), switch to oc1564 branch:

* SeoPro for OC2: [oc2011](https://github.com/rb2/opencart-seopro/tree/oc2011)
* SeoPro for OC 1.5.x: [oc1564](https://github.com/rb2/opencart-seopro/tree/oc1564)


# INSTALLATION

* execute SQL query

        ALTER TABLE `product_to_category` ADD `main_category` tinyint(1) NOT NULL DEFAULT '0';

    **if you use prefix**, add it to the table name (for example: oc_product_to_category);

* copy all files from `upload` folder to the root of your Opencart store.
    No files will be overwritten, if you install the extension first time;

* compare and make changes to all other files. See `dev-modified` folder.

    GUI tools that can help you compare files and directories:
    Total Commander (Windows), WinMerge (Windows), Meld (Linux).

* Open file `index.php`, find

        $controller->addPreAction(new Action('common/seo_url'));

    and replace this line with following:

        if (!$seo_type = $config->get('config_seo_url_type')) {
            $seo_type = 'seo_url';
        }
        $controller->addPreAction(new Action('common/' . $seo_type));


# What the main idea of SeoPro and what the difference from default SeoUrl

In Russian: <http://opencartforum.ru/topic/20526-seopro-vs-seourl/>

Product can be assigned to SEVERAL categories in Opencart. This is why product
can have different URLs out of the box with standard SeoUrl (seo_url) library.

*   How this works with default **SeoUrl**?

    By specifying CANONICAL meta tag. Product can have several physical
    addresess in the shop, have categories and subcategories in URL, and so on.
    But all of them point to one canonical address: `domain.com/seokeyword`.
    This is canonical address of the product page.

    There is all okay for search engines. Opencart tolds the right logical
    address to search engines, shop have multiple physical addresess for product
    pages.

    Webmaster tools logs this situation and show the stats: how many physical
    pages point to their canonical address and therefore not counted as
    different pages.

*  How this solved with **SeoPro**?

    SeoPro adds the MAIN CATEGORY term for products and changes the library that
    forms URLs. Product still can be assigned to several categories, but you
    must decide: which category is main (canonical address).

    This allows to recover full category path from any point, when we have only
    `product_id` and don't have the `path` parameter. As result - we now able to
    have the unified product URL (with full path) in modules on home page: such
    as Bestsellers module, for example. The Opencart engine can now return one
    unified URL for product.

    Canonical URL = physical URL. No more physical address duplicates pointing
    to one canonical URL.

    Canonical address of the product now can contain full category path. Or do
    not use categories-ased path -- this can be selected in settings.

See also:

* <http://opencartforum.ru/topic/2463-ustranenie-dublei-stranitc-tovarov-i-kategorii/> (ru)
* <http://opencartforum.ru/topic/10270-dubli-stranitc-seopro/> (ru)




# Authors

Author: [Yesvik](http://opencartforum.ru/user/6876-yesvik/), 2011
(opencartforum.ru, ocStore)

## Contributors

Opencart 2.x

*   [Sergey Ogarkov](https://github.com/ose1955/), <http://www.OpencartJazz.com/>

*   [Ruslan Brest](http://rb.labtodo.com), <http://www.OpencartJazz.com/>

    [rb / rb2](https://opencartforum.com/user/10112-rb2/) (opencartforum.ru)



Opencart / ocStore 1.5.x

*   [freelancer](https://opencartforum.com/user/12381-freelancer/)
    (Ruslan Shvarev), <https://github.com/pavlickm/seopro>

*   [cmd](https://opencartforum.com/user/20535-cmd/)
    (Евгений)

*   [rb / rb2](https://opencartforum.com/user/10112-rb2/)
    (Ruslan Brest), <http://rb.labtodo.com>


## Contributions (SeoPro/OC2)

````sh
git shortlog -n --no-merges --since=2015-01-01 | sed 's/\:$/\n/' >> CONTRIBUTORS.md
````
