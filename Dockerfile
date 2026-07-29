FROM php:8.4-apache
RUN apt-get update && apt-get install -y zip unzip git curl libzip-dev libonig-dev && docker-php-ext-install pdo pdo_mysql zip mbstring && a2enmod rewrite && apt-get clean
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf && sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf
WORKDIR /var/www/html
COPY . .
RUN chown -R www-data:www-data /var/www/html
