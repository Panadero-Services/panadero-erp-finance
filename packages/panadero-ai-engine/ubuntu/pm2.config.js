/**
 * PM2 Configuration for Panadero AI Engine
 * This configuration manages the AI engine processes
 */

module.exports = {
  apps: [
    {
      name: 'panadero-ai-engine-api',
      script: './src/index.js',
      cwd: '/opt/panadero-ai-engine',
      instances: 2,
      exec_mode: 'cluster',
      env: {
        NODE_ENV: 'production',
        PORT: 3000,
        MODEL_SIZE: '7B',
        QUANTIZATION: 'int8',
        DEVICE: 'cpu',
        MAX_SEQUENCE_LENGTH: 2048
      },
      env_development: {
        NODE_ENV: 'development',
        PORT: 3000,
        MODEL_SIZE: '7B',
        QUANTIZATION: 'int8',
        DEVICE: 'cpu',
        MAX_SEQUENCE_LENGTH: 2048
      },
      env_staging: {
        NODE_ENV: 'staging',
        PORT: 3000,
        MODEL_SIZE: '7B',
        QUANTIZATION: 'int8',
        DEVICE: 'cpu',
        MAX_SEQUENCE_LENGTH: 2048
      },
      log_file: '/var/log/panadero-ai/api.log',
      out_file: '/var/log/panadero-ai/api-out.log',
      error_file: '/var/log/panadero-ai/api-error.log',
      log_date_format: 'YYYY-MM-DD HH:mm:ss Z',
      merge_logs: true,
      max_memory_restart: '1G',
      min_uptime: '10s',
      max_restarts: 10,
      restart_delay: 4000,
      watch: false,
      ignore_watch: ['node_modules', 'logs'],
      kill_timeout: 5000,
      wait_ready: true,
      listen_timeout: 10000,
      autorestart: true,
      max_memory_restart: '1G',
      node_args: '--max-old-space-size=1024'
    },
    {
      name: 'panadero-ai-engine-admin',
      script: './admin/server.js',
      cwd: '/opt/panadero-ai-engine',
      instances: 1,
      exec_mode: 'fork',
      env: {
        NODE_ENV: 'production',
        PORT: 3001,
        API_URL: 'http://localhost:3000'
      },
      env_development: {
        NODE_ENV: 'development',
        PORT: 3001,
        API_URL: 'http://localhost:3000'
      },
      log_file: '/var/log/panadero-ai/admin.log',
      out_file: '/var/log/panadero-ai/admin-out.log',
      error_file: '/var/log/panadero-ai/admin-error.log',
      log_date_format: 'YYYY-MM-DD HH:mm:ss Z',
      merge_logs: true,
      max_memory_restart: '512M',
      min_uptime: '10s',
      max_restarts: 5,
      restart_delay: 4000,
      watch: false,
      ignore_watch: ['node_modules', 'logs'],
      kill_timeout: 5000,
      wait_ready: true,
      listen_timeout: 10000,
      autorestart: true
    }
  ],

  deploy: {
    production: {
      user: 'ubuntu',
      host: 'your-ec2-instance.amazonaws.com',
      ref: 'origin/main',
      repo: 'https://github.com/panadero/ai-engine.git',
      path: '/opt/panadero-ai-engine',
      'pre-deploy-local': '',
      'post-deploy': 'npm install && pm2 reload ecosystem.config.js --env production',
      'pre-setup': ''
    },
    staging: {
      user: 'ubuntu',
      host: 'your-staging-instance.amazonaws.com',
      ref: 'origin/develop',
      repo: 'https://github.com/panadero/ai-engine.git',
      path: '/opt/panadero-ai-engine',
      'pre-deploy-local': '',
      'post-deploy': 'npm install && pm2 reload ecosystem.config.js --env staging',
      'pre-setup': ''
    }
  }
};
