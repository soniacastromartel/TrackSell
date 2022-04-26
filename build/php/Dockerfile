FROM php:7.3.28-apache

RUN pecl install xdebug && docker-php-ext-enable xdebug
#RUN echo 'zend_extension="/usr/local/lib/php/extensions/no-debug-non-zts-20151012/xdebug.so"' >> /usr/local/etc/php/php.ini
#RUN echo 'xdebug.remote_port=9090' >> /usr/local/etc/php/php.ini
#RUN echo 'xdebug.remote_enable=1' >> /usr/local/etc/php/php.ini
#RUN echo 'xdebug.remote_autostart=1' >> /usr/local/etc/php/php.ini
#RUN echo 'xdebug.remote_log="/app/xdebug.log"'>> /usr/local/etc/php/php.ini
#RUN echo 'xdebug.remote_host=0.0.0.0' >> /usr/local/etc/php/php.ini
#RUN echo 'xdebug.client_host=192.168.6.210' >> /usr/local/etc/php/php.ini
#RUN echo 'xdebug.remote_connect_back = false' >> /usr/local/etc/php/php.ini


# RUN echo 'xdebug.client_host = localhost' >> /usr/local/etc/php/php.ini
# RUN echo 'xdebug.client_port = 9090' >> /usr/local/etc/php/php.ini

# RUN echo 'xdebug.log ="/app/xdebug.log"' >> /usr/local/etc/php/php.ini
# RUN echo 'xdebug.remote_handler = dbgp' >> /usr/local/etc/php/php.ini
# RUN echo 'xdebug.mode = debug' >> /usr/local/etc/php/php.ini
# RUN echo 'xdebug.idekey=VSCODE' >> /usr/local/etc/php/php.ini
# RUN echo 'xdebug.start_with_request = yes' >> /usr/local/etc/php/php.ini

RUN apt-get update && apt-get install -y libpq-dev && docker-php-ext-install pdo pdo_pgsql

RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN docker-php-ext-enable mysqli 
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN apt-get update && apt-get install -y zlib1g-dev libzip-dev libpng-dev
RUN docker-php-ext-install zip

# Install LDAP
RUN apt-get update && apt-get install -y libldap2-dev
RUN docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu
RUN docker-php-ext-install ldap

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
    && docker-php-ext-install -j$(nproc) iconv \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd
    
ENV COMPOSER_MEMORY_LIMIT=-1

RUN apt-get install -y vim  

#Microsoft SQL Server Prerequisites
RUN apt-get update
RUN apt-get install -y apt-transport-https
RUN apt-get install -y gnupg2
RUN apt-get install -y curl

ENV ACCEPT_EULA=Y

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        locales \
        apt-transport-https \
    && curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add - \
    && curl https://packages.microsoft.com/config/debian/9/prod.list > /etc/apt/sources.list.d/mssql-release.list \
    && echo "en_US.UTF-8 UTF-8" > /etc/locale.gen \
    && locale-gen \
    && apt-get update \
    && ACCEPT_EULA=Y apt-get install -y msodbcsql17 \
    && ACCEPT_EULA=Y apt-get install -y mssql-tools \
    && apt-get install -y unixodbc-dev \
    && apt-get install -y libgssapi-krb5-2 


RUN echo '[system_default_sect]' >> /etc/ssl/openssl.cnf
RUN echo 'MinProtocol = TLSv1.0' >> /etc/ssl/openssl.cnf
RUN echo 'CipherString = DEFAULT@SECLEVEL=1' >> /etc/ssl/openssl.cnf 

RUN pecl install sqlsrv pdo_sqlsrv
RUN echo 'extension=sqlsrv.so' >> /usr/local/etc/php/php.ini
RUN echo 'extension=pdo_sqlsrv.so' >> /usr/local/etc/php/php.ini

VOLUME /app
WORKDIR /app



#TLS
RUN apt-get update
RUN apt-get install apache2 -y openssl
RUN a2enmod ssl

COPY ./grupoicot_es.crt /etc/apache2/ssl/grupoicot_es.crt
COPY ./grupoicot_es.key /etc/apache2/ssl/grupoicot_es.key
COPY ./grupoicot_es.ca.crt /etc/apache2/ssl/grupoicot_es.ca.crt


RUN apt-get install -y git