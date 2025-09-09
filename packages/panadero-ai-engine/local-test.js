/**
 * Local Testing Script for Panadero AI Engine
 * This script tests all components locally before AWS deployment
 */

const { AIEngine } = require('./src/index.js');
const express = require('express');
const path = require('path');

// Test configuration
const TEST_CONFIG = {
    modelSize: '7B',
    quantization: 'int8',
    device: 'cpu',
    maxSequenceLength: 2048
};

console.log('🧪 Panadero AI Engine - Local Testing');
console.log('=====================================');

async function testAIEngine() {
    console.log('\n1. Testing AI Engine Initialization...');
    
    try {
        const engine = new AIEngine(TEST_CONFIG);
        await engine.initialize();
        console.log('✅ AI Engine initialized successfully');
        
        // Test basic functionality
        console.log('\n2. Testing Text Generation...');
        const result = await engine.generate({
            prompt: "The future of AI is",
            maxTokens: 50,
            temperature: 0.7
        });
        console.log('✅ Text generation working:', result.text.substring(0, 100) + '...');
        
        // Test model info
        console.log('\n3. Testing Model Info...');
        const modelInfo = engine.getModelInfo();
        console.log('✅ Model info:', modelInfo);
        
        return engine;
        
    } catch (error) {
        console.error('❌ AI Engine test failed:', error.message);
        throw error;
    }
}

function testAdminInterface() {
    console.log('\n4. Testing Admin Interface...');
    
    try {
        const app = express();
        const PORT = 3001;
        
        // Serve admin interface
        app.use(express.static(path.join(__dirname, 'admin')));
        
        // Test admin server
        const server = app.listen(PORT, () => {
            console.log(`✅ Admin interface running on http://localhost:${PORT}`);
            console.log(`   - Dashboard: http://localhost:${PORT}`);
            console.log(`   - API endpoints: http://localhost:${PORT}/api/*`);
        });
        
        return server;
        
    } catch (error) {
        console.error('❌ Admin interface test failed:', error.message);
        throw error;
    }
}

function testPublicInterface() {
    console.log('\n5. Testing Public Metrics Interface...');
    
    try {
        const app = express();
        const PORT = 3000;
        
        // Serve public interface
        app.use(express.static(path.join(__dirname, 'public')));
        
        // Test public server
        const server = app.listen(PORT, () => {
            console.log(`✅ Public interface running on http://localhost:${PORT}`);
            console.log(`   - Metrics page: http://localhost:${PORT}`);
            console.log(`   - Health check: http://localhost:${PORT}/health`);
        });
        
        return server;
        
    } catch (error) {
        console.error('❌ Public interface test failed:', error.message);
        throw error;
    }
}

async function runAllTests() {
    console.log('🚀 Starting local tests...\n');
    
    try {
        // Test AI Engine
        const engine = await testAIEngine();
        
        // Test Admin Interface
        const adminServer = testAdminInterface();
        
        // Test Public Interface
        const publicServer = testPublicInterface();
        
        console.log('\n🎉 All tests completed successfully!');
        console.log('\n📊 Local Testing Summary:');
        console.log('========================');
        console.log('✅ AI Engine: Working');
        console.log('✅ Admin Interface: http://localhost:3001');
        console.log('✅ Public Interface: http://localhost:3000');
        console.log('✅ All components: Functional');
        
        console.log('\n🌐 Test URLs:');
        console.log('- Public Metrics: http://localhost:3000');
        console.log('- Admin Dashboard: http://localhost:3001');
        console.log('- Health Check: http://localhost:3000/health');
        
        console.log('\n⏹️  Press Ctrl+C to stop all servers');
        
        // Keep servers running
        process.on('SIGINT', () => {
            console.log('\n🛑 Stopping servers...');
            adminServer.close();
            publicServer.close();
            process.exit(0);
        });
        
    } catch (error) {
        console.error('\n❌ Testing failed:', error.message);
        process.exit(1);
    }
}

// Run tests if this file is executed directly
if (require.main === module) {
    runAllTests();
}

module.exports = { testAIEngine, testAdminInterface, testPublicInterface };
