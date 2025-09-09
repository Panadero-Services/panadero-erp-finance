/**
 * AWS Lambda Handler for Panadero AI Engine
 * 
 * This is the main API endpoint that handles all AI engine requests
 */

const { AIEngine } = require('../src/index.js');

// Initialize the AI engine (singleton pattern)
let engine = null;

async function initializeEngine() {
  if (!engine) {
    engine = new AIEngine({
      modelSize: process.env.MODEL_SIZE || '7B',
      quantization: process.env.QUANTIZATION || 'int8',
      device: process.env.DEVICE || 'cpu',
      maxSequenceLength: parseInt(process.env.MAX_SEQUENCE_LENGTH) || 2048
    });
    
    await engine.initialize();
    console.log('✅ AI Engine initialized successfully');
  }
  return engine;
}

// CORS headers
const corsHeaders = {
  'Access-Control-Allow-Origin': '*',
  'Access-Control-Allow-Headers': 'Content-Type,X-Amz-Date,Authorization,X-Api-Key,X-Amz-Security-Token',
  'Access-Control-Allow-Methods': 'GET,POST,PUT,DELETE,OPTIONS'
};

// Main Lambda handler
exports.handler = async (event) => {
  console.log('🚀 Lambda invoked:', JSON.stringify(event, null, 2));
  
  try {
    // Handle CORS preflight
    if (event.httpMethod === 'OPTIONS') {
      return {
        statusCode: 200,
        headers: corsHeaders,
        body: JSON.stringify({ message: 'CORS preflight' })
      };
    }
    
    // Initialize engine
    const aiEngine = await initializeEngine();
    
    // Parse request
    const { httpMethod, path, body, queryStringParameters } = event;
    const requestBody = body ? JSON.parse(body) : {};
    
    // Route requests
    let response;
    
    switch (path) {
      case '/api/health':
        response = await handleHealth();
        break;
        
      case '/api/generate':
        response = await handleGenerate(aiEngine, requestBody);
        break;
        
      case '/api/analyze':
        response = await handleAnalyze(aiEngine, requestBody);
        break;
        
      case '/api/metrics':
        response = await handleMetrics(aiEngine, queryStringParameters);
        break;
        
      case '/api/admin/instances':
        response = await handleAdminInstances(aiEngine, httpMethod, requestBody);
        break;
        
      case '/api/admin/status':
        response = await handleAdminStatus(aiEngine);
        break;
        
      default:
        response = {
          statusCode: 404,
          body: JSON.stringify({ error: 'Endpoint not found' })
        };
    }
    
    // Add CORS headers to response
    response.headers = { ...response.headers, ...corsHeaders };
    
    return response;
    
  } catch (error) {
    console.error('❌ Lambda error:', error);
    
    return {
      statusCode: 500,
      headers: corsHeaders,
      body: JSON.stringify({
        error: 'Internal server error',
        message: error.message,
        timestamp: new Date().toISOString()
      })
    };
  }
};

// Health check endpoint
async function handleHealth() {
  return {
    statusCode: 200,
    body: JSON.stringify({
      status: 'healthy',
      timestamp: new Date().toISOString(),
      version: '1.0.0',
      service: 'panadero-ai-engine'
    })
  };
}

// Text generation endpoint
async function handleGenerate(engine, body) {
  const { prompt, maxTokens = 100, temperature = 0.7, model = 'default' } = body;
  
  if (!prompt) {
    return {
      statusCode: 400,
      body: JSON.stringify({ error: 'Prompt is required' })
    };
  }
  
  try {
    const startTime = Date.now();
    
    const result = await engine.generate({
      prompt,
      maxTokens,
      temperature,
      model
    });
    
    const duration = Date.now() - startTime;
    
    // Log metrics
    await logMetrics('generate', { duration, tokens: result.tokens || 0 });
    
    return {
      statusCode: 200,
      body: JSON.stringify({
        text: result.text,
        tokens: result.tokens || 0,
        duration: duration,
        model: model,
        timestamp: new Date().toISOString()
      })
    };
    
  } catch (error) {
    console.error('Generate error:', error);
    return {
      statusCode: 500,
      body: JSON.stringify({ error: 'Generation failed', message: error.message })
    };
  }
}

// Data analysis endpoint
async function handleAnalyze(engine, body) {
  const { data, task, options = {} } = body;
  
  if (!data || !task) {
    return {
      statusCode: 400,
      body: JSON.stringify({ error: 'Data and task are required' })
    };
  }
  
  try {
    const startTime = Date.now();
    
    const result = await engine.analyze({
      data,
      task,
      ...options
    });
    
    const duration = Date.now() - startTime;
    
    // Log metrics
    await logMetrics('analyze', { duration, dataSize: data.length });
    
    return {
      statusCode: 200,
      body: JSON.stringify({
        result,
        duration: duration,
        task: task,
        dataSize: data.length,
        timestamp: new Date().toISOString()
      })
    };
    
  } catch (error) {
    console.error('Analyze error:', error);
    return {
      statusCode: 500,
      body: JSON.stringify({ error: 'Analysis failed', message: error.message })
    };
  }
}

// Metrics endpoint
async function handleMetrics(engine, queryParams) {
  const { type = 'all', period = '24h' } = queryParams || {};
  
  try {
    // This would typically query DynamoDB for metrics
    // For now, return mock data
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
      }
    };
    
    return {
      statusCode: 200,
      body: JSON.stringify({
        metrics,
        period,
        timestamp: new Date().toISOString()
      })
    };
    
  } catch (error) {
    console.error('Metrics error:', error);
    return {
      statusCode: 500,
      body: JSON.stringify({ error: 'Metrics retrieval failed', message: error.message })
    };
  }
}

// Admin instances endpoint
async function handleAdminInstances(engine, method, body) {
  if (method === 'GET') {
    // Get all instances
    const instances = [
      {
        id: 'instance-1',
        name: 'Production Engine',
        status: 'running',
        model: '7B',
        lastUsed: new Date().toISOString(),
        requests: 1250
      },
      {
        id: 'instance-2',
        name: 'Development Engine',
        status: 'stopped',
        model: '7B',
        lastUsed: new Date(Date.now() - 3600000).toISOString(),
        requests: 89
      }
    ];
    
    return {
      statusCode: 200,
      body: JSON.stringify({ instances })
    };
  }
  
  if (method === 'POST') {
    // Create new instance
    const { name, model = '7B', config = {} } = body;
    
    const instance = {
      id: `instance-${Date.now()}`,
      name,
      status: 'initializing',
      model,
      config,
      createdAt: new Date().toISOString()
    };
    
    return {
      statusCode: 201,
      body: JSON.stringify({ instance })
    };
  }
  
  return {
    statusCode: 405,
    body: JSON.stringify({ error: 'Method not allowed' })
  };
}

// Admin status endpoint
async function handleAdminStatus(engine) {
  const status = {
    engine: {
      initialized: engine.isInitialized,
      modelSize: engine.config.modelSize,
      device: engine.config.device,
      uptime: process.uptime()
    },
    system: {
      memory: process.memoryUsage(),
      platform: process.platform,
      nodeVersion: process.version
    },
    timestamp: new Date().toISOString()
  };
  
  return {
    statusCode: 200,
    body: JSON.stringify(status)
  };
}

// Log metrics to DynamoDB (placeholder)
async function logMetrics(operation, data) {
  // This would typically write to DynamoDB
  console.log('📊 Metrics logged:', { operation, data });
}

module.exports = { handler: exports.handler };
