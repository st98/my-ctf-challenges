FROM php:7.4-apache

ADD public/index.php /var/www/html/

RUN chmod -R 755 /var/www
RUN chown root:root /var/www

RUN echo "HarekazeCTF{<censored>}" > "/flag"
RUN chmod -R 755 /flag*