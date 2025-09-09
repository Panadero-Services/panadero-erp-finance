/**
 * Feed Forward Network - Position-wise feed-forward network
 * 
 * Implements a two-layer feed-forward network for transformer models
 */

const MathUtils = require('../core/math');

class FeedForward {
  constructor(config) {
    this.config = {
      dim: config.dim,
      hiddenDim: config.hiddenDim || config.dim * 4,
      activation: config.activation || 'swish',
      dropout: config.dropout || 0.1,
      ...config
    };
    
    this.dim = this.config.dim;
    this.hiddenDim = this.config.hiddenDim;
    this.activation = this.config.activation;
    this.dropout = this.config.dropout;
    
    // Initialize weights and bias
    this.initializeWeights();
  }

  /**
   * Initialize weights and bias
   */
  initializeWeights() {
    // First linear layer
    this.w1 = this._createWeightMatrix(this.dim, this.hiddenDim);
    this.b1 = new Array(this.hiddenDim).fill(0);
    
    // Second linear layer
    this.w2 = this._createWeightMatrix(this.hiddenDim, this.dim);
    this.b2 = new Array(this.dim).fill(0);
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
   * @param {Array} x - Input data
   * @param {Object} options - Additional options
   * @returns {Array} Output data
   */
  forward(x, options = {}) {
    const { training = true } = options;
    
    // First linear layer
    const hidden = this._linearForward(x, this.w1, this.b1);
    
    // Apply activation
    const activated = this._applyActivation(hidden);
    
    // Apply dropout during training
    if (training && this.dropout > 0) {
      this._applyDropout(activated, this.dropout);
    }
    
    // Second linear layer
    const output = this._linearForward(activated, this.w2, this.b2);
    
    return output;
  }

  /**
   * Linear forward pass
   * @param {Array} x - Input data
   * @param {Array} weights - Weight matrix
   * @param {Array} bias - Bias vector
   * @returns {Array} Output data
   * @private
   */
  _linearForward(x, weights, bias) {
    const batchSize = x.length;
    const seqLen = x[0].length;
    const outDim = weights.length;
    
    const result = [];
    for (let b = 0; b < batchSize; b++) {
      result[b] = [];
      for (let s = 0; s < seqLen; s++) {
        result[b][s] = [];
        for (let d = 0; d < outDim; d++) {
          let sum = bias[d];
          for (let i = 0; i < this.dim; i++) {
            sum += x[b][s][i] * weights[d][i];
          }
          result[b][s][d] = sum;
        }
      }
    }
    
    return result;
  }

  /**
   * Apply activation function
   * @param {Array} data - Input data
   * @returns {Array} Activated data
   * @private
   */
  _applyActivation(data) {
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
          
          switch (this.activation) {
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
            case 'tanh':
              result = MathUtils.tanh(value);
              break;
            case 'sigmoid':
              result = MathUtils.sigmoid(value);
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
   * Apply dropout
   * @param {Array} data - Input data
   * @param {number} rate - Dropout rate
   * @private
   */
  _applyDropout(data, rate) {
    const batchSize = data.length;
    const seqLen = data[0].length;
    const hiddenDim = data[0][0].length;
    
    for (let b = 0; b < batchSize; b++) {
      for (let s = 0; s < seqLen; s++) {
        for (let h = 0; h < hiddenDim; h++) {
          if (Math.random() < rate) {
            data[b][s][h] = 0;
          }
        }
      }
    }
  }

  /**
   * Get parameters
   * @returns {Object} Layer parameters
   */
  getParameters() {
    return {
      w1: this.w1,
      b1: this.b1,
      w2: this.w2,
      b2: this.b2
    };
  }

  /**
   * Get parameter count
   * @returns {number} Number of parameters
   */
  getParameterCount() {
    return this.dim * this.hiddenDim + this.hiddenDim + 
           this.hiddenDim * this.dim + this.dim;
  }
}

module.exports = FeedForward;
