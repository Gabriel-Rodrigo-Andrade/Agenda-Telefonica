FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
  zip unzip git curl libpng-dev libonig-dev libxml2-dev \
  && docker-php-ext-install pdo pdo_mysql

# Configura timezone para America/Sao_Paulo
RUN echo "America/Sao_Paulo" > /etc/timezone && \
  dpkg-reconfigure -f noninteractive tzdata

# Copia configuração do php.ini com timezone
RUN echo "date.timezone = America/Sao_Paulo" > /usr/local/etc/php/conf.d/timezone.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/backend

CMD ["php-fpm"]