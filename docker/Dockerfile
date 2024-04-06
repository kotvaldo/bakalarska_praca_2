# These values come from docker-compose as arguments, values here are defauts only
ARG PHP_IMAGE

FROM $PHP_IMAGE

# These variables must be set after FROM
ARG USER_ID=1000
ARG GROUP_ID=1000
ARG USER=web
ARG GROUP=web

ARG XDEBUG_VERSION

#Install bash
RUN apk add --no-cache bash bash-doc bash-completion

# Install git
RUN apk add --no-cache git

# Install nodejs & npm
RUN apk add --update nodejs npm

# Add curl PHP extension
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS libzip-dev curl-dev \
    && docker-php-ext-install curl

RUN apk add --no-cache freetype libpng libjpeg-turbo freetype-dev libpng-dev libjpeg-turbo-dev \
  && docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
  NPROC=$(grep -c ^processor /proc/cpuinfo 2>/dev/null || 1) \
   && docker-php-ext-install -j$(nproc) gd \
   && apk del --no-cache freetype-dev libpng-dev libjpeg-turbo-dev

# Add gd PHP extension
#RUN apk update \
#    && apk upgrade \
#    && apk add --no-cache $PHPIZE_DEPS libpng-dev libjpeg jpeg-dev libjpeg-turbo-dev freetype-dev \
#    && docker-php-ext-configure gd --with-freetype=/usr/lib/ --with-jpeg=/usr/lib/ \
#    && docker-php-ext-install gd

# Add pdo_mysql and mysqli PHP extensions
RUN docker-php-ext-install pdo_mysql mysqli

# Add mb-string PHP extension
RUN apk add --no-cache --virtual .build-deps oniguruma-dev \
    && docker-php-ext-install mbstring

# Add intl PHP extension
RUN apk add --no-cache icu-libs \
    && apk add --no-cache --virtual .build-deps icu-dev \
    && docker-php-ext-install intl

# Add zip PHP extension
#RUN apk add --no-cache zip libzip-dev \
#    && docker-php-ext-configure zip \
#    && docker-php-ext-install zip

#RUN apk add --update --no-cache libldap \
#    && apk add --update --no-cache --virtual .docker-php-ldap-dependancies openldap-dev \
#    && docker-php-ext-configure ldap \
#    && docker-php-ext-install ldap

# Install linux headers for xdebug
RUN apk add --update linux-headers

# Add PHP xdebug extension
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install xdebug-$XDEBUG_VERSION \
    && docker-php-ext-enable xdebug

# Clear unecessary packages
RUN apk del .build-deps \
    && rm -rf /var/cache/apk/* \
    && rm -rf /tmp/*

# Configure Xdebug
RUN echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.log=/tmp/xdebug.log" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=9000" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
ENV COMPOSER_HOME=/tmp
ENV DEBUG_MODE=true

#ENV PHP_IDE_CONFIG="serverName=localhost"

# Copy custom PHP settings
COPY ./php_user.ini /usr/local/etc/php/conf.d/
#RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini

# Copy www.conf for php-fpm
#COPY ./www.conf /usr/local/etc/php-fpm.d

# Set the correct user to fix permisssion isues
RUN addgroup -g $GROUP_ID $GROUP
RUN adduser -D -u $USER_ID -G $GROUP $USER
USER $USER
