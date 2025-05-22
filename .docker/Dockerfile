FROM composer:2.8 AS composer

FROM php:8.3-apache

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN --mount=type=cache,target=/var/cache/apt \
    set -eux; \
    ln -fs /usr/share/zoneinfo/Europe/Bratislava /etc/localtime; \
	apt-get update -qq && apt-get upgrade -y; \
	apt-get install -yq --no-install-recommends; \
	rm -rf /var/lib/apt/lists/*; \
	set -eux; \
    pecl install xdebug; \
	docker-php-ext-enable xdebug; \
    echo "xdebug.idekey=PHPSTORM" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.log_level=0" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.discover_client_host=1" >> /usr/local/etc/php/conf.d/xdebug.ini

WORKDIR /var/www/html

CMD ["apache2-foreground"]
