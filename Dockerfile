FROM php:7-apache

# Enable headers module
RUN ln -s /etc/apache2/mods-available/headers.load \
  /etc/apache2/mods-enabled/headers.load

# Enable rewrite module
RUN ln -s /etc/apache2/mods-available/rewrite.load \
  /etc/apache2/mods-enabled/rewrite.load

# Install syste, build dependencies with apt
RUN DEBINA_FRONTEND=noninteractive \
  apt-get update \
  && apt-get install -y --no-install-recommends \
    libpng-dev \
    libjpeg-dev \
    libmagickwand-dev \
  && rm -rf /var/lib/apt/lists/*

# Set php.ini-development as system php config
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Install php extensions
RUN pear config-set php_ini $PHP_INI_DIR/php.ini
RUN printf "\n" | pecl install imagick
RUN docker-php-ext-enable imagick
RUN docker-php-ext-configure gd --with-jpeg
RUN docker-php-ext-install gd
RUN docker-php-ext-enable gd
RUN docker-php-ext-install mysqli
RUN docker-php-ext-enable mysqli

# Clean instalation traces
RUN apt-get autoremove -y \
    libpng-dev \
    libjpeg-dev \
    libmagickwand-dev \
  && apt-get clean \
  && rm -rf \
    /tmp/* \
    /usr/share/doc/* \
    /var/cache/* \
    /var/lib/apt/lists/* \
    /var/tmp/*
