/**
 * Advanced Usage Example - Panadero AI Engine
 * 
 * This example shows advanced features like training, attention mechanisms,
 * and transformer blocks
 */

const { 
  AIEngine, 
  Tensor, 
  MathUtils, 
  MultiHeadAttention,
  GroupQueryAttention,
  TransformerBlock,
  LanguageModel,
  Optimizer,
  LossFunctions,
  DataLoader
} = require('../src/index.js');

async function advancedExample() {
  console.log('🚀 Starting Panadero AI Engine Advanced Example...\n');
  
  try {
    // 1. Advanced Tensor Operations
    console.log('1. Advanced Tensor Operations...');
    const tensor = new Tensor([[1, 2, 3, 4], [5, 6, 7, 8]]);
    console.log('Original tensor:', tensor.toString());
    
    const transposed = tensor.transpose();
    console.log('Transposed:', transposed.toString());
    
    const reshaped = tensor.reshape([4, 2]);
    console.log('Reshaped to [4, 2]:', reshaped.toString());
    console.log('✅ Advanced tensor operations working\n');
    
    // 2. Attention Mechanisms
    console.log('2. Testing Attention Mechanisms...');
    const attention = new MultiHeadAttention({
      dim: 64,
      nHeads: 8,
      headDim: 8
    });
    
    const input = new Tensor([[[1, 2, 3, 4, 5, 6, 7, 8]]]);
    console.log('Input shape:', input.shape);
    
    const attentionOutput = attention.forward(input);
    console.log('Attention output shape:', attentionOutput.shape);
    console.log('✅ Attention mechanisms working\n');
    
    // 3. Group Query Attention
    console.log('3. Testing Group Query Attention...');
    const gqa = new GroupQueryAttention({
      dim: 64,
      nHeads: 8,
      nKvHeads: 4,
      headDim: 8
    });
    
    const gqaOutput = gqa.forward(input);
    console.log('GQA output shape:', gqaOutput.shape);
    
    const memoryStats = gqa.getMemoryStats();
    console.log('GQA memory efficiency:', memoryStats.memoryEfficiency);
    console.log('✅ Group Query Attention working\n');
    
    // 4. Transformer Block
    console.log('4. Testing Transformer Block...');
    const transformer = new TransformerBlock({
      dim: 64,
      nHeads: 8,
      nKvHeads: 4,
      headDim: 8,
      hiddenDim: 256
    });
    
    const transformerOutput = transformer.forward(input);
    console.log('Transformer output shape:', transformerOutput.shape);
    console.log('✅ Transformer block working\n');
    
    // 5. Language Model
    console.log('5. Testing Language Model...');
    const languageModel = new LanguageModel({
      vocabSize: 1000,
      dim: 64,
      nLayers: 2,
      nHeads: 8,
      nKvHeads: 4,
      maxSeqLen: 128
    });
    
    const tokens = [[1, 2, 3, 4, 5]];
    const lmOutput = languageModel.forward(tokens);
    console.log('Language model output shape:', lmOutput.logits.shape);
    console.log('✅ Language model working\n');
    
    // 6. Training Components
    console.log('6. Testing Training Components...');
    const optimizer = new Optimizer({
      algorithm: 'adamw',
      learningRate: 0.001,
      weightDecay: 0.01
    });
    
    const parameters = {
      weight1: [1, 2, 3, 4],
      weight2: [5, 6, 7, 8]
    };
    
    const gradients = {
      weight1: [0.1, 0.2, 0.3, 0.4],
      weight2: [0.5, 0.6, 0.7, 0.8]
    };
    
    const updatedParams = optimizer.update(parameters, gradients);
    console.log('Updated parameters:', Object.keys(updatedParams));
    console.log('✅ Optimizer working\n');
    
    // 7. Loss Functions
    console.log('7. Testing Loss Functions...');
    const predictions = [0.1, 0.2, 0.7];
    const targets = [0, 0, 1];
    
    const ceLoss = LossFunctions.crossEntropyLoss(predictions, targets);
    const mseLoss = LossFunctions.mseLoss(predictions, targets);
    
    console.log('Cross-entropy loss:', ceLoss);
    console.log('MSE loss:', mseLoss);
    console.log('✅ Loss functions working\n');
    
    // 8. Data Loader
    console.log('8. Testing Data Loader...');
    const data = [
      [1, 2, 3, 4],
      [5, 6, 7, 8],
      [9, 10, 11, 12],
      [13, 14, 15, 16]
    ];
    const labels = [0, 1, 0, 1];
    
    const dataLoader = new DataLoader({
      batchSize: 2,
      shuffle: true
    });
    
    dataLoader.loadData(data, labels);
    console.log('Data loader stats:', dataLoader.getStats());
    
    const batch = dataLoader.nextBatch();
    console.log('First batch size:', batch.data.length);
    console.log('✅ Data loader working\n');
    
    // 9. AI Engine with Advanced Features
    console.log('9. Testing AI Engine with Advanced Features...');
    const advancedEngine = new AIEngine({
      modelSize: '7B',
      quantization: 'int8',
      device: 'cpu',
      maxSequenceLength: 512
    });
    
    await advancedEngine.initialize();
    
    const advancedGeneration = await advancedEngine.generate({
      prompt: "The future of artificial intelligence",
      maxTokens: 100,
      temperature: 0.8,
      topK: 50,
      topP: 0.9
    });
    
    console.log('Advanced generation result:');
    console.log('Text:', advancedGeneration.text);
    console.log('Tokens generated:', advancedGeneration.outputLength);
    console.log('✅ Advanced AI Engine working\n');
    
    console.log('🎉 All advanced tests completed successfully!');
    console.log('\n📊 Summary:');
    console.log('- Tensor operations: ✅');
    console.log('- Attention mechanisms: ✅');
    console.log('- Transformer blocks: ✅');
    console.log('- Language model: ✅');
    console.log('- Training components: ✅');
    console.log('- Loss functions: ✅');
    console.log('- Data loading: ✅');
    console.log('- AI Engine: ✅');
    
  } catch (error) {
    console.error('❌ Error running advanced example:', error.message);
    console.error(error.stack);
  }
}

// Run the example
if (require.main === module) {
  advancedExample();
}

module.exports = { advancedExample };