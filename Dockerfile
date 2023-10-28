FROM php:8-apache
LABEL maintainer="Alefe Souza <contact@alefesouza.com>"

RUN a2enmod rewrite

RUN docker-php-ext-install pdo_mysql

RUN pecl install -f xdebug && docker-php-ext-enable xdebug;
