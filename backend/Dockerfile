FROM php:8.2-fpm-alpine

WORKDIR /app

# Instale dependências do sistema necessárias para PHP e Laravel
RUN apk add --no-cache git curl libzip-dev libpng-dev libjpeg-turbo-dev \
    && docker-php-ext-install pdo_mysql zip gd # gd para QR code

# Se ainda usar MongoDB (e conseguir resolver a extensão)
# RUN pecl install mongodb && docker-php-ext-enable mongodb

# Instale o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copie apenas os ficheiros de dependência do Composer primeiro para cache
COPY backend/composer.json backend/composer.lock /app/
RUN composer install --no-dev --optimize-autoloader --no-autoloader

# Copie o restante da aplicação
COPY backend/ /app/

# Reconstrua o autoloader FINAL após copiar todos os ficheiros
RUN composer dump-autoload --optimize

# Limpe caches do Laravel (para produção)
RUN php artisan optimize:clear \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
