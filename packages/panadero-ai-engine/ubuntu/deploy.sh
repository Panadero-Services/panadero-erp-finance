#!/bin/bash

# Panadero AI Engine - Ubuntu Deployment Script
# This script deploys the AI engine to an Ubuntu server

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
APP_DIR="/opt/panadero-ai-engine"
NGINX_DIR="/etc/nginx"
SSL_DIR="/etc/letsencrypt"
DOMAIN_NAME="ai-engine.panadero.services"
ADMIN_DOMAIN_NAME="admin.ai-engine.panadero.services"
EMAIL="luis@panadero.services"

echo -e "${BLUE}🚀 Panadero AI Engine - Ubuntu Deployment${NC}"
echo "================================================"

# Check if running as root
if [ "$EUID" -eq 0 ]; then
    echo -e "${RED}❌ Please do not run this script as root${NC}"
    exit 1
fi

# Check if application directory exists
if [ ! -d "$APP_DIR" ]; then
    echo -e "${YELLOW}📁 Creating application directory...${NC}"
    sudo mkdir -p $APP_DIR
    sudo chown $USER:$USER $APP_DIR
fi

# Copy application files
echo -e "${YELLOW}📦 Copying application files...${NC}"
cp -r . $APP_DIR/
cd $APP_DIR

# Install dependencies
echo -e "${YELLOW}📦 Installing dependencies...${NC}"
npm install --production

# Create necessary directories
echo -e "${YELLOW}📁 Creating necessary directories...${NC}"
mkdir -p logs
mkdir -p data
mkdir -p models

# Set up file permissions
echo -e "${YELLOW}🔐 Setting up file permissions...${NC}"
sudo chown -R $USER:$USER $APP_DIR
chmod +x $APP_DIR/ubuntu/*.sh

# Configure Nginx
echo -e "${YELLOW}🌐 Configuring Nginx...${NC}"
sudo cp $APP_DIR/ubuntu/nginx.conf $NGINX_DIR/sites-available/panadero-ai-engine
sudo ln -sf $NGINX_DIR/sites-available/panadero-ai-engine $NGINX_DIR/sites-enabled/
sudo rm -f $NGINX_DIR/sites-enabled/default

# Test Nginx configuration
echo -e "${YELLOW}🧪 Testing Nginx configuration...${NC}"
sudo nginx -t

# Restart Nginx
echo -e "${YELLOW}🔄 Restarting Nginx...${NC}"
sudo systemctl restart nginx

# Set up PM2
echo -e "${YELLOW}⚙️ Setting up PM2...${NC}"
cp $APP_DIR/ubuntu/pm2.config.js $APP_DIR/ecosystem.config.js

# Start application with PM2
echo -e "${YELLOW}🚀 Starting application with PM2...${NC}"
pm2 start ecosystem.config.js --env production

# Save PM2 configuration
pm2 save

# Set up PM2 startup script
pm2 startup
echo -e "${YELLOW}⚠️  Please run the command above as root to enable PM2 startup${NC}"

# Set up SSL certificates (optional)
read -p "Do you want to set up SSL certificates? (y/n): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo -e "${YELLOW}🔒 Setting up SSL certificates...${NC}"
    
    # Install Certbot if not already installed
    if ! command -v certbot &> /dev/null; then
        sudo apt install -y certbot python3-certbot-nginx
    fi
    
    # Get SSL certificates
    sudo certbot --nginx -d $DOMAIN_NAME -d $ADMIN_DOMAIN_NAME --email $EMAIL --agree-tos --non-interactive
    
    # Set up auto-renewal
    echo -e "${YELLOW}🔄 Setting up SSL auto-renewal...${NC}"
    (crontab -l 2>/dev/null; echo "0 12 * * * /usr/bin/certbot renew --quiet") | crontab -
fi

# Set up monitoring
echo -e "${YELLOW}📊 Setting up monitoring...${NC}"
pm2 install pm2-logrotate
pm2 set pm2-logrotate:max_size 10M
pm2 set pm2-logrotate:retain 30
pm2 set pm2-logrotate:compress true

# Set up health check
echo -e "${YELLOW}🏥 Setting up health check...${NC}"
cat > $APP_DIR/health-check.sh << 'EOF'
#!/bin/bash
# Health check script for Panadero AI Engine

API_URL="http://localhost:3000/api/health"
ADMIN_URL="http://localhost:3001"

# Check API health
if curl -s $API_URL > /dev/null; then
    echo "✅ API is healthy"
else
    echo "❌ API is down"
    pm2 restart panadero-ai-engine-api
fi

# Check Admin interface
if curl -s $ADMIN_URL > /dev/null; then
    echo "✅ Admin interface is healthy"
else
    echo "❌ Admin interface is down"
    pm2 restart panadero-ai-engine-admin
fi
EOF

chmod +x $APP_DIR/health-check.sh

# Set up cron job for health check
(crontab -l 2>/dev/null; echo "*/5 * * * * $APP_DIR/health-check.sh >> $APP_DIR/logs/health-check.log 2>&1") | crontab -

