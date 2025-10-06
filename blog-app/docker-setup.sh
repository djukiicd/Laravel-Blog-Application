#!/bin/bash

# Docker Setup Script for Laravel Blog Application

echo "🐳 Setting up Laravel Blog with Docker..."

# Copy environment file for Docker
if [ ! -f .env.docker ]; then
    echo "📝 Creating Docker environment file..."
    cp .env.example .env.docker
    
    # Update database settings for Docker
    sed -i 's/DB_CONNECTION=sqlite/DB_CONNECTION=mysql/' .env.docker
    sed -i 's/DB_HOST=127.0.0.1/DB_HOST=db/' .env.docker
    sed -i 's/DB_PORT=3306/DB_PORT=3306/' .env.docker
    sed -i 's/DB_DATABASE=laravel/DB_DATABASE=blog_app/' .env.docker
    sed -i 's/DB_USERNAME=root/DB_USERNAME=blog_user/' .env.docker
    sed -i 's/DB_PASSWORD=/DB_PASSWORD=blog_password/' .env.docker
    sed -i 's/APP_URL=http:\/\/localhost/APP_URL=http:\/\/localhost:8000/' .env.docker
fi

# Build and start containers
echo "🔨 Building Docker containers..."
docker-compose build

echo "🚀 Starting containers..."
docker-compose up -d

# Wait for database to be ready
echo "⏳ Waiting for database to be ready..."
sleep 30

# Run Laravel setup commands
echo "⚙️  Setting up Laravel application..."
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan db:seed --force

echo "✅ Setup complete!"
echo ""
echo "🌐 Your application is available at:"
echo "   - Blog: http://localhost:8000"
echo "   - phpMyAdmin: http://localhost:8080"
echo ""
echo "🔑 Default login credentials:"
echo "   - Email: admin@example.com"
echo "   - Password: password"
echo ""
echo "📝 Useful commands:"
echo "   - View logs: docker-compose logs -f"
echo "   - Stop containers: docker-compose down"
echo "   - Restart containers: docker-compose restart"
