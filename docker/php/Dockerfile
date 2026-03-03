FROM php:8.3-fpm

# Установка системных зависимостей
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    default-mysql-client \
    libmariadb-dev \
    libicu-dev \
    && rm -rf /var/lib/apt/lists/*

# Установка PHP расширений для Laravel
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    intl \
    opcache \
    zip

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Директория приложения (монтируется volume при запуске)
RUN mkdir -p /var/www/short-linker.loc && chown -R www-data:www-data /var/www/short-linker.loc

WORKDIR /var/www/short-linker.loc
