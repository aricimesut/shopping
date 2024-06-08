# PHP'nin resmi image'ını kullan
FROM php:8.2-fpm

# Sistem bağımlılıklarını yükle
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl

# Composer'ı yükle
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Laravel bağımlılıklarını yüklemek için gerekli PHP eklentilerini yükle
RUN docker-php-ext-install pdo mbstring exif pcntl bcmath gd pdo_mysql

# Çalışma dizinini ayarla
WORKDIR /var/www

# Laravel uygulamasının dosyalarını kopyala
COPY . .

# Bağımlılıkları yükle
RUN composer install --ignore-platform-reqs

# Cache, oturum ve günlükler için gerekli dizinlerin izinlerini ayarla
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Başlangıç komutunu belirle
CMD ["php-fpm"]

# Portu belirle
EXPOSE 9000
