#!/bin/bash

echo "🚀 Setting up Reusemart Mobile API..."

# Navigate to Laravel directory
cd "$(dirname "$0")"

# Clear all caches
echo "📝 Clearing Laravel caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Install/update dependencies
echo "📦 Installing dependencies..."
composer install --no-dev --optimize-autoloader

# Generate application key if not exists
if [ ! -f .env ]; then
    echo "📋 Creating .env file..."
    cp .env.example .env
    php artisan key:generate
fi

# Setup database
echo "🗄 Setting up database..."
php artisan migrate:fresh --seed

# Install Sanctum
echo "🔐 Setting up Sanctum..."
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# Create test user
echo "👤 Creating test user..."
php artisan tinker --execute="
\App\Models\User::create([
    'name' => 'Test Buyer',
    'email' => 'buyer@test.com',
    'password' => bcrypt('password'),
    'role_id' => 4,
    'dob' => '1990-01-01'
]);

\App\Models\Pembeli::create([
    'user_id' => 1,
    'poin_reward' => 100
]);

echo 'Test user created: buyer@test.com / password';
"

# Set permissions
echo "🔧 Setting permissions..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

# Test API endpoints
echo "🧪 Testing API endpoints..."
php artisan route:list --path=api/mobile

echo "✅ Setup complete!"
echo ""
echo "🌐 Start Laravel server with:"
echo "   php artisan serve --host=0.0.0.0 --port=8000"
echo ""
echo "📱 Test credentials:"
echo "   Email: buyer@test.com"
echo "   Password: password"
echo ""
echo "🔗 Test endpoints:"
echo "   GET  http://localhost:8000/api/mobile/test"
echo "   POST http://localhost:8000/api/mobile/login"
echo ""