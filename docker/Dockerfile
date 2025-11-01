# Laravel FSM Phase 1 - GitOps Docker Image
# This container automatically pulls updates from git every 5 minutes

FROM php:8.2-fpm-alpine

# Build arguments
ARG GIT_REPO
ARG GIT_BRANCH=phase1-core
ARG GIT_TOKEN

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    wget \
    zip \
    unzip \
    nginx \
    supervisor \
    nodejs \
    npm \
    mysql-client \
    redis \
    bash \
    rsync \
    # PHP extensions dependencies
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    libxml2-dev \
    postgresql-dev \
    && rm -rf /var/cache/apk/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl \
    opcache \
    soap

# Install Redis extension
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Set working directory
WORKDIR /var/www/html

# Configure nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/site.conf /etc/nginx/http.d/default.conf

# Configure PHP-FPM
RUN echo "php_admin_value[error_log] = /var/log/php-fpm/error.log" >> /usr/local/etc/php-fpm.d/www.conf \
    && echo "php_admin_flag[log_errors] = on" >> /usr/local/etc/php-fpm.d/www.conf \
    && mkdir -p /var/log/php-fpm

# Copy PHP configuration
COPY docker/php.ini /usr/local/etc/php/conf.d/99-custom.ini

# Copy supervisor configuration
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy GitOps scripts
COPY docker/gitops-entrypoint.sh /usr/local/bin/gitops-entrypoint.sh
COPY docker/gitops-update.sh /usr/local/bin/gitops-update.sh
COPY docker/scheduler-entrypoint.sh /usr/local/bin/scheduler-entrypoint.sh
RUN chmod +x /usr/local/bin/gitops-entrypoint.sh \
    && chmod +x /usr/local/bin/gitops-update.sh \
    && chmod +x /usr/local/bin/scheduler-entrypoint.sh

# Create necessary directories
RUN mkdir -p \
    /var/www/html/storage/logs \
    /var/www/html/storage/framework/cache \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/bootstrap/cache \
    /run/nginx \
    /var/log/gitops

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Configure git (for GitOps)
RUN git config --global advice.detachedHead false \
    && git config --global user.email "gitops@laravel-fsm.local" \
    && git config --global user.name "GitOps Bot"

# Expose ports
EXPOSE 80

# Health check
HEALTHCHECK --interval=30s --timeout=10s --retries=3 --start-period=40s \
    CMD curl -f http://localhost/health || exit 1

# Environment variables
ENV GITOPS_ENABLED=true
ENV GITOPS_INTERVAL=300
ENV GITOPS_BRANCH=phase1-core
ENV GITOPS_AUTO_MIGRATE=true
ENV GITOPS_AUTO_INSTALL=true

# Start GitOps entrypoint
ENTRYPOINT ["/usr/local/bin/gitops-entrypoint.sh"]
