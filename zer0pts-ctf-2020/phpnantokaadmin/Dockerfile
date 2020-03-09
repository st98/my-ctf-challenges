FROM php:7.3-apache

ENV DEBIAN_FRONTEND noninteractive

RUN apt-get -y update --fix-missing && apt-get install -y libsqlite3-dev && docker-php-ext-install pdo_sqlite

ADD challenge/index.php /var/www/html/
ADD challenge/util.php /var/www/html/
ADD challenge/config.php /var/www/html/
ADD challenge/.htaccess /var/www/html/
ADD challenge/style.css /var/www/html/
RUN mkdir /var/www/html/db && touch /var/www/html/db/index.html

RUN chown -R www-data:www-data /var/www/