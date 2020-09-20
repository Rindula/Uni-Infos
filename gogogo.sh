#!/bin/bash
cd /var/www/vhosts/rindula.de/uni.rindula.de/ || exit

chmod +x ./bin/cake

# Composer Update
/opt/plesk/php/7.4/bin/php /usr/lib/plesk-9.0/composer.phar install --dev --no-ansi --optimize-autoloader --no-interaction --no-plugins --no-progress --no-scripts --no-suggest

# Cake
## Datenbank Migration
/opt/plesk/php/7.4/bin/php ./bin/cake.php migrations migrate

## Cache leeren
/opt/plesk/php/7.4/bin/php ./bin/cake.php cache clear_all
