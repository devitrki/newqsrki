git pull origin master
composer dump-autoload -o
composer install
php artisan migrate --force
php artisan clear-compiled
php artisan view:clear
php artisan config:clear
php artisan optimize
composer dump-autoload -o
php artisan queue:restart


chmod -R 777 /var/www/newqsrki/
chown -R www-data:www-data /var/www/newqsrki/
find /var/www/newqsrki/ -type f -exec chmod 644 {} \;
find /var/www/newqsrki/ -type d -exec chmod 755 {} \;
chgrp -R www-data storage bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache
