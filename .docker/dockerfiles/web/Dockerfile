FROM php:5.6-apache

ENV TIMEZONE Europe/Paris
RUN echo "date.timezone = $TIMEZONE" > /usr/local/etc/php/conf.d/timezone.ini

ENV DEBIAN_FRONTEND noninteractive
RUN pecl install mongo
RUN echo "extension=mongo.so" > /usr/local/etc/php/conf.d/mongo.ini

RUN a2enmod rewrite

RUN rm -rf /var/www/html && \
    ln -s /var/www/app/web /var/www/html

VOLUME [ "/var/www/app", "/var/log/apache2" ]

# CMD [ "/bin/bash" ]
