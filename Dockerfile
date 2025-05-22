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
# Installeer en schakel de pdo_mysql extensie in
RUN docker-php-ext-install pdo pdo_mysql \
    && docker-php-ext-enable pdo_mysql
    # De 'pdo' extensie is vaak al ingeschakeld, maar kan geen kwaad om mee te installeren.