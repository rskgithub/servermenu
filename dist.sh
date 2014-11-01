#!/bin/sh
composer update
grunt dist
filename=servermenu-`git log --pretty=format:'%h' -n 1`.zip
zip -q -r ${filename} . -x app/config.php \
    -x less\* \
    -x cache/* \
    -x templates/cache/\* \
    -x *.zip \
    -x *.git* \
    -x .DS_Store \
    -x *.idea* \
    -x node_modules\* \
    -x logs/app.log \
    -x components\*

echo ----------------------
echo Created ${filename}