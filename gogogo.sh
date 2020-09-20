#!/bin/bash
cd /var/www/vhosts/rindula.de/uni.rindula.de/ || exit

chmod +x ./bin/cake

export php=/opt/plesk/php/7.4/bin/php

# Composer Update
/usr/bin/composer install --dev --no-ansi --optimize-autoloader --no-interaction --no-plugins --no-progress --no-scripts --no-suggest

# Cake
## Datenbank Migration
php ./bin/cake.php migrations migrate

## Cache leeren
php ./bin/cake.php cache clear_all
