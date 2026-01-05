#!/bin/sh

# Fail on any error
set -e

# Run composer install if vendor folder is missing
if [ ! -d "vendor" ]; then
    composer install --no-interaction --no-progress
fi

# Run migrations (only in environments where it's safe)
# php artisan migrate --force

# Cache configuration and routes (optional for local/dev)
# php artisan config:cache
# php artisan route:cache
# php artisan view:cache

# Execute the CMD from the Dockerfile
exec "$@"
