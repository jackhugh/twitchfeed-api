FROM php:8-apache

# Install PDO php extension
RUN docker-php-ext-install pdo_mysql

# Enable mod rewrite in apache
RUN a2enmod rewrite

# Create ini file from default dev ini file
RUN cp "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Change document root
ENV APACHE_DOCUMENT_ROOT /var/www/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Give file permissions to apache so php can edit files
RUN chown --recursive www-data:www-data /var/www

# Add default user to apache group
RUN usermod -u 1000 www-data
RUN usermod -G staff www-data