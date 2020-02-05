#!/bin/bash
cd /var/www/vhosts/rindula.de/uni.rindula.de/

# Composer Update
/opt/plesk/php/7.2/bin/php /usr/lib/plesk-9.0/composer.phar update

# Cake
## Datenbank Migration
/bin/bash ./bin/cake migrations migrate

## Cache leeren
/bin/bash ./bin/cake cache clear_all
