FROM php:8.2-fpm
COPY php.ini /usr/local/etc/php/

RUN apt-get update
RUN apt-get install -y zlib1g-dev mariadb-client vim libzip-dev
RUN docker-php-ext-install zip pdo_mysql
RUN curl -sL https://deb.nodesource.com/setup_20.x | bash -
RUN apt-get install -y nodejs
RUN groupadd --force -g 1000 developer
RUN useradd -ms /bin/bash --no-user-group -g developer -u 1000 developer

#Composer install
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer
RUN chmod -R ugo+rw /usr/local/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER 1

ENV COMPOSER_HOME /composer

ENV PATH $PATH:/composer/vendor/bin


WORKDIR /var/www

RUN composer global require "laravel/installer"
