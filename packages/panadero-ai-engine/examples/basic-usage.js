/**
 * Basic Usage Example - Panadero AI Engine
 * 
 * This example shows how to use the AI engine for basic operations
 */

const { AIEngine, Tensor, MathUtils } = require('../src/index.js');

async function basicExample() {
  console.log('🚀 Starting Panadero AI Engine Basic Example...\n');
  
  try {
    // 1. Initialize the AI Engine
    console.log('1. Initializing AI Engine...');
    const engine = new AIEngine({
      modelSize: '7B',
      quantization: 'int8',
      device: 'cpu'
    });
    
    await engine.initialize();
    console.log('✅ AI Engine initialized successfully\n');
    
    // 2. Basic Tensor Operations
    console.log('2. Testing Tensor Operations...');
    const tensor1 = new Tensor([[1, 2, 3], [4, 5, 6]]);
    const tensor2 = new Tensor([[7, 8], [9, 10], [11, 12]]);
    
    console.log('Tensor 1:', tensor1.toString());
    console.log('Tensor 2:', tensor2.toString());
    
    const result = tensor1.matmul(tensor2);
    console.log('Matrix multiplication result:', result.toString());
    console.log('✅ Tensor operations working\n');
    
    // 3. Math Utilities
    console.log('3. Testing Math Utilities...');
    const testArray = [1, 2, 3, 4, 5];
    console.log('Input array:', testArray);
    console.log('Mean:', MathUtils.mean(testArray));
    console.log('Standard deviation:', MathUtils.std(testArray));
    console.log('Softmax:', MathUtils.softmax(testArray));
    console.log('✅ Math utilities working\n');
    
    // 4. Text Generation (simplified)
    console.log('4. Testing Text Generation...');
    const prompt = "The future of AI is";
    console.log('Prompt:', prompt);
    
    const generation = await engine.generate({
      prompt: prompt,
      maxTokens: 50,
      temperature: 0.7
    });
    
    console.log('Generated text:', generation.text);
    console.log('✅ Text generation working\n');
    
    // 5. Model Information
    console.log('5. Model Information:');
    const modelInfo = engine.getModelInfo();
    console.log(JSON.stringify(modelInfo, null, 2));
    console.log('✅ Model info retrieved\n');
    
    console.log('🎉 All tests completed successfully!');
    
  } catch (error) {
    console.error('❌ Error running example:', error.message);
    console.error(error.stack);
  }
}

// Run the example
if (require.main === module) {
  basicExample();
}

module.exports = { basicExample };