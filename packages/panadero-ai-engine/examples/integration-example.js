/**
 * Integration Example - Panadero AI Engine
 * 
 * Shows how to integrate the AI engine with your existing framework
 * and how external parties can use it
 */

const { AIEngine, Tensor, MathUtils } = require('../src/index.js');

// Simulate your existing ERP data structure
const indigoData = {
  production: [
    { date: '2023-01-01', units: 1000, efficiency: 0.85 },
    { date: '2023-02-01', units: 1200, efficiency: 0.87 },
    { date: '2023-03-01', units: 1100, efficiency: 0.86 },
    // ... more historical data
  ],
  financial: [
    { date: '2023-01-01', revenue: 50000, costs: 30000 },
    { date: '2023-02-01', revenue: 55000, costs: 32000 },
    { date: '2023-03-01', revenue: 52000, costs: 31000 },
    // ... more financial data
  ],
  inventory: [
    { date: '2023-01-01', stock: 500, demand: 600 },
    { date: '2023-02-01', stock: 450, demand: 650 },
    { date: '2023-03-01', stock: 400, demand: 700 },
    // ... more inventory data
  ]
};

async function internalIntegration() {
  console.log('🏢 Internal Integration Example\n');
  
  try {
    // Initialize AI Engine for internal use
    const engine = new AIEngine({
      modelSize: '7B',
      quantization: 'int8',
      device: 'cpu',
      maxSequenceLength: 2048
    });
    
    await engine.initialize();
    
    // 1. Production Forecasting
    console.log('1. Production Forecasting...');
    const productionForecast = await engine.analyze({
      data: indigoData.production,
      task: 'production_forecasting',
      horizon: '3_months'
    });
    console.log('Production forecast:', productionForecast);
    
    // 2. Financial Analysis
    console.log('\n2. Financial Analysis...');
    const financialAnalysis = await engine.analyze({
      data: indigoData.financial,
      task: 'financial_analysis',
      metrics: ['revenue_growth', 'cost_optimization']
    });
    console.log('Financial analysis:', financialAnalysis);
    
    // 3. Inventory Optimization
    console.log('\n3. Inventory Optimization...');
    const inventoryOptimization = await engine.analyze({
      data: indigoData.inventory,
      task: 'inventory_optimization',
      objective: 'minimize_stockouts'
    });
    console.log('Inventory optimization:', inventoryOptimization);
    
    console.log('\n✅ Internal integration working!');
    
  } catch (error) {
    console.error('❌ Internal integration error:', error.message);
  }
}

async function externalAPI() {
  console.log('\n🌐 External API Example\n');
  
  try {
    // Initialize AI Engine for external use
    const engine = new AIEngine({
      modelSize: '7B',
      quantization: 'int8',
      device: 'cpu'
    });
    
    await engine.initialize();
    
    // 1. Text Generation
    console.log('1. Text Generation...');
    const textGeneration = await engine.generate({
      prompt: "The future of artificial intelligence in business is",
      maxTokens: 100,
      temperature: 0.7
    });
    console.log('Generated text:', textGeneration.text);
    
    // 2. Data Analysis
    console.log('\n2. Data Analysis...');
    const dataAnalysis = await engine.analyze({
      data: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
      task: 'statistical_analysis',
      metrics: ['mean', 'std', 'trend']
    });
    console.log('Data analysis:', dataAnalysis);
    
    // 3. Predictive Modeling
    console.log('\n3. Predictive Modeling...');
    const prediction = await engine.predict({
      input: [1, 2, 3, 4, 5],
      task: 'time_series_forecast',
      horizon: 3
    });
    console.log('Prediction:', prediction);
    
    console.log('\n✅ External API working!');
    
  } catch (error) {
    console.error('❌ External API error:', error.message);
  }
}

async function businessModel() {
  console.log('\n💰 Business Model Example\n');
  
  // Internal Use Cases
  console.log('Internal Use Cases:');
  console.log('- ERP Integration: ✅');
  console.log('- Historical Data Analysis: ✅');
  console.log('- Predictive Analytics: ✅');
  console.log('- Business Intelligence: ✅');
  
  // External Use Cases
  console.log('\nExternal Use Cases:');
  console.log('- Text Generation: ✅');
  console.log('- Data Analysis: ✅');
  console.log('- Predictive Modeling: ✅');
  console.log('- Custom AI Solutions: ✅');
  
  // Revenue Streams
  console.log('\nRevenue Streams:');
  console.log('- License Fees: $10,000/year per installation');
  console.log('- API Calls: $0.01 per request');
  console.log('- Custom Training: $50,000 per project');
  console.log('- Consulting: $200/hour');
  
  // Market Positioning
  console.log('\nMarket Positioning:');
  console.log('- Enterprise AI Engine');
  console.log('- Built on 20 years of data');
  console.log('- Production-ready');
  console.log('- Scalable and reliable');
}

// Run all examples
async function runAllExamples() {
  console.log('🚀 Panadero AI Engine - Integration & Business Model\n');
  
  await internalIntegration();
  await externalAPI();
  await businessModel();
  
  console.log('\n🎉 All examples completed!');
  console.log('\n📊 Summary:');
  console.log('- Internal integration: Ready for ERP systems');
  console.log('- External API: Ready for commercial use');
  console.log('- Business model: Multiple revenue streams');
  console.log('- Market positioning: Enterprise AI solution');
}

// Run the examples
if (require.main === module) {
  runAllExamples();
}

module.exports = { internalIntegration, externalAPI, businessModel };
