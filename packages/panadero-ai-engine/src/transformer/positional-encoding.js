/**
 * Positional Encoding - Positional encoding for transformers
 * 
 * Implements various positional encoding methods including
 * sinusoidal, learned, and relative positional encodings
 */

const MathUtils = require('../core/math');

class PositionalEncoding {
  constructor(config) {
    this.config = {
      dim: config.dim,
      maxSeqLen: config.maxSeqLen || 2048,
      type: config.type || 'sinusoidal',
      ...config
    };
    
    this.dim = this.config.dim;
    this.maxSeqLen = this.config.maxSeqLen;
    this.type = this.config.type;
    
    // Initialize positional encoding
    this.initializeEncoding();
  }

  /**
   * Initialize positional encoding
   */
  initializeEncoding() {
    switch (this.type) {
      case 'sinusoidal':
        this.encoding = this._createSinusoidalEncoding();
        break;
      case 'learned':
        this.encoding = this._createLearnedEncoding();
        break;
      case 'relative':
        this.encoding = this._createRelativeEncoding();
        break;
      default:
        this.encoding = this._createSinusoidalEncoding();
    }
  }

  /**
   * Create sinusoidal positional encoding
   * @returns {Array} Sinusoidal encoding
   * @private
   */
  _createSinusoidalEncoding() {
    const encoding = [];
    
    for (let pos = 0; pos < this.maxSeqLen; pos++) {
      encoding[pos] = [];
      for (let i = 0; i < this.dim; i++) {
        if (i % 2 === 0) {
          encoding[pos][i] = Math.sin(pos / Math.pow(10000, i / this.dim));
        } else {
          encoding[pos][i] = Math.cos(pos / Math.pow(10000, (i - 1) / this.dim));
        }
      }
    }
    
    return encoding;
  }

  /**
   * Create learned positional encoding
   * @returns {Array} Learned encoding
   * @private
   */
  _createLearnedEncoding() {
    const encoding = [];
    
    for (let pos = 0; pos < this.maxSeqLen; pos++) {
      encoding[pos] = [];
      for (let i = 0; i < this.dim; i++) {
        encoding[pos][i] = MathUtils.randomNormal(0, 0.02);
      }
    }
    
    return encoding;
  }

  /**
   * Create relative positional encoding
   * @returns {Array} Relative encoding
   * @private
   */
  _createRelativeEncoding() {
    const encoding = [];
    const maxRelativeDistance = this.maxSeqLen;
    
    for (let pos = 0; pos < this.maxSeqLen; pos++) {
      encoding[pos] = [];
      for (let i = 0; i < this.dim; i++) {
        if (i % 2 === 0) {
          encoding[pos][i] = Math.sin(pos / Math.pow(10000, i / this.dim));
        } else {
          encoding[pos][i] = Math.cos(pos / Math.pow(10000, (i - 1) / this.dim));
        }
      }
    }
    
    return encoding;
  }

  /**
   * Get positional encoding for sequence
   * @param {number} seqLen - Sequence length
   * @returns {Array} Positional encoding
   */
  getEncoding(seqLen) {
    if (seqLen > this.maxSeqLen) {
      throw new Error(`Sequence length ${seqLen} exceeds maximum ${this.maxSeqLen}`);
    }
    
    return this.encoding.slice(0, seqLen);
  }

  /**
   * Add positional encoding to input
   * @param {Array} input - Input embeddings
   * @returns {Array} Input with positional encoding
   */
  addPositionalEncoding(input) {
    const batchSize = input.length;
    const seqLen = input[0].length;
    const dim = input[0][0].length;
    
    if (dim !== this.dim) {
      throw new Error(`Input dimension ${dim} does not match encoding dimension ${this.dim}`);
    }
    
    const posEnc = this.getEncoding(seqLen);
    const result = [];
    
    for (let b = 0; b < batchSize; b++) {
      result[b] = [];
      for (let s = 0; s < seqLen; s++) {
        result[b][s] = [];
        for (let d = 0; d < dim; d++) {
          result[b][s][d] = input[b][s][d] + posEnc[s][d];
        }
      }
    }
    
    return result;
  }

  /**
   * Get relative positional encoding
   * @param {number} seqLen - Sequence length
   * @returns {Array} Relative positional encoding
   */
  getRelativeEncoding(seqLen) {
    const encoding = [];
    
    for (let i = 0; i < seqLen; i++) {
      encoding[i] = [];
      for (let j = 0; j < seqLen; j++) {
        const relativePos = i - j;
        const absPos = Math.abs(relativePos);
        
        if (absPos < this.maxSeqLen) {
          encoding[i][j] = this.encoding[absPos];
        } else {
          // Use the last available encoding for positions beyond maxSeqLen
          encoding[i][j] = this.encoding[this.maxSeqLen - 1];
        }
      }
    }
    
    return encoding;
  }

  /**
   * Update maximum sequence length
   * @param {number} newMaxSeqLen - New maximum sequence length
   */
  updateMaxSeqLen(newMaxSeqLen) {
    if (newMaxSeqLen > this.maxSeqLen) {
      this.maxSeqLen = newMaxSeqLen;
      this.initializeEncoding();
    }
  }

  /**
   * Get encoding statistics
   * @returns {Object} Encoding statistics
   */
  getStats() {
    return {
      type: this.type,
      dim: this.dim,
      maxSeqLen: this.maxSeqLen,
      totalElements: this.maxSeqLen * this.dim,
      memoryUsage: this.maxSeqLen * this.dim * 4 // 4 bytes per float32
    };
  }

  /**
   * Get encoding configuration
   * @returns {Object} Encoding configuration
   */
  getConfig() {
    return {
      ...this.config,
      stats: this.getStats()
    };
  }
}

module.exports = PositionalEncoding;
