/**
 * Language Model - Complete language model implementation
 * 
 * Implements a full language model with embedding, transformer blocks,
 * and language modeling head for text generation
 */

const TransformerBlock = require('./transformer-block');
const MathUtils = require('../core/math');
const Tensor = require('../core/tensor');

class LanguageModel {
  constructor(config) {
    this.config = {
      vocabSize: config.vocabSize || 50000,
      dim: config.dim || 512,
      nLayers: config.nLayers || 12,
      nHeads: config.nHeads || 8,
      nKvHeads: config.nKvHeads || config.nHeads,
      headDim: config.dim / config.nHeads,
      hiddenDim: config.hiddenDim || config.dim * 4,
      maxSeqLen: config.maxSeqLen || 2048,
      dropout: config.dropout || 0.1,
      activation: config.activation || 'swish',
      ...config
    };
    
    this.vocabSize = this.config.vocabSize;
    this.dim = this.config.dim;
    this.nLayers = this.config.nLayers;
    this.maxSeqLen = this.config.maxSeqLen;
    
    // Initialize components
    this.initializeComponents();
  }

  /**
   * Initialize language model components
   */
  initializeComponents() {
    // Token embedding
    this.tokenEmbedding = this._createEmbedding(this.vocabSize, this.dim);
    
    // Position embedding
    this.positionEmbedding = this._createPositionEmbedding(this.maxSeqLen, this.dim);
    
    // Transformer blocks
    this.transformerBlocks = [];
    for (let i = 0; i < this.nLayers; i++) {
      this.transformerBlocks.push(new TransformerBlock({
        dim: this.dim,
        nHeads: this.config.nHeads,
        nKvHeads: this.config.nKvHeads,
        hiddenDim: this.config.hiddenDim,
        dropout: this.config.dropout,
        activation: this.config.activation
      }));
    }
    
    // Final layer normalization
    this.finalNorm = this._createRMSNorm();
    
    // Language modeling head
    this.lmHead = this._createLanguageModelHead();
    
    // Dropout
    this.dropout = this.config.dropout;
  }

  /**
   * Create token embedding
   * @param {number} vocabSize - Vocabulary size
   * @param {number} dim - Embedding dimension
   * @returns {Object} Embedding layer
   * @private
   */
  _createEmbedding(vocabSize, dim) {
    const weights = [];
    for (let i = 0; i < vocabSize; i++) {
      weights[i] = [];
      for (let j = 0; j < dim; j++) {
        weights[i][j] = MathUtils.randomNormal(0, 0.02);
      }
    }
    
    return {
      weights,
      vocabSize,
      dim,
      
      forward: (tokens) => this._embeddingForward(tokens, weights)
    };
  }

  /**
   * Token embedding forward pass
   * @param {Array} tokens - Token indices
   * @param {Array} weights - Embedding weights
   * @returns {Tensor} Embedded tokens
   * @private
   */
  _embeddingForward(tokens, weights) {
    const batchSize = tokens.length;
    const seqLen = tokens[0].length;
    const dim = weights[0].length;
    
    const embedded = [];
    for (let b = 0; b < batchSize; b++) {
      embedded[b] = [];
      for (let s = 0; s < seqLen; s++) {
        embedded[b][s] = [...weights[tokens[b][s]]];
      }
    }
    
    return new Tensor(embedded, [batchSize, seqLen, dim]);
  }

  /**
   * Create position embedding
   * @param {number} maxSeqLen - Maximum sequence length
   * @param {number} dim - Embedding dimension
   * @returns {Object} Position embedding
   * @private
   */
  _createPositionEmbedding(maxSeqLen, dim) {
    const weights = [];
    for (let pos = 0; pos < maxSeqLen; pos++) {
      weights[pos] = [];
      for (let d = 0; d < dim; d++) {
        if (d % 2 === 0) {
          weights[pos][d] = Math.sin(pos / Math.pow(10000, d / dim));
        } else {
          weights[pos][d] = Math.cos(pos / Math.pow(10000, (d - 1) / dim));
        }
      }
    }
    
    return {
      weights,
      maxSeqLen,
      dim,
      
      forward: (seqLen) => this._positionEmbeddingForward(seqLen, weights)
    };
  }

