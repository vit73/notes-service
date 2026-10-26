FROM php:8.4-cli-alpine AS vendor

RUN apk add --no-cache git unzip libpq oniguruma \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS postgresql-dev oniguruma-dev \
    && docker-php-ext-install -j"$(nproc)" pdo_pgsql mbstring \
    && apk del .build-deps

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY backend/composer.json backend/composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --no-scripts \
    --no-autoloader

COPY backend/ ./

RUN rm -f bootstrap/cache/packages.php bootstrap/cache/services.php \
    && composer dump-autoload --no-dev --optimize --no-scripts \
    && php artisan package:discover --ansi


FROM php:8.4-fpm-alpine

RUN apk add --no-cache libpq oniguruma \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS postgresql-dev oniguruma-dev \
    && docker-php-ext-install -j"$(nproc)" pdo_pgsql mbstring \
    && apk del .build-deps

WORKDIR /var/www/html

COPY --from=vendor /app /var/www/html

COPY docker/php/entrypoint.sh /usr/local/bin/notes-entrypoint

RUN chmod +x /usr/local/bin/notes-entrypoint \
    && mkdir -p \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

ENTRYPOINT ["notes-entrypoint"]

CMD ["php-fpm"]