/**
 * Normalization Layers - Various normalization techniques
 * 
 * Implements RMSNorm, LayerNorm, and other normalization methods
 */

const MathUtils = require('../core/math');

class RMSNorm {
  constructor(config) {
    this.config = {
      dim: config.dim,
      epsilon: config.epsilon || 1e-6,
      ...config
    };
    
    this.dim = this.config.dim;
    this.epsilon = this.config.epsilon;
    
    // Initialize weight
    this.weight = new Array(this.dim).fill(1.0);
  }

  /**
   * Forward pass
   * @param {Array} x - Input data
   * @returns {Array} Normalized data
   */
  forward(x) {
    const batchSize = x.length;
    const seqLen = x[0].length;
    const dim = x[0][0].length;
    
    const normalized = [];
    for (let b = 0; b < batchSize; b++) {
      normalized[b] = [];
      for (let s = 0; s < seqLen; s++) {
        normalized[b][s] = [];
        
        // Calculate RMS
        let sumSquares = 0;
        for (let d = 0; d < dim; d++) {
          sumSquares += x[b][s][d] * x[b][s][d];
        }
        const rms = Math.sqrt(sumSquares / dim + this.epsilon);
        
        // Normalize and scale
        for (let d = 0; d < dim; d++) {
          normalized[b][s][d] = (x[b][s][d] / rms) * this.weight[d];
        }
      }
    }
    
    return normalized;
  }

  /**
   * Get parameters
   * @returns {Object} Layer parameters
   */
  getParameters() {
    return {
      weight: this.weight
    };
  }

  /**
   * Get parameter count
   * @returns {number} Number of parameters
   */
  getParameterCount() {
    return this.dim;
  }
}

class LayerNorm {
  constructor(config) {
    this.config = {
      dim: config.dim,
      epsilon: config.epsilon || 1e-6,
      ...config
    };
    
    this.dim = this.config.dim;
    this.epsilon = this.config.epsilon;
    
    // Initialize weight and bias
    this.weight = new Array(this.dim).fill(1.0);
    this.bias = new Array(this.dim).fill(0.0);
  }

  /**
   * Forward pass
   * @param {Array} x - Input data
   * @returns {Array} Normalized data
   */
  forward(x) {
    const batchSize = x.length;
    const seqLen = x[0].length;
    const dim = x[0][0].length;
    
    const normalized = [];
    for (let b = 0; b < batchSize; b++) {
      normalized[b] = [];
      for (let s = 0; s < seqLen; s++) {
        normalized[b][s] = [];
        
        // Calculate mean
        let sum = 0;
        for (let d = 0; d < dim; d++) {
          sum += x[b][s][d];
        }
        const mean = sum / dim;
        
        // Calculate variance
        let sumSquares = 0;
        for (let d = 0; d < dim; d++) {
          const diff = x[b][s][d] - mean;
          sumSquares += diff * diff;
        }
        const variance = sumSquares / dim;
        const std = Math.sqrt(variance + this.epsilon);
        
        // Normalize and scale
        for (let d = 0; d < dim; d++) {
          normalized[b][s][d] = ((x[b][s][d] - mean) / std) * this.weight[d] + this.bias[d];
        }
      }
    }
    
    return normalized;
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
    return this.dim * 2;
  }
}

class BatchNorm {
  constructor(config) {
    this.config = {
      dim: config.dim,
      epsilon: config.epsilon || 1e-6,
      momentum: config.momentum || 0.1,
      ...config
    };
    
    this.dim = this.config.dim;
    this.epsilon = this.config.epsilon;
    this.momentum = this.config.momentum;
    
    // Initialize parameters
    this.weight = new Array(this.dim).fill(1.0);
    this.bias = new Array(this.dim).fill(0.0);
    
    // Running statistics
    this.runningMean = new Array(this.dim).fill(0.0);
    this.runningVar = new Array(this.dim).fill(1.0);
  }

  /**
   * Forward pass
   * @param {Array} x - Input data
   * @param {Object} options - Additional options
   * @returns {Array} Normalized data
   */
  forward(x, options = {}) {
    const { training = true } = options;
    const batchSize = x.length;
    const seqLen = x[0].length;
    const dim = x[0][0].length;
    
    if (training) {
      // Calculate batch statistics
      const mean = this._calculateMean(x);
      const var_ = this._calculateVariance(x, mean);
      
      // Update running statistics
      for (let d = 0; d < dim; d++) {
        this.runningMean[d] = this.momentum * this.runningMean[d] + (1 - this.momentum) * mean[d];
        this.runningVar[d] = this.momentum * this.runningVar[d] + (1 - this.momentum) * var_[d];
      }
      
      return this._normalize(x, mean, var_);
    } else {
      return this._normalize(x, this.runningMean, this.runningVar);
    }
  }

  /**
   * Calculate mean across batch
   * @param {Array} x - Input data
   * @returns {Array} Mean values
   * @private
   */
  _calculateMean(x) {
    const batchSize = x.length;
    const seqLen = x[0].length;
    const dim = x[0][0].length;
    
    const mean = new Array(dim).fill(0);
    
    for (let d = 0; d < dim; d++) {
      let sum = 0;
      for (let b = 0; b < batchSize; b++) {
        for (let s = 0; s < seqLen; s++) {
          sum += x[b][s][d];
        }
      }
      mean[d] = sum / (batchSize * seqLen);
    }
    
    return mean;
  }

  /**
   * Calculate variance across batch
   * @param {Array} x - Input data
   * @param {Array} mean - Mean values
   * @returns {Array} Variance values
   * @private
   */
  _calculateVariance(x, mean) {
    const batchSize = x.length;
    const seqLen = x[0].length;
    const dim = x[0][0].length;
    
    const var_ = new Array(dim).fill(0);
    
    for (let d = 0; d < dim; d++) {
      let sumSquares = 0;
      for (let b = 0; b < batchSize; b++) {
        for (let s = 0; s < seqLen; s++) {
          const diff = x[b][s][d] - mean[d];
          sumSquares += diff * diff;
        }
      }
      var_[d] = sumSquares / (batchSize * seqLen);
    }
    
    return var_;
  }

  /**
   * Normalize data
   * @param {Array} x - Input data
   * @param {Array} mean - Mean values
   * @param {Array} var_ - Variance values
   * @returns {Array} Normalized data
   * @private
   */
  _normalize(x, mean, var_) {
    const batchSize = x.length;
    const seqLen = x[0].length;
    const dim = x[0][0].length;
    
    const normalized = [];
    for (let b = 0; b < batchSize; b++) {
      normalized[b] = [];
      for (let s = 0; s < seqLen; s++) {
        normalized[b][s] = [];
        for (let d = 0; d < dim; d++) {
          const std = Math.sqrt(var_[d] + this.epsilon);
          normalized[b][s][d] = ((x[b][s][d] - mean[d]) / std) * this.weight[d] + this.bias[d];
        }
      }
    }
    
    return normalized;
  }

  /**
   * Get parameters
   * @returns {Object} Layer parameters
   */
  getParameters() {
    return {
      weight: this.weight,
      bias: this.bias,
      runningMean: this.runningMean,
      runningVar: this.runningVar
    };
  }

  /**
   * Get parameter count
   * @returns {number} Number of parameters
   */
  getParameterCount() {
    return this.dim * 4;
  }
}

module.exports = { RMSNorm, LayerNorm, BatchNorm };
