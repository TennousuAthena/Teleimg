FROM php:7.4-fpm-alpine

RUN apk add --update libzip-dev curl-dev sqlite3pp wget &&\
    docker-php-ext-install curl json pdo_sqlite && \
    apk del gcc g++ &&\
    rm -rf /var/cache/apk/*

VOLUME /var/www
WORKDIR /var/www

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
 && composer --ansi --version --no-interaction



CMD ["php-fpm"]