/**
 * Admin Server for Panadero AI Engine
 * This server provides the admin interface and API endpoints
 */

const express = require('express');
const path = require('path');
const cors = require('cors');
const helmet = require('helmet');
const rateLimit = require('express-rate-limit');

const app = express();
const PORT = process.env.PORT || 3001;
const API_URL = process.env.API_URL || 'http://localhost:3000';

// Middleware
app.use(helmet({
    contentSecurityPolicy: {
        directives: {
            defaultSrc: ["'self'"],
            styleSrc: ["'self'", "'unsafe-inline'", "https://cdnjs.cloudflare.com"],
            scriptSrc: ["'self'", "'unsafe-inline'", "https://cdnjs.cloudflare.com"],
            imgSrc: ["'self'", "data:", "https:"],
            connectSrc: ["'self'", API_URL]
        }
    }
}));

app.use(cors());
app.use(express.json());
app.use(express.static(path.join(__dirname)));

// Rate limiting
const limiter = rateLimit({
    windowMs: 15 * 60 * 1000, // 15 minutes
    max: 100, // limit each IP to 100 requests per windowMs
    message: 'Too many requests from this IP, please try again later.'
});
app.use('/api/', limiter);

// Admin API endpoints
app.get('/api/instances', async (req, res) => {
    try {
        // This would typically query your database or PM2
        const instances = [
            {
                id: 'instance-1',
                name: 'Production Engine',
                status: 'running',
                model: '7B',
                lastUsed: new Date().toISOString(),
                requests: 1250,
                memory: '512MB',
                cpu: '15%'
            },
            {
                id: 'instance-2',
                name: 'Development Engine',
                status: 'stopped',
                model: '7B',
                lastUsed: new Date(Date.now() - 3600000).toISOString(),
                requests: 89,
                memory: '0MB',
                cpu: '0%'
            }
        ];
        
        res.json({ instances });
    } catch (error) {
        console.error('Error fetching instances:', error);
        res.status(500).json({ error: 'Failed to fetch instances' });
    }
});

app.post('/api/instances', async (req, res) => {
    try {
        const { name, model = '7B', config = {} } = req.body;
        
        if (!name) {
            return res.status(400).json({ error: 'Instance name is required' });
        }
        
        // This would typically create a new instance
        const instance = {
            id: `instance-${Date.now()}`,
            name,
            status: 'initializing',
            model,
            config,
            createdAt: new Date().toISOString()
        };
        
        res.status(201).json({ instance });
    } catch (error) {
        console.error('Error creating instance:', error);
        res.status(500).json({ error: 'Failed to create instance' });
    }
});

app.put('/api/instances/:id/start', async (req, res) => {
    try {
        const { id } = req.params;
        
        // This would typically start the instance
        res.json({ 
            message: `Instance ${id} started successfully`,
            status: 'running'
        });
    } catch (error) {
        console.error('Error starting instance:', error);
        res.status(500).json({ error: 'Failed to start instance' });
    }
});

app.put('/api/instances/:id/stop', async (req, res) => {
    try {
        const { id } = req.params;
        
        // This would typically stop the instance
        res.json({ 
            message: `Instance ${id} stopped successfully`,
            status: 'stopped'
        });
    } catch (error) {
        console.error('Error stopping instance:', error);
        res.status(500).json({ error: 'Failed to stop instance' });
    }
});

app.delete('/api/instances/:id', async (req, res) => {
    try {
        const { id } = req.params;
        
        // This would typically delete the instance
        res.json({ 
            message: `Instance ${id} deleted successfully`
        });
    } catch (error) {
        console.error('Error deleting instance:', error);
        res.status(500).json({ error: 'Failed to delete instance' });
    }
});

app.get('/api/metrics', async (req, res) => {
    try {
        // This would typically fetch real metrics
        const metrics = {
            requests: {
                total: 1250,
                last24h: 89,
                last1h: 12
            },
            performance: {
                avgResponseTime: 245,
                p95ResponseTime: 890,
                p99ResponseTime: 1200
            },
            usage: {
                tokensGenerated: 45600,
                dataAnalyzed: 2340,
                activeUsers: 45
            },
            errors: {
                total: 12,
                last24h: 2,
                rate: 0.02
            },
            system: {
                memory: process.memoryUsage(),
                uptime: process.uptime(),
                platform: process.platform,
                nodeVersion: process.version
            }
        };
        
        res.json({ metrics });
    } catch (error) {
        console.error('Error fetching metrics:', error);
        res.status(500).json({ error: 'Failed to fetch metrics' });
    }
});

app.get('/api/logs', async (req, res) => {
    try {
        const { type = 'all', limit = 100 } = req.query;
        
        // This would typically fetch real logs
        const logs = [
            {
                timestamp: new Date().toISOString(),
                level: 'info',
                message: 'Instance started successfully',
                instance: 'instance-1'
            },
            {
                timestamp: new Date(Date.now() - 300000).toISOString(),
                level: 'warn',
                message: 'High memory usage detected',
                instance: 'instance-1'
            },
            {
                timestamp: new Date(Date.now() - 600000).toISOString(),
                level: 'error',
                message: 'Failed to process request',
                instance: 'instance-2'
            }
        ];
        
        res.json({ logs: logs.slice(0, parseInt(limit)) });
    } catch (error) {
        console.error('Error fetching logs:', error);
        res.status(500).json({ error: 'Failed to fetch logs' });
    }
});

// Health check endpoint
app.get('/health', (req, res) => {
    res.json({
        status: 'healthy',
        timestamp: new Date().toISOString(),
        uptime: process.uptime(),
        memory: process.memoryUsage()
    });
});

// Serve admin interface
app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, 'index.html'));
});

// Error handling middleware
app.use((err, req, res, next) => {
    console.error('Admin server error:', err);
    res.status(500).json({ 
        error: 'Internal server error',
        message: err.message 
    });
});

// 404 handler
app.use((req, res) => {
    res.status(404).json({ error: 'Not found' });
});

// Start server
app.listen(PORT, () => {
    console.log(`🚀 Admin server running on port ${PORT}`);
    console.log(`📊 Admin interface: http://localhost:${PORT}`);
    console.log(`🔗 API URL: ${API_URL}`);
});

module.exports = app;