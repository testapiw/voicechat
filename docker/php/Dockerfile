FROM php:8.4-fpm AS stage-0

ARG UID=1000
ARG GID=1000


USER root

# Installing dependencies
RUN apt-get update && DEBIAN_FRONTEND=noninteractive apt-get install -y \
    git zip unzip curl ca-certificates build-essential pkg-config libtool-bin \
    libpq-dev libonig-dev libzip-dev \
    telnet \
    && docker-php-ext-install pdo pdo_pgsql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*


# Installing Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

#COPY --from=stage-0 ./docker/php/esl/ESL.so /usr/local/lib/php/extensions/no-debug-non-zts-20240924/
#RUN ls -l /usr/local/lib/php/extensions/no-debug-non-zts-20240924
#RUN echo "extension=ESL.so" > /usr/local/etc/php/conf.d/esl.ini

# Create a user
RUN groupadd -g ${GID} appgroup \
    && useradd -u ${UID} -g appgroup -m appuser

WORKDIR /var/www/symfony

#  Switch to a regular user
USER appuser

# RUN chown -R appuser:appgroup /var/www/symfony
