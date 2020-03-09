FROM php:7.4.0-apache
RUN apt-get update 
RUN apt-get install -y git
RUN git clone https://github.com/phpredis/phpredis.git /usr/src/php/ext/redis
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install redis
COPY ./www /var/www/html
EXPOSE 80
