FROM php:8.3-fpm

# Set working directory
WORKDIR /var/www/server

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    unzip \
    supervisor \
    curl \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo pdo_mysql mysqli zip bcmath \
    && pecl install mongodb redis \
    && docker-php-ext-enable mongodb redis \
    && rm -rf /tmp/pear

# Clear out the local repository of retrieved package files
RUN apt-get clean

# Add user for laravel application
RUN groupadd -g 1000 www && \
    useradd -u 1000 -ms /bin/bash -g www www

# Install Composer and npm packages
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


# Set the permission of the working directory
RUN mkdir -p /var/www/server/vendor && \
    chown -R www:www /var/www/server && \
    chmod -R 775 /var/www/server

# Copy existing application directory contents
COPY ./laravel /var/www/server

# Copy existing application directory permissions
COPY --chown=www:www . /var/www/server

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy supervisor configuration
COPY ./supervisord /etc/supervisor/conf.d

# Switch to the non-root user
USER www

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
