#!/bin/bash
cd /var/www/vhosts/rindula.de/uni.rindula.de/

/usr/sbin/plesk bin domain --suspend uni.rindula.de

# Composer Update
/opt/plesk/php/7.2/bin/php /usr/lib/plesk-9.0/composer.phar update

# Cake
## Datenbank Migration
/opt/plesk/php/7.2/bin/php ./bin/cake.php migrations migrate

## Cache leeren
/opt/plesk/php/7.2/bin/php ./bin/cake.php cache clear_all

/usr/sbin/plesk bin domain --on uni.rindula.de
