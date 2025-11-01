# Use Laravel Sail's PHP base image
FROM laravelsail/php80-composer

# Set working directory inside container
WORKDIR /var/www/html

# Copy all project files into the container
COPY . .

# Ensure setup.sh is executable
RUN chmod +x setup.sh

# Install Composer dependencies (including Sail)
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Optional: cache Laravel config/routes/views for faster boot
# RUN php artisan config:cache \
#     && php artisan route:cache \
#     && php artisan view:cache || true

# Run setup.sh when the container starts
CMD ["/var/www/html/setup.sh"]