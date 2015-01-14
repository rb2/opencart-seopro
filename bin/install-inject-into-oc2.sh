#!/bin/bash
## Inject all module files into clean copy of Opencart 2.x via symlinks

######
###### BE CAREFUL: the --FORCE switch used in `ln`, which removes original files ######
######
# USE SEPARATE BRANCH for testing:
#
#    cd oc2011 && git co master && git co -b oc2011-seopro
#
# Without `--force` it will be hard to update module injection into OC

# ln -s existing-file symlink-filename

## To help prepare list of files and folders use `tree -afFR --noreport`.
## For detecting new files use something like `git logst --diff-filter=A .` (look for only added files)


# ------------------------------------------------------------------------------------
# CONFIGURATION
#
# ose:
BASE="$HOME/webproject/oc/oc2011/public_html"
# rb:
# BASE="$HOME/projects/oc/oc2011/public_html"
# ------------------------------------------------------------------------------------

## make structure of folders
mkdir -p ${BASE}/catalog/controller/module/
mkdir -p ${BASE}/catalog/view/theme/default/template/module/
#mkdir -p ${BASE}/admin/language/english/module/
#mkdir -p ${BASE}/admin/language/english/rbose/
#mkdir -p ${BASE}/admin/language/russian/module/
#mkdir -p ${BASE}/admin/language/russian/rbose/
#mkdir -p ${BASE}/admin/model/module/
#mkdir -p ${BASE}/admin/view/javascript/jquery/magnific/
#mkdir -p ${BASE}/admin/view/stylesheet/rbose/
#mkdir -p ${BASE}/admin/view/template/module/
#mkdir -p ${BASE}/admin/view/template/rbose/

#mkdir -p ${BASE}/catalog/language/russian/information

## new module files
cd ../upload
ln -s --force `pwd`/catalog/controller/common/seo_pro.php                   ${BASE}/catalog/controller/common/seo_pro.php
ln -s --force `pwd`/catalog/controller/module/language.php                  ${BASE}/catalog/controller/module/language.php
ln -s --force `pwd`/catalog/view/theme/default/template/module/language.tpl ${BASE}/catalog/view/theme/default/template/module/language.tpl

## change existing core files
cd ../dev-modified
ln -s --force `pwd`/system/library/url.php                       ${BASE}/system/library/url.php
ln -s --force `pwd`/admin/controller/catalog/product.php         ${BASE}/admin/controller/catalog/product.php
ln -s --force `pwd`/admin/controller/setting/setting.php         ${BASE}/admin/controller/setting/setting.php
ln -s --force `pwd`/admin/language/english/catalog/product.php   ${BASE}/admin/language/english/catalog/product.php
ln -s --force `pwd`/admin/language/english/setting/setting.php   ${BASE}/admin/language/english/setting/setting.php
ln -s --force `pwd`/admin/model/catalog/category.php             ${BASE}/admin/model/catalog/category.php
ln -s --force `pwd`/admin/model/catalog/product.php              ${BASE}/admin/model/catalog/product.php
ln -s --force `pwd`/admin/view/template/catalog/product_form.tpl ${BASE}/admin/view/template/catalog/product_form.tpl
ln -s --force `pwd`/admin/view/template/setting/setting.tpl      ${BASE}/admin/view/template/setting/setting.tpl
ln -s --force `pwd`/index.php                                    ${BASE}/index.php
