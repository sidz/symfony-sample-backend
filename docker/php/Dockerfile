FROM php:7.4.10-fpm-alpine

RUN apk add --no-cache \
        bash \
        make \
        unzip

RUN set -eux; \
    apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        build-base \
        libzip-dev \
    ; \
    \
    docker-php-ext-install -j$(nproc) \
        zip \
        opcache \
    ; \
    docker-php-ext-enable opcache; \
    runDeps="$( \
        scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
            | tr ',' '\n' \
            | sort -u \
            | awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
    )"; \
    apk add --no-cache --virtual .phpexts-rundeps $runDeps; \
    apk del .build-deps

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY docker/php/php.ini $PHP_INI_DIR

ENV COMPOSER_ALLOW_SUPERUSER 1

COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

WORKDIR /app

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]
