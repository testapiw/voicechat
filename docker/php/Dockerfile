FROM php:8.4-fpm

ARG UID=1000
ARG GID=1000


USER root

# Installing dependencies
RUN apt-get update && DEBIAN_FRONTEND=noninteractive apt-get install -y \
    git zip unzip curl ca-certificates build-essential pkg-config libtool-bin \
    libpq-dev libonig-dev libzip-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*


# Installing Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create a user
RUN groupadd -g ${GID} appgroup \
    && useradd -u ${UID} -g appgroup -m appuser

WORKDIR /var/www/symfony

#  Switch to a regular user
USER appuser

# RUN chown -R appuser:appgroup /var/www/symfony

# Let's repackage the container:
# docker compose build php
# docker compose up -d
# docker compose exec php bash
