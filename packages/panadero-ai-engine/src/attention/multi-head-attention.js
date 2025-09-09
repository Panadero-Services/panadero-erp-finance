/**
 * Multi-Head Attention - Standard multi-head attention mechanism
 * 
 * Implements the standard multi-head attention from "Attention is All You Need"
 * with optimizations for JavaScript performance
 */

const MathUtils = require('../core/math');
const Tensor = require('../core/tensor');

class MultiHeadAttention {
  constructor(config) {
    this.config = {
      dim: config.dim,
      nHeads: config.nHeads,
      headDim: config.dim / config.nHeads,
      dropout: config.dropout || 0.1,
      bias: config.bias !== false,
      ...config
    };
    
    this.headDim = this.config.headDim;
    this.scale = 1 / Math.sqrt(this.headDim);
    
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
    
    // Biases (optional)
    if (this.config.bias) {
      this.bq = new Array(nHeads * headDim).fill(0);
      this.bk = new Array(nHeads * headDim).fill(0);
      this.bv = new Array(nHeads * headDim).fill(0);
      this.bo = new Array(dim).fill(0);
    }
  }

  /**
   * Create weight matrix with Xavier initialization
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
    const { mask = null, training = true } = options;
    const { nHeads, headDim } = this.config;
    
    // Get input dimensions
    const batchSize = x.shape[0];
    const seqLen = x.shape[1];
    const dim = x.shape[2];
    
    // Linear projections
    const q = this._linearProjection(x, this.wq, this.bq);
    const k = this._linearProjection(x, this.wk, this.bk);
    const v = this._linearProjection(x, this.wv, this.bv);
    
    // Reshape for multi-head attention
    const qReshaped = this._reshapeForHeads(q, batchSize, seqLen, nHeads, headDim);
    const kReshaped = this._reshapeForHeads(k, batchSize, seqLen, nHeads, headDim);
    const vReshaped = this._reshapeForHeads(v, batchSize, seqLen, nHeads, headDim);
    
    // Transpose for attention computation
    const qTransposed = this._transposeForAttention(qReshaped);
    const kTransposed = this._transposeForAttention(kReshaped);
    const vTransposed = this._transposeForAttention(vReshaped);
    
    // Compute attention scores
    const scores = this._computeAttentionScores(qTransposed, kTransposed);
    
    // Apply mask if provided
    if (mask) {
      this._applyMask(scores, mask);
    }
    
    // Apply softmax
    const attentionWeights = this._applySoftmax(scores);
    
    // Apply dropout during training
    if (training && this.config.dropout > 0) {
      this._applyDropout(attentionWeights, this.config.dropout);
    }
    
    // Apply attention to values
    const attended = this._applyAttention(attentionWeights, vTransposed);
    
    // Transpose back and reshape
    const output = this._transposeAndReshape(attended, batchSize, seqLen, nHeads, headDim);
    
    // Final linear projection
    const result = this._linearProjection(output, this.wo, this.bo);
    
    return new Tensor(result, [batchSize, seqLen, dim]);
  }

  /**
   * Linear projection
   * @param {Tensor} x - Input tensor
   * @param {Array} weights - Weight matrix
   * @param {Array} bias - Bias vector
   * @returns {Array} Projected data
   * @private
   */
  _linearProjection(x, weights, bias) {
    const batchSize = x.shape[0];
    const seqLen = x.shape[1];
    const outDim = weights.length;
    
    const result = [];
    for (let b = 0; b < batchSize; b++) {
      result[b] = [];
      for (let s = 0; s < seqLen; s++) {
        result[b][s] = [];
        for (let d = 0; d < outDim; d++) {
          let sum = 0;
          for (let i = 0; i < x.shape[2]; i++) {
            sum += x.data[b][s][i] * weights[d][i];
          }
          if (bias) {
            sum += bias[d];
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
   * @param {number} batchSize - Batch size
   * @param {number} seqLen - Sequence length
   * @param {number} nHeads - Number of heads
   * @param {number} headDim - Head dimension
   * @returns {Array} Reshaped data
   * @private
   */
  _reshapeForHeads(data, batchSize, seqLen, nHeads, headDim) {
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
   * Transpose for attention computation
   * @param {Array} data - Input data
   * @returns {Array} Transposed data
   * @private
   */
  _transposeForAttention(data) {
    const batchSize = data.length;
    const seqLen = data[0].length;
    const nHeads = data[0][0].length;
    const headDim = data[0][0][0].length;
    
    const result = [];
    for (let b = 0; b < batchSize; b++) {
      result[b] = [];
      for (let h = 0; h < nHeads; h++) {
        result[b][h] = [];
        for (let s = 0; s < seqLen; s++) {
          result[b][h][s] = [];
          for (let d = 0; d < headDim; d++) {
            result[b][h][s][d] = data[b][s][h][d];
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
    const nHeads = q[0].length;
    const seqLen = q[0][0].length;
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
              sum += q[b][h][i][d] * k[b][h][j][d];
            }
            scores[b][h][i][j] = sum * this.scale;
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
   * Apply dropout to attention weights
   * @param {Array} weights - Attention weights
   * @param {number} dropoutRate - Dropout rate
   * @private
   */
  _applyDropout(weights, dropoutRate) {
    const batchSize = weights.length;
    const nHeads = weights[0].length;
    const seqLen = weights[0][0].length;
    
    for (let b = 0; b < batchSize; b++) {
      for (let h = 0; h < nHeads; h++) {
        for (let i = 0; i < seqLen; i++) {
          for (let j = 0; j < seqLen; j++) {
            if (Math.random() < dropoutRate) {
              weights[b][h][i][j] = 0;
            }
          }
        }
      }
    }
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
              sum += weights[b][h][i][j] * v[b][h][j][d];
            }
            attended[b][h][i][d] = sum;
          }
        }
      }
    }
    return attended;
  }

  /**
   * Transpose and reshape attended values
   * @param {Array} attended - Attended values
   * @param {number} batchSize - Batch size
   * @param {number} seqLen - Sequence length
   * @param {number} nHeads - Number of heads
   * @param {number} headDim - Head dimension
   * @returns {Array} Reshaped data
   * @private
   */
  _transposeAndReshape(attended, batchSize, seqLen, nHeads, headDim) {
    const result = [];
    for (let b = 0; b < batchSize; b++) {
      result[b] = [];
      for (let s = 0; s < seqLen; s++) {
        result[b][s] = [];
        for (let h = 0; h < nHeads; h++) {
          for (let d = 0; d < headDim; d++) {
            const index = h * headDim + d;
            result[b][s][index] = attended[b][h][s][d];
          }
        }
      }
    }
    return result;
  }

  /**
   * Get attention weights for visualization
   * @param {Tensor} x - Input tensor
   * @param {Object} options - Options
   * @returns {Array} Attention weights
   */
  getAttentionWeights(x, options = {}) {
    const { mask = null } = options;
    const { nHeads, headDim } = this.config;
    
    const batchSize = x.shape[0];
    const seqLen = x.shape[1];
    
    // Linear projections
    const q = this._linearProjection(x, this.wq, this.bq);
    const k = this._linearProjection(x, this.wk, this.bk);
    
    // Reshape and transpose
    const qReshaped = this._reshapeForHeads(q, batchSize, seqLen, nHeads, headDim);
    const kReshaped = this._reshapeForHeads(k, batchSize, seqLen, nHeads, headDim);
    
    const qTransposed = this._transposeForAttention(qReshaped);
    const kTransposed = this._transposeForAttention(kReshaped);
    
    // Compute scores
    const scores = this._computeAttentionScores(qTransposed, kTransposed);
    
    // Apply mask
    if (mask) {
      this._applyMask(scores, mask);
    }
    
    // Apply softmax
    const weights = this._applySoftmax(scores);
    
    return weights;
  }
}

module.exports = MultiHeadAttention;