  /**
   * Position embedding forward pass
   * @param {number} seqLen - Sequence length
   * @param {Array} weights - Position weights
   * @returns {Tensor} Position embeddings
   * @private
   */
  _positionEmbeddingForward(seqLen, weights) {
    const dim = weights[0].length;
    const embedded = [];
    
    for (let s = 0; s < seqLen; s++) {
      embedded[s] = [...weights[s]];
    }
    
    return new Tensor(embedded, [seqLen, dim]);
  }

  /**
   * Create RMSNorm layer
   * @returns {Object} RMSNorm layer
   * @private
   */
  _createRMSNorm() {
    return {
      weight: new Array(this.dim).fill(1.0),
      epsilon: 1e-6,
      
      forward: (x) => this._rmsNormForward(x, this.weight, this.epsilon)
    };
  }

  /**
   * RMSNorm forward pass
   * @param {Tensor} x - Input tensor
   * @param {Array} weight - Weight vector
   * @param {number} epsilon - Small constant
   * @returns {Tensor} Normalized tensor
   * @private
   */
  _rmsNormForward(x, weight, epsilon) {
    const batchSize = x.shape[0];
    const seqLen = x.shape[1];
    const dim = x.shape[2];
    
    const normalized = [];
    for (let b = 0; b < batchSize; b++) {
      normalized[b] = [];
      for (let s = 0; s < seqLen; s++) {
        normalized[b][s] = [];
        
        // Calculate RMS
        let sumSquares = 0;
        for (let d = 0; d < dim; d++) {
          sumSquares += x.data[b][s][d] * x.data[b][s][d];
        }
        const rms = Math.sqrt(sumSquares / dim + epsilon);
        
        // Normalize and scale
        for (let d = 0; d < dim; d++) {
          normalized[b][s][d] = (x.data[b][s][d] / rms) * weight[d];
        }
      }
    }
    
    return new Tensor(normalized, [batchSize, seqLen, dim]);
  }

  /**
   * Create language modeling head
   * @returns {Object} Language modeling head
   * @private
   */
  _createLanguageModelHead() {
    const weights = [];
    for (let i = 0; i < this.vocabSize; i++) {
      weights[i] = [];
      for (let j = 0; j < this.dim; j++) {
        weights[i][j] = MathUtils.randomNormal(0, 0.02);
      }
    }
    
    return {
      weights,
      vocabSize: this.vocabSize,
      dim: this.dim,
      
      forward: (x) => this._lmHeadForward(x, weights)
    };
  }

  /**
   * Language modeling head forward pass
   * @param {Tensor} x - Input tensor
   * @param {Array} weights - Head weights
   * @returns {Tensor} Logits
   * @private
   */
  _lmHeadForward(x, weights) {
    const batchSize = x.shape[0];
    const seqLen = x.shape[1];
    const dim = x.shape[2];
    
    const logits = [];
    for (let b = 0; b < batchSize; b++) {
      logits[b] = [];
      for (let s = 0; s < seqLen; s++) {
        logits[b][s] = [];
        for (let v = 0; v < this.vocabSize; v++) {
          let sum = 0;
          for (let d = 0; d < dim; d++) {
            sum += x.data[b][s][d] * weights[v][d];
          }
          logits[b][s][v] = sum;
        }
      }
    }
    
    return new Tensor(logits, [batchSize, seqLen, this.vocabSize]);
  }

