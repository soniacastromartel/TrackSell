# PDI
# PDI

PDI, Plan de Incentivos. Nueva versión de la App.


## Acceso a mysql8

docker exec -it mysql8 mysql -uroot -p

## Permitir acceso desde el cliente
ALTER USER 'root'@'%' IDENTIFIED WITH mysql_native_password BY 'secret';


## Error composer memory 
docker-compose run web php -d memory_limit=512M /usr/bin/composer update

## Refresh config .env 
docker-compose run web php artisan config:clear


#Config de LDAP

docker-compose run app php -d memory_limit=512M /usr/bin/composer update
docker-compose run app composer require adldap2/adldap2-laravel

docker-compose run app php artisan vendor:publish --provider='Adldap\Laravel\AdldapServiceProvider'
docker-compose run app php artisan vendor:publish --provider='Adldap\Laravel\AdldapServiceProvider'

se nos genera en config: 

ldap.php
ldap_auth.php

docker-compose run app /usr/bin/composer update --no-cache


## Instalar ChartList Legend

npm install chartist-plugin-legend --save


## Log en produccion
 //LOG IN PRODUCTION
 //Log::debug('calculateTargets method');

 //use Illuminate\Support\Facades\Log; 