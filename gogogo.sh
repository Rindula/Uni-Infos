#!/bin/bash
cd /var/www/vhosts/rindula.de/uni.rindula.de/ || exit

/usr/sbin/plesk bin domain --suspend uni.rindula.de
/usr/sbin/plesk bin domain --suspend dh.rindula.de
/usr/sbin/plesk bin domain --suspend dhbw.rindula.de

# Composer Update
/opt/plesk/php/7.2/bin/php /usr/lib/plesk-9.0/composer.phar install --dev --no-ansi --optimize-autoloader --no-interaction --no-plugins --no-progress --no-scripts --no-suggest

# Cake
## Datenbank Migration
/opt/plesk/php/7.2/bin/php ./bin/cake.php migrations migrate

## Cache leeren
/opt/plesk/php/7.2/bin/php ./bin/cake.php cache clear_all

/usr/sbin/plesk bin domain --on uni.rindula.de
/usr/sbin/plesk bin domain --on dh.rindula.de
/usr/sbin/plesk bin domain --on dhbw.rindula.de
