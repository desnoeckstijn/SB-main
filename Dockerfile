FROM php:8.2-apache

RUN a2enmod rewrite

# Zorg dat je geen oude versie van de site kopieert
WORKDIR /var/www/html

# Debuggen met PHP
COPY ./php.ini /usr/local/etc/php/php.ini