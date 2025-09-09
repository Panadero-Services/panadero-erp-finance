# Panadero AI Engine - Ubuntu Deployment Guide

This guide explains how to deploy the Panadero AI Engine on an Ubuntu server.

## 🚀 Quick Start

### 1. Server Setup

First, set up your Ubuntu server with all necessary dependencies:

```bash
# Run the server setup script
chmod +x ubuntu/setup-server.sh
./ubuntu/setup-server.sh
```

This script will install:
- Node.js 18.x
- PM2 process manager
- Nginx web server
- Certbot for SSL certificates
- Essential system packages

### 2. Deploy Application

Deploy the AI engine to your server:

```bash
# Run the deployment script
chmod +x ubuntu/deploy.sh
./ubuntu/deploy.sh
```

This script will:
- Copy application files to `/opt/panadero-ai-engine`
- Install dependencies
- Configure Nginx
- Set up PM2 processes
- Configure SSL certificates (optional)
- Set up monitoring and backups

## 📁 Directory Structure

```
/opt/panadero-ai-engine/
├── src/                    # AI Engine source code
├── admin/                  # Admin interface
├── public/                 # Public metrics page
├── ubuntu/                 # Ubuntu deployment files
├── logs/                   # Application logs
├── data/                   # Data storage
├── models/                 # AI models
├── ecosystem.config.js     # PM2 configuration
└── package.json           # Dependencies
```

## 🔧 Configuration

### Nginx Configuration

The Nginx configuration is located at `/etc/nginx/sites-available/panadero-ai-engine` and includes:

- **Public Interface**: `ai-engine.panadero.services`
- **Admin Interface**: `admin.ai-engine.panadero.services`
- **API Endpoints**: `/api/*`
- **Rate Limiting**: 10 req/s for API, 5 req/s for admin
- **Security Headers**: XSS protection, CSRF protection, etc.

### PM2 Configuration

PM2 manages the following processes:

- **API Server**: `panadero-ai-engine-api` (port 3000)
- **Admin Server**: `panadero-ai-engine-admin` (port 3001)

## 🌐 URLs

After deployment, your AI engine will be available at:

- **Public Metrics**: `http://ai-engine.panadero.services`
- **Admin Dashboard**: `http://admin.ai-engine.panadero.services`
- **API Endpoint**: `http://ai-engine.panadero.services/api`
- **Health Check**: `http://ai-engine.panadero.services/health`

## 📊 Management Commands

### PM2 Commands

```bash
# View status
pm2 status

# View logs
pm2 logs

# Restart all services
pm2 restart all

# Stop all services
pm2 stop all

# Start all services
pm2 start all

# View specific service logs
pm2 logs panadero-ai-engine-api
pm2 logs panadero-ai-engine-admin
```

### Nginx Commands

```bash
# Test configuration
sudo nginx -t

# Reload configuration
sudo systemctl reload nginx

# Restart Nginx
sudo systemctl restart nginx

# View logs
sudo tail -f /var/log/nginx/ai_engine_access.log
sudo tail -f /var/log/nginx/ai_engine_error.log
```

## 🔒 SSL Setup

To enable HTTPS:

1. **Automatic Setup** (recommended):
   ```bash
   sudo certbot --nginx -d ai-engine.panadero.services -d admin.ai-engine.panadero.services
   ```

2. **Manual Setup**:
   - Place your SSL certificates in `/etc/letsencrypt/live/`
   - Update the Nginx configuration
   - Reload Nginx

## 📈 Monitoring

### Health Checks

The system includes automatic health checks that run every 5 minutes:

```bash
# View health check logs
tail -f /opt/panadero-ai-engine/logs/health-check.log

# Run health check manually
/opt/panadero-ai-engine/health-check.sh
```

### Log Files

- **API Logs**: `/var/log/panadero-ai/api.log`
- **Admin Logs**: `/var/log/panadero-ai/admin.log`
- **Health Check**: `/opt/panadero-ai-engine/logs/health-check.log`
- **Backup Logs**: `/opt/panadero-ai-engine/logs/backup.log`

### System Monitoring

```bash
# View system resources
htop

# View disk usage
df -h

# View memory usage
free -h

# View PM2 monitoring
pm2 monit
```

## 💾 Backup

### Automatic Backups

Daily backups are automatically created at 2 AM:

```bash
# View backup logs
tail -f /opt/panadero-ai-engine/logs/backup.log

# Run backup manually
/opt/panadero-ai-engine/backup.sh
```

### Manual Backup

```bash
# Create backup
tar -czf panadero-ai-engine-backup-$(date +%Y%m%d).tar.gz -C /opt/panadero-ai-engine .

# Restore backup
tar -xzf panadero-ai-engine-backup-YYYYMMDD.tar.gz -C /opt/panadero-ai-engine
```

## 🔄 Updates

To update the AI engine:

1. **Pull latest changes**:
   ```bash
   cd /opt/panadero-ai-engine
   git pull origin main
   ```

2. **Install dependencies**:
   ```bash
   npm install --production
   ```

3. **Restart services**:
   ```bash
   pm2 restart all
   ```

## 🚨 Troubleshooting

### Common Issues

1. **Services not starting**:
   ```bash
   pm2 logs
   sudo systemctl status nginx
   ```

2. **Port conflicts**:
   ```bash
   sudo netstat -tlnp | grep :3000
   sudo netstat -tlnp | grep :3001
   ```

3. **Permission issues**:
   ```bash
   sudo chown -R $USER:$USER /opt/panadero-ai-engine
   ```

4. **Nginx configuration errors**:
   ```bash
   sudo nginx -t
   sudo tail -f /var/log/nginx/error.log
   ```

### Log Analysis

```bash
# View recent errors
grep -i error /var/log/panadero-ai/*.log

# View high memory usage
grep -i "memory" /var/log/panadero-ai/*.log

# View API requests
tail -f /var/log/nginx/ai_engine_access.log
```

## 🔐 Security

### Firewall Configuration

```bash
# View firewall status
sudo ufw status

# Allow specific ports
sudo ufw allow 22    # SSH
sudo ufw allow 80    # HTTP
sudo ufw allow 443   # HTTPS
```

### SSL Certificate Renewal

```bash
# Test renewal
sudo certbot renew --dry-run

# Manual renewal
sudo certbot renew
```

## 📞 Support

For issues or questions:

- **Logs**: Check `/var/log/panadero-ai/` and `/var/log/nginx/`
- **Status**: Run `pm2 status` and `sudo systemctl status nginx`
- **Health**: Run `/opt/panadero-ai-engine/health-check.sh`

## 🎯 Next Steps

After successful deployment:

1. **Configure DNS**: Point your domains to the server IP
2. **Set up monitoring**: Configure alerts for system resources
3. **Enable backups**: Verify backup scripts are working
4. **Test endpoints**: Verify all API endpoints are working
5. **Set up CI/CD**: Configure automated deployments

---

**Note**: This deployment is designed for production use. Make sure to:
- Use strong passwords for admin access
- Regularly update system packages
- Monitor system resources
- Keep backups up to date
- Review security logs regularly
