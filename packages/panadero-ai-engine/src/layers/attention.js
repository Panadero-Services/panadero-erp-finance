/**
 * Attention Layer - Basic attention mechanism
 * 
 * Implements a simple attention mechanism for neural networks
 */

const MathUtils = require('../core/math');

class Attention {
  constructor(config) {
    this.config = {
      dim: config.dim,
      nHeads: config.nHeads || 1,
      headDim: config.dim / (config.nHeads || 1),
      dropout: config.dropout || 0.1,
      ...config
    };
    
    this.dim = this.config.dim;
    this.nHeads = this.config.nHeads;
    this.headDim = this.config.headDim;
    this.dropout = this.config.dropout;
    
    // Initialize weights
    this.initializeWeights();
  }

  /**
   * Initialize attention weights
   */
  initializeWeights() {
    const { dim, nHeads, headDim } = this.config;
    
    // Query, Key, Value projections
    this.wq = this._createWeightMatrix(dim, nHeads * headDim);
    this.wk = this._createWeightMatrix(dim, nHeads * headDim);
    this.wv = this._createWeightMatrix(dim, nHeads * headDim);
    
    // Output projection
    this.wo = this._createWeightMatrix(nHeads * headDim, dim);
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
    const { mask = null, training = true } = options;
    
    // Linear projections
    const q = this._linearProjection(x, this.wq);
    const k = this._linearProjection(x, this.wk);
    const v = this._linearProjection(x, this.wv);
    
    // Reshape for multi-head attention
    const qReshaped = this._reshapeForHeads(q);
    const kReshaped = this._reshapeForHeads(k);
    const vReshaped = this._reshapeForHeads(v);
    
    // Compute attention scores
    const scores = this._computeAttentionScores(qReshaped, kReshaped);
    
    // Apply mask if provided
    if (mask) {
      this._applyMask(scores, mask);
    }
    
    // Apply softmax
    const attentionWeights = this._applySoftmax(scores);
    
    // Apply attention to values
    const attended = this._applyAttention(attentionWeights, vReshaped);
    
    // Reshape and project output
    const output = this._reshapeAndProject(attended);
    
    return output;
  }

