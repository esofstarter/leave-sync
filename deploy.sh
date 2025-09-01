#!/bin/bash

echo "🚀 Starting deployment..."

# Run Laravel backend commands inside Docker container
echo "🔹 Running Laravel backend setup..."
docker exec -it app /bin/bash -c "
  cd starter/api &&
  composer install &&
  php artisan config:clear &&
  php artisan view:clear &&
  php artisan route:clear &&
  composer dump-autoload &&
  php artisan cache:clear &&
  php artisan config:cache &&
  php artisan route:cache
"

# Run frontend build for admin panel
echo "🔹 Running Admin frontend build..."
docker exec -it node /bin/bash -c "
  cd client/admin &&
  npm install &&
  npm run build
"

echo "✅ Deployment completed successfully!"