# Create backup script
echo -e "${YELLOW}💾 Creating backup script...${NC}"
cat > $APP_DIR/backup.sh << 'EOF'
#!/bin/bash
# Backup script for Panadero AI Engine

BACKUP_DIR="/opt/backups/panadero-ai-engine"
DATE=$(date +%Y%m%d_%H%M%S)
APP_DIR="/opt/panadero-ai-engine"

# Create backup directory
mkdir -p $BACKUP_DIR

# Create backup
tar -czf $BACKUP_DIR/panadero-ai-engine_$DATE.tar.gz -C $APP_DIR .

# Keep only last 7 days of backups
find $BACKUP_DIR -name "panadero-ai-engine_*.tar.gz" -mtime +7 -delete

echo "Backup completed: panadero-ai-engine_$DATE.tar.gz"
EOF

chmod +x $APP_DIR/backup.sh

# Set up daily backup
(crontab -l 2>/dev/null; echo "0 2 * * * $APP_DIR/backup.sh >> $APP_DIR/logs/backup.log 2>&1") | crontab -

# Test the deployment
echo -e "${YELLOW}🧪 Testing deployment...${NC}"
sleep 5

# Check if services are running
if pm2 list | grep -q "panadero-ai-engine-api.*online"; then
    echo -e "${GREEN}✅ API service is running${NC}"
else
    echo -e "${RED}❌ API service is not running${NC}"
fi

if pm2 list | grep -q "panadero-ai-engine-admin.*online"; then
    echo -e "${GREEN}✅ Admin service is running${NC}"
else
    echo -e "${RED}❌ Admin service is not running${NC}"
fi

# Test endpoints
echo -e "${YELLOW}🌐 Testing endpoints...${NC}"
if curl -s http://localhost/api/health > /dev/null; then
    echo -e "${GREEN}✅ API endpoint is accessible${NC}"
else
    echo -e "${RED}❌ API endpoint is not accessible${NC}"
fi

if curl -s http://localhost/admin/ > /dev/null; then
    echo -e "${GREEN}✅ Admin interface is accessible${NC}"
else
    echo -e "${RED}❌ Admin interface is not accessible${NC}"
fi

echo -e "${GREEN}✅ Deployment completed successfully!${NC}"
echo "================================================"
echo -e "${BLUE}📊 Deployment Summary:${NC}"
echo "Application Directory: $APP_DIR"
echo "Public URL: http://$DOMAIN_NAME"
echo "Admin URL: http://$ADMIN_DOMAIN_NAME"
echo "API Endpoint: http://$DOMAIN_NAME/api"
echo "Health Check: http://$DOMAIN_NAME/health"
echo ""
echo -e "${YELLOW}Management Commands:${NC}"
echo "PM2 Status: pm2 status"
echo "PM2 Logs: pm2 logs"
echo "PM2 Restart: pm2 restart all"
echo "PM2 Stop: pm2 stop all"
echo "PM2 Start: pm2 start all"
echo ""
echo -e "${YELLOW}Log Files:${NC}"
echo "API Logs: $APP_DIR/logs/api.log"
echo "Admin Logs: $APP_DIR/logs/admin.log"
echo "Health Check: $APP_DIR/logs/health-check.log"
echo "Backup Logs: $APP_DIR/logs/backup.log"
echo ""
echo -e "${GREEN}🎉 Your AI Engine is now live on Ubuntu!${NC}"

# Show PM2 status
echo -e "${YELLOW}📊 Current PM2 Status:${NC}"
pm2 status
