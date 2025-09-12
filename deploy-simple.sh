#!/bin/bash

# i3v2-bots Simple AWS Deployment Script
# This script deploys without private Git dependencies

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
SERVER_IP="15.188.147.145"
SERVER_USER="ubuntu"
KEY_FILE="bastion.pem"
APP_DIR="/opt/i3v2-bots"

echo -e "${BLUE}🚀 i3v2-bots Simple AWS Deployment${NC}"
echo "================================================"

# Check if key file exists
if [ ! -f "$KEY_FILE" ]; then
    echo -e "${RED}❌ Key file $KEY_FILE not found${NC}"
    exit 1
fi

# Check if server is reachable
echo -e "${YELLOW}🔍 Checking server connectivity...${NC}"
if ! ssh -i "$KEY_FILE" -o ConnectTimeout=10 "$SERVER_USER@$SERVER_IP" "echo 'Server is reachable'" > /dev/null 2>&1; then
    echo -e "${RED}❌ Cannot connect to server $SERVER_IP${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Server is reachable${NC}"

# Create application directory on server
echo -e "${YELLOW}📁 Creating application directory on server...${NC}"
ssh -i "$KEY_FILE" "$SERVER_USER@$SERVER_IP" "sudo mkdir -p $APP_DIR && sudo chown $SERVER_USER:$SERVER_USER $APP_DIR"

# Copy application files to server (excluding problematic files)
echo -e "${YELLOW}📦 Copying application files to server...${NC}"
rsync -avz --progress \
    --exclude 'node_modules' \
    --exclude '.git' \
    --exclude 'storage/logs' \
    --exclude 'storage/framework/cache' \
    --exclude 'storage/framework/sessions' \
    --exclude 'storage/framework/views' \
    --exclude 'vendor' \
    --exclude 'packages/panadero-*' \
    -e "ssh -i $KEY_FILE" \
    ./ "$SERVER_USER@$SERVER_IP:$APP_DIR/"

# Install PHP dependencies
echo -e "${YELLOW}📦 Installing PHP dependencies...${NC}"
ssh -i "$KEY_FILE" "$SERVER_USER@$SERVER_IP" "cd $APP_DIR && composer install --no-dev --optimize-autoloader"

# Create a simple package.json without private dependencies
echo -e "${YELLOW}📦 Creating simplified package.json...${NC}"
cat > simple-package.json << 'EOF'
{
  "private": true,
  "type": "module",
  "scripts": {
    "dev": "vite",
    "build": "vite build"
  },
  "devDependencies": {
    "@inertiajs/vue3": "^1.0.14",
    "@tailwindcss/forms": "^0.5.7",
    "@tailwindcss/typography": "^0.5.10",
    "@vitejs/plugin-vue": "^5.0.0",
    "autoprefixer": "^10.4.16",
    "axios": "^1.7.4",
    "laravel-vite-plugin": "^1.3.0",
    "postcss": "^8.4.32",
    "tailwindcss": "^3.4.0",
    "typescript": "^5.8.3",
    "vite": "^6.3.5",
    "vue": "^3.3.13"
  },
  "dependencies": {
    "@fortawesome/fontawesome-free": "^7.0.0",
    "@headlessui/vue": "^1.7.23",
    "@heroicons/vue": "^2.1.5",
    "date-fns": "^4.1.0",
    "decimal.js": "^10.0.0",
    "ethers": "^6.6.4",
    "express": "^5.1.0",
    "heroicons": "^2.1.5",
    "internet-available": "^1.0.0",
    "mysql": "^2.18.1",
    "node-fetch": "^3.3.2",
    "pinia": "^2.1.3",
    "pusher-js": "^8.4.0",
    "socket.io": "^4.7.5",
    "socket.io-client": "^4.8.1"
  }
}
EOF

# Copy simplified package.json to server
scp -i "$KEY_FILE" simple-package.json "$SERVER_USER@$SERVER_IP:$APP_DIR/package.json"

# Install Node.js dependencies
echo -e "${YELLOW}📦 Installing Node.js dependencies...${NC}"
ssh -i "$KEY_FILE" "$SERVER_USER@$SERVER_IP" "cd $APP_DIR && npm install --production"

# Build assets
echo -e "${YELLOW}🔨 Building assets...${NC}"
ssh -i "$KEY_FILE" "$SERVER_USER@$SERVER_IP" "cd $APP_DIR && npm run build"

# Set up Laravel environment
echo -e "${YELLOW}⚙️ Setting up Laravel environment...${NC}"
ssh -i "$KEY_FILE" "$SERVER_USER@$SERVER_IP" "cd $APP_DIR && cp .env.example .env"

# Generate application key
ssh -i "$KEY_FILE" "$SERVER_USER@$SERVER_IP" "cd $APP_DIR && php artisan key:generate"

# Set up storage permissions
ssh -i "$KEY_FILE" "$SERVER_USER@$SERVER_IP" "cd $APP_DIR && sudo chown -R www-data:www-data storage bootstrap/cache && sudo chmod -R 775 storage bootstrap/cache"

