#!/bin/bash
cd ..
rm -f ocjazz-seopro-v2*.zip
cp aux-development/install.* .
git log --pretty=format:"%cd %s" --date=short --no-merges | grep -v '\[~\]'  > ./history.txt
zip -r ocjazz-seopro-v2.0.1.ocmod.zip install.xml upload README.* history.txt
zip -r ocjazz-seopro-v2.0.1.manual-install.zip upload dev-modified README.* history.txt
rm -f history.txt install.xml