  /**
   * Forward pass
   * @param {Array} tokens - Input tokens
   * @param {Object} options - Additional options
   * @returns {Object} Model output
   */
  forward(tokens, options = {}) {
    const { mask = null, training = true, startPos = 0 } = options;
    
    const batchSize = tokens.length;
    const seqLen = tokens[0].length;
    
    // Token embedding
    const tokenEmb = this.tokenEmbedding.forward(tokens);
    
    // Position embedding
    const posEmb = this.positionEmbedding.forward(seqLen);
    
    // Add embeddings
    const x = this._addEmbeddings(tokenEmb, posEmb);
    
    // Apply dropout
    const dropped = this._applyDropout(x, this.dropout, training);
    
    // Transformer blocks
    let hidden = dropped;
    const attentionWeights = [];
    
    for (let i = 0; i < this.nLayers; i++) {
      const block = this.transformerBlocks[i];
      hidden = block.forward(hidden, {
        mask,
        training,
        startPos
      });
      
      // Store attention weights for visualization
      if (options.returnAttention) {
        attentionWeights.push(block.getAttentionWeights(hidden, { mask, startPos }));
      }
    }
    
    // Final normalization
    const normalized = this.finalNorm.forward(hidden);
    
    // Language modeling head
    const logits = this.lmHead.forward(normalized);
    
    // Apply softmax for probabilities
    const probs = this._applySoftmax(logits);
    
    return {
      logits,
      probs,
      hidden,
      attentionWeights: options.returnAttention ? attentionWeights : null
    };
  }

  /**
   * Add token and position embeddings
   * @param {Tensor} tokenEmb - Token embeddings
   * @param {Tensor} posEmb - Position embeddings
   * @returns {Tensor} Combined embeddings
   * @private
   */
  _addEmbeddings(tokenEmb, posEmb) {
    const batchSize = tokenEmb.shape[0];
    const seqLen = tokenEmb.shape[1];
    const dim = tokenEmb.shape[2];
    
    const combined = [];
    for (let b = 0; b < batchSize; b++) {
      combined[b] = [];
      for (let s = 0; s < seqLen; s++) {
        combined[b][s] = [];
        for (let d = 0; d < dim; d++) {
          combined[b][s][d] = tokenEmb.data[b][s][d] + posEmb.data[s][d];
        }
      }
    }
    
    return new Tensor(combined, [batchSize, seqLen, dim]);
  }

  /**
   * Apply dropout
   * @param {Tensor} x - Input tensor
   * @param {number} rate - Dropout rate
   * @param {boolean} training - Training mode
   * @returns {Tensor} Output tensor
   * @private
   */
  _applyDropout(x, rate, training) {
    if (!training || rate === 0) {
      return x;
    }
    
    const batchSize = x.shape[0];
    const seqLen = x.shape[1];
    const dim = x.shape[2];
    
    const output = [];
    for (let b = 0; b < batchSize; b++) {
      output[b] = [];
      for (let s = 0; s < seqLen; s++) {
        output[b][s] = [];
        for (let d = 0; d < dim; d++) {
          const keep = Math.random() > rate;
          output[b][s][d] = keep ? x.data[b][s][d] : 0;
        }
      }
    }
    
    return new Tensor(output, [batchSize, seqLen, dim]);
  }

  /**
   * Apply softmax to logits
   * @param {Tensor} logits - Input logits
   * @returns {Tensor} Probabilities
   * @private
   */
  _applySoftmax(logits) {
    const batchSize = logits.shape[0];
    const seqLen = logits.shape[1];
    const vocabSize = logits.shape[2];
    
    const probs = [];
    for (let b = 0; b < batchSize; b++) {
      probs[b] = [];
      for (let s = 0; s < seqLen; s++) {
        const row = logits.data[b][s];
        const softmaxRow = MathUtils.softmax(row);
        probs[b][s] = softmaxRow;
      }
    }
    
    return new Tensor(probs, [batchSize, seqLen, vocabSize]);
  }

