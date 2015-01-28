#!/bin/bash
MODULE=ocjazz-seopro-v2.0.1

cd ..
git log --pretty=format:"%cd %s" --date=short --no-merges | grep -v '\[~\]'  > ./history.txt

# rm -f $MODULE*.zip
zip -r $MODULE.ocmod.zip install.xml upload README.* history.txt
zip -r $MODULE.manual-install.zip upload dev-modified README.* history.txt
rm -f history.txt
