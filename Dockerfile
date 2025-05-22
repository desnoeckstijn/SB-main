FROM php:8.2-apache

RUN a2enmod rewrite

# Zorg dat je geen oude versie van de site kopieert
WORKDIR /var/www/html

# Debuggen met PHP
COPY ./php.ini /usr/local/etc/php/php.ini

# servername localhost voor de apache server
#. Apache warning: “Could not reliably determine the server’s fully qualified domain name”
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# PHP PDO MySQL Extensie 
RUN docker-php-ext-install pdo pdo_mysql
RUN docker-php-ext-enable pdo pdo_mysql