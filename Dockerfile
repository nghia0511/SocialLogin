# Use PHP 8 with Apache as the base image
FROM php:8-apache

RUN apt-get update -y

# Install underlying dependencies
RUN apt-get install -y \
    git \
    libcurl4-openssl-dev \
    libicu-dev \
    libpng-dev \
    unzip \
    zip \
    zlib1g-dev

# Install PHP extensions
RUN docker-php-ext-install \
    bcmath \
    curl \
    gd \
    intl \
    pdo_mysql

# Install Xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Configure Xdebug
RUN echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the working directory
WORKDIR /var/www/html

# Copy the application source code into the container
COPY . /var/www/html

# Set permissions for the project directory
RUN chown -R www-data:www-data /var/www/html

#change uid and gid of apache to docker user uid/gid
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

#change the web_root to laravel /var/www/html/public folder
RUN sed -i -e "s/html/html\/webroot/g" /etc/apache2/sites-enabled/000-default.conf

# enable apache module rewrite
RUN a2enmod rewrite

# Expose port 80 to access the application from outside
EXPOSE 80

# Start Apache when the container runs
CMD ["apache2-foreground"]
