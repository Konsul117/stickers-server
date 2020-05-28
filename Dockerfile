FROM richarvey/nginx-php-fpm

ENV USER_ID = 1000
ENV GROUP_ID = 1000

#RUN deluser www-data &&\
#    if getent group www-data ; then delgroup www-data; fi &&\
#    addgroup -g ${GROUP_ID} www-data &&\
#    adduser -u ${USER_ID} user -g www-data www-data &&\
#    install -d -m 0755 -o www-data -g www-data /home/www-data &&\
#    chown --changes --silent --no-dereference --recursive \
#          --from=33:33 ${USER_ID}:${GROUP_ID} \
#        /var/www \
#        /.composer \
#        /var/run/php-fpm \
#        /var/lib/php/sessions \

ENV MEMCACHED_DEPS zlib-dev libmemcached-dev cyrus-sasl-dev
RUN apk add --no-cache --update libmemcached-libs zlib
RUN set -xe \
    && apk add --no-cache --update --virtual .phpize-deps $PHPIZE_DEPS \
    && apk add --no-cache --update --virtual .memcached-deps $MEMCACHED_DEPS \
    && pecl install memcached \
    && echo "extension=memcached.so" > /usr/local/etc/php/conf.d/20_memcached.ini \
    && rm -rf /usr/share/php7 \
    && rm -rf /tmp/* \
    && apk del .memcached-deps .phpize-deps