# Run database migrations
echo -e "${YELLOW}🗄️ Running database migrations...${NC}"
ssh -i "$KEY_FILE" "$SERVER_USER@$SERVER_IP" "cd $APP_DIR && php artisan migrate --force"

# Seed database
echo -e "${YELLOW}🌱 Seeding database...${NC}"
ssh -i "$KEY_FILE" "$SERVER_USER@$SERVER_IP" "cd $APP_DIR && php artisan db:seed --force"

# Configure Nginx
echo -e "${YELLOW}🌐 Configuring Nginx...${NC}"
cat > nginx.conf << 'NGINX_EOF'
server {
    listen 80;
    server_name i3v2-bots.panadero.services;
    root /opt/i3v2-bots/public;
    index index.php index.html;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
NGINX_EOF

# Copy Nginx configuration to server
scp -i "$KEY_FILE" nginx.conf "$SERVER_USER@$SERVER_IP:/tmp/nginx.conf"
ssh -i "$KEY_FILE" "$SERVER_USER@$SERVER_IP" "sudo mv /tmp/nginx.conf /etc/nginx/sites-available/i3v2-bots"

# Enable the site
ssh -i "$KEY_FILE" "$SERVER_USER@$SERVER_IP" "sudo ln -sf /etc/nginx/sites-available/i3v2-bots /etc/nginx/sites-enabled/ && sudo rm -f /etc/nginx/sites-enabled/default"

# Test Nginx configuration
echo -e "${YELLOW}🧪 Testing Nginx configuration...${NC}"
ssh -i "$KEY_FILE" "$SERVER_USER@$SERVER_IP" "sudo nginx -t"

# Restart Nginx
echo -e "${YELLOW}🔄 Restarting Nginx...${NC}"
ssh -i "$KEY_FILE" "$SERVER_USER@$SERVER_IP" "sudo systemctl restart nginx"

# Install PM2 globally
echo -e "${YELLOW}⚙️ Installing PM2...${NC}"
ssh -i "$KEY_FILE" "$SERVER_USER@$SERVER_IP" "sudo npm install -g pm2"

# Create PM2 ecosystem file
echo -e "${YELLOW}⚙️ Creating PM2 configuration...${NC}"
cat > ecosystem.config.js << 'PM2_EOF'
module.exports = {
  apps: [{
    name: 'i3v2-bots',
    script: 'artisan',
    args: 'serve --host=0.0.0.0 --port=8000',
    cwd: '/opt/i3v2-bots',
    instances: 1,
    autorestart: true,
    watch: false,
    max_memory_restart: '1G',
    env: {
      NODE_ENV: 'production',
      APP_ENV: 'production'
    }
  }]
};
PM2_EOF

# Copy PM2 configuration to server
scp -i "$KEY_FILE" ecosystem.config.js "$SERVER_USER@$SERVER_IP:$APP_DIR/"

# Start application with PM2
echo -e "${YELLOW}🚀 Starting application with PM2...${NC}"
ssh -i "$KEY_FILE" "$SERVER_USER@$SERVER_IP" "cd $APP_DIR && pm2 start ecosystem.config.js"

# Save PM2 configuration
ssh -i "$KEY_FILE" "$SERVER_USER@$SERVER_IP" "pm2 save"

# Set up PM2 startup script
echo -e "${YELLOW}⚙️ Setting up PM2 startup...${NC}"
ssh -i "$KEY_FILE" "$SERVER_USER@$SERVER_IP" "pm2 startup"

# Clean up local files
rm -f nginx.conf ecosystem.config.js simple-package.json

# Test the deployment
echo -e "${YELLOW}🧪 Testing deployment...${NC}"
sleep 5

# Check if application is running
if ssh -i "$KEY_FILE" "$SERVER_USER@$SERVER_IP" "pm2 list | grep -q 'i3v2-bots.*online'"; then
    echo -e "${GREEN}✅ Application is running${NC}"
else
    echo -e "${RED}❌ Application is not running${NC}"
fi

# Test HTTP response
echo -e "${YELLOW}🌐 Testing HTTP response...${NC}"
if curl -s "http://$SERVER_IP" > /dev/null; then
    echo -e "${GREEN}✅ Application is accessible via HTTP${NC}"
else
    echo -e "${RED}❌ Application is not accessible via HTTP${NC}"
fi

echo -e "${GREEN}✅ Deployment completed successfully!${NC}"
echo "================================================"
echo -e "${BLUE}📊 Deployment Summary:${NC}"
echo "Server IP: $SERVER_IP"
echo "Application Directory: $APP_DIR"
echo "Public URL: http://$SERVER_IP"
echo "Domain: http://i3v2-bots.panadero.services (if DNS is configured)"
echo ""
echo -e "${YELLOW}Management Commands:${NC}"
echo "SSH to server: ssh -i $KEY_FILE $SERVER_USER@$SERVER_IP"
echo "PM2 Status: pm2 status"
echo "PM2 Logs: pm2 logs"
echo "PM2 Restart: pm2 restart i3v2-bots"
echo "PM2 Stop: pm2 stop i3v2-bots"
echo "PM2 Start: pm2 start i3v2-bots"
echo ""
echo -e "${GREEN}🎉 Your i3v2-bots is now live on AWS!${NC}"
