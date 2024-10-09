FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    gnupg2 \
    iputils-ping \
    telnet \
    wget \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && apt-get install -y libzip-dev \
    && docker-php-ext-install zip

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

#Install ODBC and MSSQL drivers
RUN curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add - && \
    curl https://packages.microsoft.com/config/debian/9/prod.list > /etc/apt/sources.list.d/mssql-release.list && \
    apt-get update && ACCEPT_EULA=Y apt-get install -y msodbcsql17 mssql-tools unixodbc-dev && \
    apt-get install -y libgssapi-krb5-2

# Modify the OpenSSL configuration file
RUN sed -i 's/openssl_conf = openssl_init/openssl_conf = default_conf/' /etc/ssl/openssl.cnf && \
    echo "\n[ default_conf ]\nssl_conf = ssl_sect\n\n[ssl_sect]\nsystem_default = system_default_sect\n\n[system_default_sect]\nMinProtocol = TLSv1.2\nCipherString = DEFAULT@SECLEVEL=0" >> /etc/ssl/openssl.cnf

# Add sqlcmd to PATH for all users
RUN echo 'export PATH="$PATH:/opt/mssql-tools/bin"' >> /etc/profile.d/mssql-tools.sh && \
    echo 'export PATH="$PATH:/opt/mssql-tools/bin"' >> ~/.bashrc

# Install sqlsrv and pdo_sqlsrv extensions
RUN pecl install sqlsrv pdo_sqlsrv && \
    docker-php-ext-enable sqlsrv pdo_sqlsrv

# Set PHP configurations
RUN echo "upload_max_filesize = 100M" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "post_max_size = 100M" >> /usr/local/etc/php/conf.d/uploads.ini

# Set working directory
WORKDIR /var/www

# Copy existing application directory contents
COPY . /var/www

# Install Composer dependencies
RUN composer install

# Set correct permissions
RUN chown -R www-data:www-data /var/www
RUN find /var/www -type f -exec chmod 644 {} \;
RUN find /var/www -type d -exec chmod 755 {} \;
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache /var/www/app

# Create a script to fix permissions at runtime
RUN echo '#!/bin/sh\n\
chown -R www-data:www-data /var/www\n\
find /var/www -type f -exec chmod 644 {} \;\n\
find /var/www -type d -exec chmod 755 {} \;\n\
chmod -R 775 /var/www/storage /var/www/bootstrap/cache /var/www/app\n\
exec docker-php-entrypoint "$@"' > /usr/local/bin/docker-php-entrypoint-custom
RUN chmod +x /usr/local/bin/docker-php-entrypoint-custom

# Use the custom entrypoint
ENTRYPOINT ["/usr/local/bin/docker-php-entrypoint-custom"]
CMD ["php-fpm"]