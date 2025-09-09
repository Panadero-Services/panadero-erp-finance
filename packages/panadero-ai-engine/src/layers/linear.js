/**
 * Linear Layer - Fully connected linear layer
 * 
 * Implements a standard linear transformation layer for neural networks
 */

const MathUtils = require('../core/math');

class LinearLayer {
  constructor(config) {
    this.config = {
      inFeatures: config.inFeatures,
      outFeatures: config.outFeatures,
      bias: config.bias !== false,
      ...config
    };
    
    this.inFeatures = this.config.inFeatures;
    this.outFeatures = this.config.outFeatures;
    this.bias = this.config.bias;
    
    // Initialize weights and bias
    this.initializeWeights();
  }

  /**
   * Initialize weights and bias
   */
  initializeWeights() {
    // Xavier/Glorot initialization
    const limit = Math.sqrt(6 / (this.inFeatures + this.outFeatures));
    
    this.weight = [];
    for (let i = 0; i < this.outFeatures; i++) {
      this.weight[i] = [];
      for (let j = 0; j < this.inFeatures; j++) {
        this.weight[i][j] = MathUtils.randomUniform(-limit, limit);
      }
    }
    
    if (this.bias) {
      this.bias = new Array(this.outFeatures).fill(0);
    }
  }

  /**
   * Forward pass
   * @param {Array} input - Input data
   * @returns {Array} Output data
   */
  forward(input) {
    const batchSize = input.length;
    const output = [];
    
    for (let b = 0; b < batchSize; b++) {
      output[b] = [];
      for (let i = 0; i < this.outFeatures; i++) {
        let sum = this.bias ? this.bias[i] : 0;
        for (let j = 0; j < this.inFeatures; j++) {
          sum += input[b][j] * this.weight[i][j];
        }
        output[b][i] = sum;
      }
    }
    
    return output;
  }

  /**
   * Get parameters
   * @returns {Object} Layer parameters
   */
  getParameters() {
    return {
      weight: this.weight,
      bias: this.bias
    };
  }

  /**
   * Get parameter count
   * @returns {number} Number of parameters
   */
  getParameterCount() {
    let count = this.inFeatures * this.outFeatures;
    if (this.bias) {
      count += this.outFeatures;
    }
    return count;
  }
}

module.exports = LinearLayer;
