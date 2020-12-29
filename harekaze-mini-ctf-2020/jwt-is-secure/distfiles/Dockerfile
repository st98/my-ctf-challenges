FROM php:7.4-apache

COPY ./php.ini $PHP_INI_DIR/php.ini
COPY ./public /var/www/html
RUN chmod -R 755 /var/www/html
RUN chmod -R 1777 /var/www/html/keys
RUN chown root:root /var/www/html

EXPOSE 80