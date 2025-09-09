/**
 * RoPE (Rotary Position Embedding) - Positional encoding for transformers
 * 
 * Implements Rotary Position Embedding for better position understanding
 * in transformer models, especially for long sequences
 */

const MathUtils = require('../core/math');
const Tensor = require('../core/tensor');

class RoPEEncoding {
  constructor(config) {
    this.config = {
      dim: config.dim,
      maxSeqLen: config.maxSeqLen || 2048,
      base: config.base || 10000,
      ...config
    };
    
    this.dim = this.config.dim;
    this.maxSeqLen = this.config.maxSeqLen;
    this.base = this.config.base;
    
    // Precompute frequency matrix
    this.freqs = this._precomputeFreqs();
  }

  /**
   * Precompute frequency matrix for RoPE
   * @returns {Array} Frequency matrix
   * @private
   */
  _precomputeFreqs() {
    const freqs = [];
    const invFreqs = [];
    
    for (let i = 0; i < this.dim / 2; i++) {
      const freq = 1.0 / Math.pow(this.base, (2 * i) / this.dim);
      invFreqs.push(freq);
    }
    
    for (let pos = 0; pos < this.maxSeqLen; pos++) {
      const posFreqs = [];
      for (let i = 0; i < this.dim / 2; i++) {
        const freq = pos * invFreqs[i];
        posFreqs.push(Math.cos(freq));
        posFreqs.push(Math.sin(freq));
      }
      freqs.push(posFreqs);
    }
    
    return freqs;
  }

  /**
   * Apply RoPE to query and key tensors
   * @param {Tensor} q - Query tensor
   * @param {Tensor} k - Key tensor
   * @param {number} startPos - Starting position
   * @returns {Object} Rotated tensors
   */
  applyRoPE(q, k, startPos = 0) {
    const batchSize = q.shape[0];
    const seqLen = q.shape[1];
    const dim = q.shape[2];
    
    // Get frequency matrix for current positions
    const freqs = this._getFreqsForPositions(startPos, seqLen);
    
    // Apply rotation to queries and keys
    const qRotated = this._applyRotation(q, freqs);
    const kRotated = this._applyRotation(k, freqs);
    
    return {
      q: qRotated,
      k: kRotated
    };
  }

  /**
   * Get frequency matrix for specific positions
   * @param {number} startPos - Starting position
   * @param {number} seqLen - Sequence length
   * @returns {Array} Frequency matrix
   * @private
   */
  _getFreqsForPositions(startPos, seqLen) {
    const freqs = [];
    for (let i = 0; i < seqLen; i++) {
      const pos = startPos + i;
      if (pos < this.maxSeqLen) {
        freqs.push(this.freqs[pos]);
      } else {
        // Extend beyond precomputed range
        freqs.push(this._computeFreqsForPosition(pos));
      }
    }
    return freqs;
  }

  /**
   * Compute frequencies for a specific position
   * @param {number} pos - Position
   * @returns {Array} Frequencies
   * @private
   */
  _computeFreqsForPosition(pos) {
    const freqs = [];
    for (let i = 0; i < this.dim / 2; i++) {
      const freq = pos / Math.pow(this.base, (2 * i) / this.dim);
      freqs.push(Math.cos(freq));
      freqs.push(Math.sin(freq));
    }
    return freqs;
  }

  /**
   * Apply rotation to tensor
   * @param {Tensor} tensor - Input tensor
   * @param {Array} freqs - Frequency matrix
   * @returns {Tensor} Rotated tensor
   * @private
   */
  _applyRotation(tensor, freqs) {
    const batchSize = tensor.shape[0];
    const seqLen = tensor.shape[1];
    const dim = tensor.shape[2];
    
    const rotated = [];
    for (let b = 0; b < batchSize; b++) {
      rotated[b] = [];
      for (let s = 0; s < seqLen; s++) {
        rotated[b][s] = [];
        const posFreqs = freqs[s];
        
        for (let d = 0; d < dim; d += 2) {
          const x = tensor.data[b][s][d];
          const y = tensor.data[b][s][d + 1];
          
          const cos = posFreqs[d];
          const sin = posFreqs[d + 1];
          
          rotated[b][s][d] = x * cos - y * sin;
          rotated[b][s][d + 1] = x * sin + y * cos;
        }
      }
    }
    
    return new Tensor(rotated, [batchSize, seqLen, dim]);
  }

