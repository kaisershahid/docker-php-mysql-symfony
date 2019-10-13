FROM php:7.4-apache

COPY ./entrypoint.sh /

RUN a2enmod rewrite

# INSTALL: php extensions
RUN apt-get update && apt-get install -y \
    libmemcached-dev \
    uuid-dev \
    zlib1g-dev \
    acl \
    libicu-dev --no-install-recommends \
  && docker-php-ext-configure intl \
  && docker-php-ext-configure zip \
  && docker-php-ext-install \
    zip \
    intl \
    mysqli \
    pdo_mysql \
    opcache \
    pcntl \
    mbstring

# INSTALL: mysql client
RUN apt-get -y install mysql-client

# INSTALL: composer
RUN \
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php -r "if (hash_file('sha384', 'composer-setup.php') === 'a5c698ffe4b8e849a443b120cd5ba38043260d5c4023dbf93e1558871f1f07f58274fc6f4c93bcfd858c6bd0775cd8d1') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" && \
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
    php -r "unlink('composer-setup.php');"

# --- setup local path stuff here

COPY ./http.conf /etc/apache2/sites-enabled/000-default.conf

RUN useradd -u 1000 YOUR_APP
RUN mkdir -p /home/YOUR_APP
RUN touch /home/YOUR_APP/.env
RUN mkdir /home/YOUR_APP/var

COPY composer.json /home/YOUR_APP/composer.json
RUN composer install -d /home/YOUR_APP \
    --no-dev \
    -n \
    --prefer-dist \
    --optimize-autoloader \
    --apcu-autoloader

RUN chmod a+x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]
