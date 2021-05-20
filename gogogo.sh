#!/bin/bash
cd /home/rindula/domains/uni.rindula.de/public_html || exit

git fetch
git checkout "$1"

# Composer Update
/usr/bin/composer install --optimize-autoloader --no-interaction