  /**
   * Apply RoPE to single tensor
   * @param {Tensor} tensor - Input tensor
   * @param {number} startPos - Starting position
   * @returns {Tensor} Rotated tensor
   */
  applyRoPESingle(tensor, startPos = 0) {
    const batchSize = tensor.shape[0];
    const seqLen = tensor.shape[1];
    const dim = tensor.shape[2];
    
    const freqs = this._getFreqsForPositions(startPos, seqLen);
    return this._applyRotation(tensor, freqs);
  }

  /**
   * Get frequency matrix for visualization
   * @param {number} startPos - Starting position
   * @param {number} seqLen - Sequence length
   * @returns {Array} Frequency matrix
   */
  getFreqs(startPos = 0, seqLen = 10) {
    return this._getFreqsForPositions(startPos, seqLen);
  }

  /**
   * Update max sequence length
   * @param {number} newMaxSeqLen - New maximum sequence length
   */
  updateMaxSeqLen(newMaxSeqLen) {
    this.maxSeqLen = newMaxSeqLen;
    this.freqs = this._precomputeFreqs();
  }

  /**
   * Get configuration
   * @returns {Object} Configuration
   */
  getConfig() {
    return {
      dim: this.dim,
      maxSeqLen: this.maxSeqLen,
      base: this.base,
      precomputedFreqs: this.freqs.length
    };
  }

  /**
   * Create causal mask for attention
   * @param {number} seqLen - Sequence length
   * @param {number} batchSize - Batch size
   * @returns {Array} Causal mask
   */
  createCausalMask(seqLen, batchSize = 1) {
    const mask = [];
    for (let b = 0; b < batchSize; b++) {
      mask[b] = [];
      for (let i = 0; i < seqLen; i++) {
        mask[b][i] = [];
        for (let j = 0; j < seqLen; j++) {
          mask[b][i][j] = j <= i ? 1 : 0;
        }
      }
    }
    return mask;
  }

  /**
   * Create padding mask
   * @param {Array} lengths - Sequence lengths
   * @param {number} maxLen - Maximum length
   * @returns {Array} Padding mask
   */
  createPaddingMask(lengths, maxLen) {
    const batchSize = lengths.length;
    const mask = [];
    
    for (let b = 0; b < batchSize; b++) {
      mask[b] = [];
      for (let i = 0; i < maxLen; i++) {
        mask[b][i] = [];
        for (let j = 0; j < maxLen; j++) {
          const validI = i < lengths[b];
          const validJ = j < lengths[b];
          mask[b][i][j] = validI && validJ ? 1 : 0;
        }
      }
    }
    
    return mask;
  }

  /**
   * Combine multiple masks
   * @param {Array} masks - Array of masks
   * @returns {Array} Combined mask
   */
  combineMasks(masks) {
    if (masks.length === 0) return null;
    
    const batchSize = masks[0].length;
    const seqLen = masks[0][0].length;
    const combined = [];
    
    for (let b = 0; b < batchSize; b++) {
      combined[b] = [];
      for (let i = 0; i < seqLen; i++) {
        combined[b][i] = [];
        for (let j = 0; j < seqLen; j++) {
          let result = 1;
          for (const mask of masks) {
            result *= mask[b][i][j];
          }
          combined[b][i][j] = result;
        }
      }
    }
    
    return combined;
  }

  /**
   * Get memory usage
   * @returns {Object} Memory usage statistics
   */
  getMemoryUsage() {
    const freqSize = this.freqs.length * this.freqs[0].length * 8; // 8 bytes per number
    const totalSize = freqSize + (this.dim * 8); // Additional overhead
    
    return {
      freqMatrixSize: freqSize,
      totalSize: totalSize,
      maxSeqLen: this.maxSeqLen,
      dim: this.dim
    };
  }
}

module.exports = RoPEEncoding;