  /**
   * Generate text
   * @param {Array} prompt - Input prompt tokens
   * @param {Object} options - Generation options
   * @returns {Array} Generated tokens
   */
  generate(prompt, options = {}) {
    const {
      maxLength = 100,
      temperature = 0.7,
      topK = 50,
      topP = 0.9,
      stopTokens = []
    } = options;
    
    const generated = [...prompt];
    const batchSize = 1;
    
    for (let i = 0; i < maxLength; i++) {
      // Prepare input
      const input = [generated.slice(-this.maxSeqLen)];
      
      // Forward pass
      const output = this.forward(input, { training: false });
      const logits = output.logits.data[0][-1]; // Last token logits
      
      // Apply temperature
      const scaledLogits = logits.map(logit => logit / temperature);
      
      // Apply top-k filtering
      const topKLogits = this._applyTopK(scaledLogits, topK);
      
      // Apply top-p filtering
      const topPLogits = this._applyTopP(topKLogits, topP);
      
      // Sample next token
      const nextToken = this._sampleToken(topPLogits);
      
      // Check for stop tokens
      if (stopTokens.includes(nextToken)) {
        break;
      }
      
      generated.push(nextToken);
    }
    
    return generated;
  }

  /**
   * Apply top-k filtering
   * @param {Array} logits - Input logits
   * @param {number} k - Top-k value
   * @returns {Array} Filtered logits
   * @private
   */
  _applyTopK(logits, k) {
    const sorted = logits
      .map((logit, index) => ({ logit, index }))
      .sort((a, b) => b.logit - a.logit)
      .slice(0, k);
    
    const filtered = new Array(logits.length).fill(-Infinity);
    sorted.forEach(({ logit, index }) => {
      filtered[index] = logit;
    });
    
    return filtered;
  }

  /**
   * Apply top-p filtering
   * @param {Array} logits - Input logits
   * @param {number} p - Top-p value
   * @returns {Array} Filtered logits
   * @private
   */
  _applyTopP(logits, p) {
    const sorted = logits
      .map((logit, index) => ({ logit, index }))
      .sort((a, b) => b.logit - a.logit);
    
    const probs = MathUtils.softmax(logits);
    let cumulativeProb = 0;
    const cutoff = sorted.findIndex(({ index }) => {
      cumulativeProb += probs[index];
      return cumulativeProb >= p;
    });
    
    const filtered = new Array(logits.length).fill(-Infinity);
    for (let i = 0; i <= cutoff; i++) {
      filtered[sorted[i].index] = sorted[i].logit;
    }
    
    return filtered;
  }

  /**
   * Sample token from logits
   * @param {Array} logits - Input logits
   * @returns {number} Sampled token
   * @private
   */
  _sampleToken(logits) {
    const probs = MathUtils.softmax(logits);
    const random = Math.random();
    let cumulativeProb = 0;
    
    for (let i = 0; i < probs.length; i++) {
      cumulativeProb += probs[i];
      if (random <= cumulativeProb) {
        return i;
      }
    }
    
    return probs.length - 1;
  }

  /**
   * Get model parameters count
   * @returns {Object} Parameter counts
   */
  getParameterCount() {
    const embeddingParams = this.vocabSize * this.dim;
    const positionParams = this.maxSeqLen * this.dim;
    const lmHeadParams = this.vocabSize * this.dim;
    
    // Transformer blocks parameters (approximate)
    const blockParams = this.nLayers * (
      this.dim * this.dim * 4 + // attention
      this.dim * this.config.hiddenDim * 2 + // ffn
      this.dim * 2 // norms
    );
    
    const total = embeddingParams + positionParams + lmHeadParams + blockParams;
    
    return {
      embedding: embeddingParams,
      position: positionParams,
      transformer: blockParams,
      lmHead: lmHeadParams,
      total
    };
  }

  /**
   * Get model configuration
   * @returns {Object} Model configuration
   */
  getConfig() {
    return {
      ...this.config,
      parameterCount: this.getParameterCount()
    };
  }
}

module.exports = LanguageModel;
