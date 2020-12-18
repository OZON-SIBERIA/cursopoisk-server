FROM php:7.4-fpm

WORKDIR /srv/cursopoisk

RUN apt-get update \
  && apt-get install -y --no-install-recommends \
  git \
  unzip \
  && rm -rf /var/lib/apt/lists/*

#install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mkdir -p /usr/local/bin
RUN mv composer.phar /usr/local/bin/composer

RUN docker-php-ext-install pdo_mysql

RUN composer install