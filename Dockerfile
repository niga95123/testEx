# Базовый образ соотвествует требованиям https://symfony.com/doc/current/setup.html#technical-requirements
FROM php:8.1-fpm AS builder

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libpq-dev \
        libicu-dev \
        libxslt1-dev \
        libzip-dev \
        unzip \
        librabbitmq-dev \
    && pecl install apcu amqp \
    && docker-php-ext-enable apcu amqp \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
        pdo pdo_pgsql pgsql \
        intl \
        xsl \
        zip \
        opcache

RUN pecl install xdebug && docker-php-ext-enable xdebug

COPY ./docker/php-fpm/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Устанавливаем composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

# При необходимости, оптимизировать для разных сборок
COPY /docker/php-fpm/php.ini /usr/local/etc/php/php.ini

# Удаляем лишние файлы из образа
RUN rm -rf /var/lib/apt/lists/* \
    && rm -rf ./docker

# ---
# При создания образа для docker-compose указать target docker
FROM builder AS docker

# Рабочая директория
WORKDIR /var/www/shop/

COPY . /var/www/shop/

# Устанавливаем Symfony CLI
RUN echo 'deb [trusted=yes] https://repo.symfony.com/apt/ /' | tee /etc/apt/sources.list.d/symfony-cli.list \
&& apt update \
&& apt install -y symfony-cli

# При первом создании проекта закоментировать эту строку, создать скелет проекта, перенести содержимое в корень и раскомментировать
CMD composer require symfony/requirements-checker \
  && php-fpm