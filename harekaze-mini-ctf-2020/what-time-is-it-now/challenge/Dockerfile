FROM php:7.4-apache

ADD public/index.php /var/www/html/

RUN chmod -R 755 /var/www
RUN chown root:root /var/www

RUN echo "HarekazeCTF{1t's_7pm_1n_t0ky0}" > "/flag"
RUN chmod -R 755 /flag*

EXPOSE 80