  /**
   * Linear projection
   * @param {Array} x - Input data
   * @param {Array} weights - Weight matrix
   * @returns {Array} Projected data
   * @private
   */
  _linearProjection(x, weights) {
    const batchSize = x.length;
    const seqLen = x[0].length;
    const outDim = weights.length;
    
    const result = [];
    for (let b = 0; b < batchSize; b++) {
      result[b] = [];
      for (let s = 0; s < seqLen; s++) {
        result[b][s] = [];
        for (let d = 0; d < outDim; d++) {
          let sum = 0;
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
   * Reshape for multi-head attention
   * @param {Array} data - Input data
   * @returns {Array} Reshaped data
   * @private
   */
  _reshapeForHeads(data) {
    const batchSize = data.length;
    const seqLen = data[0].length;
    const nHeads = this.nHeads;
    const headDim = this.headDim;
    
    const result = [];
    for (let b = 0; b < batchSize; b++) {
      result[b] = [];
      for (let s = 0; s < seqLen; s++) {
        result[b][s] = [];
        for (let h = 0; h < nHeads; h++) {
          result[b][s][h] = [];
          for (let d = 0; d < headDim; d++) {
            const index = h * headDim + d;
            result[b][s][h][d] = data[b][s][index];
          }
        }
      }
    }
    
    return result;
  }

  /**
   * Compute attention scores
   * @param {Array} q - Query tensor
   * @param {Array} k - Key tensor
   * @returns {Array} Attention scores
   * @private
   */
  _computeAttentionScores(q, k) {
    const batchSize = q.length;
    const seqLen = q[0].length;
    const nHeads = q[0][0].length;
    const headDim = q[0][0][0].length;
    
    const scores = [];
    for (let b = 0; b < batchSize; b++) {
      scores[b] = [];
      for (let h = 0; h < nHeads; h++) {
        scores[b][h] = [];
        for (let i = 0; i < seqLen; i++) {
          scores[b][h][i] = [];
          for (let j = 0; j < seqLen; j++) {
            let sum = 0;
            for (let d = 0; d < headDim; d++) {
              sum += q[b][i][h][d] * k[b][j][h][d];
            }
            scores[b][h][i][j] = sum / Math.sqrt(headDim);
          }
        }
      }
    }
    
    return scores;
  }

  /**
   * Apply mask to attention scores
   * @param {Array} scores - Attention scores
   * @param {Array} mask - Mask tensor
   * @private
   */
  _applyMask(scores, mask) {
    const batchSize = scores.length;
    const nHeads = scores[0].length;
    const seqLen = scores[0][0].length;
    
    for (let b = 0; b < batchSize; b++) {
      for (let h = 0; h < nHeads; h++) {
        for (let i = 0; i < seqLen; i++) {
          for (let j = 0; j < seqLen; j++) {
            if (mask[b][i][j] === 0) {
              scores[b][h][i][j] = -Infinity;
            }
          }
        }
      }
    }
  }

  /**
   * Apply softmax to attention scores
   * @param {Array} scores - Attention scores
   * @returns {Array} Attention weights
   * @private
   */
  _applySoftmax(scores) {
    const batchSize = scores.length;
    const nHeads = scores[0].length;
    const seqLen = scores[0][0].length;
    
    const weights = [];
    for (let b = 0; b < batchSize; b++) {
      weights[b] = [];
      for (let h = 0; h < nHeads; h++) {
        weights[b][h] = [];
        for (let i = 0; i < seqLen; i++) {
          const row = scores[b][h][i];
          const softmaxRow = MathUtils.softmax(row);
          weights[b][h][i] = softmaxRow;
        }
      }
    }
    
    return weights;
  }

  /**
   * Apply attention weights to values
   * @param {Array} weights - Attention weights
   * @param {Array} v - Value tensor
   * @returns {Array} Attended values
   * @private
   */
  _applyAttention(weights, v) {
    const batchSize = weights.length;
    const nHeads = weights[0].length;
    const seqLen = weights[0][0].length;
    const headDim = v[0][0][0].length;
    
    const attended = [];
    for (let b = 0; b < batchSize; b++) {
      attended[b] = [];
      for (let h = 0; h < nHeads; h++) {
        attended[b][h] = [];
        for (let i = 0; i < seqLen; i++) {
          attended[b][h][i] = [];
          for (let d = 0; d < headDim; d++) {
            let sum = 0;
            for (let j = 0; j < seqLen; j++) {
              sum += weights[b][h][i][j] * v[b][j][h][d];
            }
            attended[b][h][i][d] = sum;
          }
        }
      }
    }
    
    return attended;
  }

  /**
   * Reshape and project output
   * @param {Array} attended - Attended values
   * @returns {Array} Final output
   * @private
   */
  _reshapeAndProject(attended) {
    const batchSize = attended.length;
    const seqLen = attended[0].length;
    const nHeads = attended[0][0].length;
    const headDim = attended[0][0][0].length;
    
    // Reshape to [batch, seq, nHeads * headDim]
    const reshaped = [];
    for (let b = 0; b < batchSize; b++) {
      reshaped[b] = [];
      for (let s = 0; s < seqLen; s++) {
        reshaped[b][s] = [];
        for (let h = 0; h < nHeads; h++) {
          for (let d = 0; d < headDim; d++) {
            const index = h * headDim + d;
            reshaped[b][s][index] = attended[b][h][s][d];
          }
        }
      }
    }
    
    // Project to output dimension
    return this._linearProjection(reshaped, this.wo);
  }

  /**
   * Get parameters
   * @returns {Object} Layer parameters
   */
  getParameters() {
    return {
      wq: this.wq,
      wk: this.wk,
      wv: this.wv,
      wo: this.wo
    };
  }

  /**
   * Get parameter count
   * @returns {number} Number of parameters
   */
  getParameterCount() {
    const dim = this.dim;
    const nHeads = this.nHeads;
    const headDim = this.headDim;
    
    return dim * nHeads * headDim * 3 + nHeads * headDim * dim;
  }
}

module.exports = Attention;
