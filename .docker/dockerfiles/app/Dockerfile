FROM php:5.6-cli

ENV TIMEZONE Europe/Paris
RUN echo "date.timezone = $TIMEZONE" > /usr/local/etc/php/conf.d/timezone.ini

ENV DEBIAN_FRONTEND noninteractive
RUN pecl install mongo
RUN echo "extension=mongo.so" > /usr/local/etc/php/conf.d/mongo.ini

WORKDIR /usr/local/bin
RUN php -r "readfile('https://getcomposer.org/installer');" | php
RUN mv composer.phar composer
RUN chmod +x composer

VOLUME [ /data ]

CMD ["/data/bin/eeevve"]
# CMD [ "/bin/bash" ]
