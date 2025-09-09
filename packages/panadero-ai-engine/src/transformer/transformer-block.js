/**
 * Transformer Block - Complete transformer block implementation
 * 
 * Implements a full transformer block with self-attention, feed-forward,
 * layer normalization, and residual connections
 */

const GroupQueryAttention = require('../attention/group-query-attention');
const RoPEEncoding = require('../attention/rope-encoding');
const MathUtils = require('../core/math');
const Tensor = require('../core/tensor');

class TransformerBlock {
  constructor(config) {
    this.config = {
      dim: config.dim,
      nHeads: config.nHeads,
      nKvHeads: config.nKvHeads || config.nHeads,
      headDim: config.dim / config.nHeads,
      hiddenDim: config.hiddenDim || config.dim * 4,
      dropout: config.dropout || 0.1,
      activation: config.activation || 'swish',
      ...config
    };
    
    this.dim = this.config.dim;
    this.hiddenDim = this.config.hiddenDim;
    
    // Initialize components
    this.initializeComponents();
  }

  /**
   * Initialize transformer block components
   */
  initializeComponents() {
    // Self-attention layer
    this.attention = new GroupQueryAttention({
      dim: this.dim,
      nHeads: this.config.nHeads,
      nKvHeads: this.config.nKvHeads,
      dropout: this.config.dropout
    });
    
    // RoPE positional encoding
    this.rope = new RoPEEncoding({
      dim: this.dim,
      maxSeqLen: 2048
    });
    
    // Layer normalization (RMSNorm)
    this.norm1 = this._createRMSNorm();
    this.norm2 = this._createRMSNorm();
    
    // Feed-forward network
    this.ffn = this._createFeedForward();
    
    // Dropout layers
    this.dropout1 = this.config.dropout;
    this.dropout2 = this.config.dropout;
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
   * Create feed-forward network
   * @returns {Object} Feed-forward network
   * @private
   */
  _createFeedForward() {
    const { dim, hiddenDim, activation } = this.config;
    
    // Weight matrices
    const w1 = this._createWeightMatrix(dim, hiddenDim);
    const w2 = this._createWeightMatrix(hiddenDim, dim);
    
    // Biases
    const b1 = new Array(hiddenDim).fill(0);
    const b2 = new Array(dim).fill(0);
    
    return {
      w1, w2, b1, b2, activation,
      
      forward: (x) => this._ffnForward(x, w1, w2, b1, b2, activation)
    };
  }

  /**
   * Feed-forward forward pass
   * @param {Tensor} x - Input tensor
   * @param {Array} w1 - First weight matrix
   * @param {Array} w2 - Second weight matrix
   * @param {Array} b1 - First bias
   * @param {Array} b2 - Second bias
   * @param {string} activation - Activation function
   * @returns {Tensor} Output tensor
   * @private
   */
  _ffnForward(x, w1, w2, b1, b2, activation) {
    const batchSize = x.shape[0];
    const seqLen = x.shape[1];
    const dim = x.shape[2];
    const hiddenDim = w1.length;
    
    // First linear layer
    const hidden = [];
    for (let b = 0; b < batchSize; b++) {
      hidden[b] = [];
      for (let s = 0; s < seqLen; s++) {
        hidden[b][s] = [];
        for (let h = 0; h < hiddenDim; h++) {
          let sum = b1[h];
          for (let d = 0; d < dim; d++) {
            sum += x.data[b][s][d] * w1[h][d];
          }
          hidden[b][s][h] = sum;
        }
      }
    }
    
    // Apply activation
    const activated = this._applyActivation(hidden, activation);
    
    // Second linear layer
    const output = [];
    for (let b = 0; b < batchSize; b++) {
      output[b] = [];
      for (let s = 0; s < seqLen; s++) {
        output[b][s] = [];
        for (let d = 0; d < dim; d++) {
          let sum = b2[d];
          for (let h = 0; h < hiddenDim; h++) {
            sum += activated[b][s][h] * w2[d][h];
          }
          output[b][s][d] = sum;
        }
      }
    }
    
    return new Tensor(output, [batchSize, seqLen, dim]);
  }

  /**
   * Apply activation function
   * @param {Array} data - Input data
   * @param {string} activation - Activation function name
   * @returns {Array} Activated data
   * @private
   */
  _applyActivation(data, activation) {
    const batchSize = data.length;
    const seqLen = data[0].length;
    const hiddenDim = data[0][0].length;
    
    const activated = [];
    for (let b = 0; b < batchSize; b++) {
      activated[b] = [];
      for (let s = 0; s < seqLen; s++) {
        activated[b][s] = [];
        for (let h = 0; h < hiddenDim; h++) {
          const value = data[b][s][h];
          let result;
          
          switch (activation) {
            case 'relu':
              result = MathUtils.relu(value);
              break;
            case 'gelu':
              result = MathUtils.gelu(value);
              break;
            case 'swish':
              result = MathUtils.swish(value);
              break;
            case 'silu':
              result = value * MathUtils.sigmoid(value);
              break;
            default:
              result = value;
          }
          
          activated[b][s][h] = result;
        }
      }
    }
    
    return activated;
  }

  /**
   * Create weight matrix
   * @param {number} inSize - Input size
   * @param {number} outSize - Output size
   * @returns {Array} Weight matrix
   * @private
   */
  _createWeightMatrix(inSize, outSize) {
    const matrix = [];
    const limit = Math.sqrt(6 / (inSize + outSize));
    
    for (let i = 0; i < outSize; i++) {
      matrix[i] = [];
      for (let j = 0; j < inSize; j++) {
        matrix[i][j] = MathUtils.randomUniform(-limit, limit);
      }
    }
    
    return matrix;
  }

  /**
   * Forward pass
   * @param {Tensor} x - Input tensor
   * @param {Object} options - Additional options
   * @returns {Tensor} Output tensor
   */
  forward(x, options = {}) {
    const { mask = null, training = true, startPos = 0 } = options;
    
    // Self-attention with RoPE
    const normX = this.norm1.forward(x);
    const { q, k } = this.rope.applyRoPE(normX, normX, startPos);
    
    // Attention
    const attentionOut = this.attention.forward(normX, {
      mask,
      training
    });
    
    // Residual connection + dropout
    const residual1 = this._addResidual(x, attentionOut);
    const dropped1 = this._applyDropout(residual1, this.dropout1, training);
    
    // Feed-forward network
    const norm2X = this.norm2.forward(dropped1);
    const ffnOut = this.ffn.forward(norm2X);
    
    // Residual connection + dropout
    const residual2 = this._addResidual(dropped1, ffnOut);
    const output = this._applyDropout(residual2, this.dropout2, training);
    
    return output;
  }

  /**
   * Add residual connection
   * @param {Tensor} x - Input tensor
   * @param {Tensor} residual - Residual tensor
   * @returns {Tensor} Sum tensor
   * @private
   */
  _addResidual(x, residual) {
    const batchSize = x.shape[0];
    const seqLen = x.shape[1];
    const dim = x.shape[2];
    
    const sum = [];
    for (let b = 0; b < batchSize; b++) {
      sum[b] = [];
      for (let s = 0; s < seqLen; s++) {
        sum[b][s] = [];
        for (let d = 0; d < dim; d++) {
          sum[b][s][d] = x.data[b][s][d] + residual.data[b][s][d];
        }
      }
    }
    
    return new Tensor(sum, [batchSize, seqLen, dim]);
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
   * Get attention weights
   * @param {Tensor} x - Input tensor
   * @param {Object} options - Options
   * @returns {Array} Attention weights
   */
  getAttentionWeights(x, options = {}) {
    const { mask = null, startPos = 0 } = options;
    
    const normX = this.norm1.forward(x);
    const { q, k } = this.rope.applyRoPE(normX, normX, startPos);
    
    return this.attention.getAttentionWeights(normX, { mask });
  }

  /**
   * Get model parameters
   * @returns {Object} Model parameters
   */
  getParameters() {
    return {
      attention: this.attention,
      norm1: this.norm1,
      norm2: this.norm2,
      ffn: this.ffn,
      rope: this.rope
    };
  }

  /**
   * Get memory usage
   * @returns {Object} Memory usage statistics
   */
  getMemoryUsage() {
    const attentionMemory = this.attention.getMemoryStats();
    const ropeMemory = this.rope.getMemoryUsage();
    
    return {
      attention: attentionMemory,
      rope: ropeMemory,
      total: {
        dim: this.dim,
        hiddenDim: this.hiddenDim,
        nHeads: this.config.nHeads,
        nKvHeads: this.config.nKvHeads
      }
    };
  }
}

module.exports = TransformerBlock;
