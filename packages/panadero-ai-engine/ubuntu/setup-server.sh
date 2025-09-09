#!/bin/bash

# Panadero AI Engine - Ubuntu Server Setup Script
# This script sets up an Ubuntu server for deploying the AI engine

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
DOMAIN_NAME="ai-engine.panadero.services"
ADMIN_DOMAIN_NAME="admin.ai-engine.panadero.services"
EMAIL="luis@panadero.services"
APP_DIR="/opt/panadero-ai-engine"
NGINX_DIR="/etc/nginx"
SSL_DIR="/etc/letsencrypt"

echo -e "${BLUE}🚀 Panadero AI Engine - Ubuntu Server Setup${NC}"
echo "================================================"

# Update system
echo -e "${YELLOW}📦 Updating system packages...${NC}"
sudo apt update && sudo apt upgrade -y

# Install essential packages
echo -e "${YELLOW}📦 Installing essential packages...${NC}"
sudo apt install -y curl wget git unzip software-properties-common apt-transport-https ca-certificates gnupg lsb-release

# Install Node.js 18.x
echo -e "${YELLOW}📦 Installing Node.js 18.x...${NC}"
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Verify Node.js installation
echo -e "${GREEN}✅ Node.js version: $(node --version)${NC}"
echo -e "${GREEN}✅ npm version: $(npm --version)${NC}"

# Install PM2 globally
echo -e "${YELLOW}📦 Installing PM2 process manager...${NC}"
sudo npm install -g pm2

# Install Nginx
echo -e "${YELLOW}📦 Installing Nginx...${NC}"
sudo apt install -y nginx

# Start and enable Nginx
sudo systemctl start nginx
sudo systemctl enable nginx

# Install Certbot for SSL
echo -e "${YELLOW}📦 Installing Certbot for SSL certificates...${NC}"
sudo apt install -y certbot python3-certbot-nginx

# Create application directory
echo -e "${YELLOW}📁 Creating application directory...${NC}"
sudo mkdir -p $APP_DIR
sudo chown $USER:$USER $APP_DIR

# Create system user for the application
echo -e "${YELLOW}👤 Creating system user...${NC}"
sudo useradd -r -s /bin/false -d $APP_DIR panadero-ai || echo "User already exists"

# Set up firewall
echo -e "${YELLOW}🔥 Configuring firewall...${NC}"
sudo ufw allow OpenSSH
sudo ufw allow 'Nginx Full'
sudo ufw --force enable

# Install additional tools
echo -e "${YELLOW}📦 Installing additional tools...${NC}"
sudo apt install -y htop tree jq

# Create log directories
echo -e "${YELLOW}📁 Creating log directories...${NC}"
sudo mkdir -p /var/log/panadero-ai
sudo chown $USER:$USER /var/log/panadero-ai

# Set up log rotation
echo -e "${YELLOW}📋 Setting up log rotation...${NC}"
sudo tee /etc/logrotate.d/panadero-ai > /dev/null <<EOF
/var/log/panadero-ai/*.log {
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
    create 644 $USER $USER
    postrotate
        pm2 reloadLogs
    endscript
}
EOF

echo -e "${GREEN}✅ Ubuntu server setup completed!${NC}"
echo "================================================"
echo -e "${BLUE}📊 Server Information:${NC}"
echo "Node.js: $(node --version)"
echo "npm: $(npm --version)"
echo "PM2: $(pm2 --version)"
echo "Nginx: $(nginx -v 2>&1)"
echo "Application Directory: $APP_DIR"
echo "Log Directory: /var/log/panadero-ai"
echo ""
echo -e "${YELLOW}Next Steps:${NC}"
echo "1. Deploy your application to $APP_DIR"
echo "2. Configure Nginx virtual hosts"
echo "3. Set up SSL certificates"
echo "4. Start your application with PM2"
echo "5. Configure monitoring and backups"

echo -e "${GREEN}✨ Server is ready for deployment!${NC}"