#php artisan schedule:run

#/usr/sbin/apache2ctl -DFOREGROUND
chown -R www-data:www-data /public
chmod -R 755 /public
a2enmod rewrite 
a2enmod headers

cd /app && chgrp -R www-data storage bootstrap/cache public
cd /app && chmod -R ug+rwx storage bootstrap/cache public
#cd /app && chown www-data:www-data -R ./
#cd /app && php artisan schedule:run 

/usr/sbin/apache2ctl -DFOREGROUND

#crontab /etc/cron.d/cron-tasks
#crond -f  
# php artisan serve --port=8080 
# php artisan serve --port=8000
