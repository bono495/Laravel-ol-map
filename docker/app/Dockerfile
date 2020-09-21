FROM php:7.4-apache

RUN apt-get update \
    && mkdir -p /usr/share/man/man1 \
    && mkdir -p /usr/share/man/man7 \
    && apt-get install -y --no-install-recommends \
        libicu-dev \
        libmcrypt-dev \
        postgresql \
        libpq-dev \
        libfontconfig1 \
        libxrender1 \
        libzip-dev \
        libxext6 \
        libpng-dev \
        git \
        unzip \
        zip \
        nano \
        zlib1g-dev \
    && rm -rf /var/lib/apt/lists/*

# Install composer
RUN curl -Lso composer-setup.php https://getcomposer.org/installer && \
    EXPECTED_SIGNATURE=$(curl -Ls https://composer.github.io/installer.sig) && \
    ACTUAL_SIGNATURE=$(sha384sum composer-setup.php | awk '{print $1}') && \
    \
    if [ "$EXPECTED_SIGNATURE" != "$ACTUAL_SIGNATURE" ]; then \
        >&2 echo 'ERROR: Invalid installer signature'; \
        rm composer-setup.php; \
        exit 1; \
    fi;\
    \
    php composer-setup.php --filename=composer --install-dir=/usr/local/bin; \
    RESULT=$?; \
    rm composer-setup.php; \
    exit $RESULT

# Install PHP extensions
RUN docker-php-ext-install -j5 \
        intl \
        pdo_pgsql \
        opcache \
        gd \
        zip;

# Copy the .env file to docker
#COPY .env.example .env

# Apache modules
RUN a2enmod rewrite headers expires

# PHP configuration
COPY config/php.ini /usr/local/etc/php/conf.d/boom-php.ini

# Apache config
COPY config/apache.conf /etc/apache2/sites-enabled/000-default.conf
