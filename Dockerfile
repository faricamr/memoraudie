FROM php:8.2-apache

# Install FFmpeg and system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    ffmpeg \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_mysql zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Increase PHP upload limits for audio recordings
RUN echo "upload_max_filesize = 30M\npost_max_size = 35M\nmemory_limit = 128M" > /usr/local/etc/php/conf.d/uploads.ini

# Set working directory and copy application files
WORKDIR /var/www/html
COPY . /var/www/html/

# Ensure uploads directory exists and has proper permissions
RUN mkdir -p /var/www/html/uploads/temp \
    && chown -R www-data:www-data /var/www/html/uploads \
    && chmod -R 775 /var/www/html/uploads

EXPOSE 80

CMD ["apache2-foreground"]

