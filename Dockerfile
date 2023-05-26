FROM php:8.1-fpm

# Copy composer.lock and composer.json into the working directory
COPY composer.lock composer.json /var/www/html/app/

# Set working directory
WORKDIR /var/www/html/app/

# Install dependencies for the operating system software
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    zip \
    jpegoptim optipng pngquant gifsicle \
    libzip-dev \
    unzip \
    libonig-dev \
    curl \
    nano \
    nodejs

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions for php
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd
RUN echo 'memory_limit = -1' >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini;

# Install composer (php package manager)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy existing application directory contents to the working directory
COPY . /var/www/html/app

# Run composer install
RUN composer install
RUN composer dump-autoload

# Assign permissions of the working directory to the www-data user
RUN chown -R $USER:www-data /var/www/html/app/public/
RUN chown -R $USER:www-data /var/www/html/app/storage/
RUN chown -R $USER:www-data /var/www/html/app/bootstrap/cache/
RUN chmod -R 775 /var/www/html/app/public/
RUN chmod -R 775 /var/www/html/app/storage/
RUN chmod -R 775 /var/www/html/app/bootstrap/cache/

# Copy existing application directory contents to the working directory
COPY . /var/www/html/app

# Expose port 9000 and start php-fpm server (for FastCGI Process Manager)
EXPOSE 9000
CMD ["php-fpm"]
