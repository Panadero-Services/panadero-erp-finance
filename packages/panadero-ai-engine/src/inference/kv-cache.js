/**
 * KV Cache - Key-Value caching for efficient inference
 * 
 * Implements efficient key-value caching for transformer models
 * to speed up autoregressive generation
 */

class KVCache {
  constructor(config) {
    this.config = {
      maxBatchSize: config.maxBatchSize || 1,
      maxSeqLen: config.maxSeqLen || 2048,
      nHeads: config.nHeads || 8,
      headDim: config.headDim || 64,
      dtype: config.dtype || 'float32',
      ...config
    };
    
    this.maxBatchSize = this.config.maxBatchSize;
    this.maxSeqLen = this.config.maxSeqLen;
    this.nHeads = this.config.nHeads;
    this.headDim = this.config.headDim;
    this.dtype = this.config.dtype;
    
    // Initialize cache
    this.cache = this._initializeCache();
    this.currentLength = 0;
    this.batchSize = 0;
  }

  /**
   * Initialize cache structure
   * @returns {Object} Cache structure
   * @private
   */
  _initializeCache() {
    const cache = {
      k: new Array(this.maxBatchSize),
      v: new Array(this.maxBatchSize)
    };
    
    for (let b = 0; b < this.maxBatchSize; b++) {
      cache.k[b] = new Array(this.maxSeqLen);
      cache.v[b] = new Array(this.maxSeqLen);
      
      for (let s = 0; s < this.maxSeqLen; s++) {
        cache.k[b][s] = new Array(this.nHeads);
        cache.v[b][s] = new Array(this.nHeads);
        
        for (let h = 0; h < this.nHeads; h++) {
          cache.k[b][s][h] = new Array(this.headDim).fill(0);
          cache.v[b][s][h] = new Array(this.headDim).fill(0);
        }
      }
    }
    
    return cache;
  }

  /**
   * Update cache with new key-value pairs
   * @param {Array} keys - New key values
   * @param {Array} values - New value values
   * @param {number} startPos - Starting position
   * @param {number} batchSize - Batch size
   */
  update(keys, values, startPos, batchSize) {
    if (batchSize > this.maxBatchSize) {
      throw new Error(`Batch size ${batchSize} exceeds maximum ${this.maxBatchSize}`);
    }
    
    this.batchSize = batchSize;
    const seqLen = keys[0].length;
    
    for (let b = 0; b < batchSize; b++) {
      for (let s = 0; s < seqLen; s++) {
        const pos = startPos + s;
        if (pos >= this.maxSeqLen) {
          continue;
        }
        
        for (let h = 0; h < this.nHeads; h++) {
          for (let d = 0; d < this.headDim; d++) {
            this.cache.k[b][pos][h][d] = keys[b][s][h][d];
            this.cache.v[b][pos][h][d] = values[b][s][h][d];
          }
        }
      }
    }
    
    this.currentLength = Math.max(this.currentLength, startPos + seqLen);
  }

  /**
   * Get cached keys and values
   * @param {number} startPos - Starting position
   * @param {number} length - Length to retrieve
   * @param {number} batchSize - Batch size
   * @returns {Object} Cached keys and values
   */
  get(startPos, length, batchSize) {
    if (batchSize > this.maxBatchSize) {
      throw new Error(`Batch size ${batchSize} exceeds maximum ${this.maxBatchSize}`);
    }
    
    const keys = new Array(batchSize);
    const values = new Array(batchSize);
    
    for (let b = 0; b < batchSize; b++) {
      keys[b] = new Array(length);
      values[b] = new Array(length);
      
      for (let s = 0; s < length; s++) {
        const pos = startPos + s;
        if (pos >= this.currentLength) {
          // Return zeros for positions beyond current length
          keys[b][s] = new Array(this.nHeads);
          values[b][s] = new Array(this.nHeads);
          
          for (let h = 0; h < this.nHeads; h++) {
            keys[b][s][h] = new Array(this.headDim).fill(0);
            values[b][s][h] = new Array(this.headDim).fill(0);
          }
        } else {
          keys[b][s] = new Array(this.nHeads);
          values[b][s] = new Array(this.nHeads);
          
          for (let h = 0; h < this.nHeads; h++) {
            keys[b][s][h] = [...this.cache.k[b][pos][h]];
            values[b][s][h] = [...this.cache.v[b][pos][h]];
          }
        }
      }
    }
    
    return { keys, values };
  }

  /**
   * Clear cache
   */
  clear() {
    this.cache = this._initializeCache();
    this.currentLength = 0;
    this.batchSize = 0;
  }

  /**
   * Clear cache for specific batch
   * @param {number} batchIndex - Batch index to clear
   */
  clearBatch(batchIndex) {
    if (batchIndex >= this.maxBatchSize) {
      throw new Error(`Batch index ${batchIndex} out of range`);
    }
    
    for (let s = 0; s < this.maxSeqLen; s++) {
      for (let h = 0; h < this.nHeads; h++) {
        for (let d = 0; d < this.headDim; d++) {
          this.cache.k[batchIndex][s][h][d] = 0;
          this.cache.v[batchIndex][s][h][d] = 0;
        }
      }
    }
  }

  /**
   * Get cache statistics
   * @returns {Object} Cache statistics
   */
  getStats() {
    const totalElements = this.maxBatchSize * this.maxSeqLen * this.nHeads * this.headDim;
    const usedElements = this.batchSize * this.currentLength * this.nHeads * this.headDim;
    const memoryUsage = usedElements * this._getElementSize();
    
    return {
      maxBatchSize: this.maxBatchSize,
      maxSeqLen: this.maxSeqLen,
      nHeads: this.nHeads,
      headDim: this.headDim,
      currentBatchSize: this.batchSize,
      currentLength: this.currentLength,
      totalElements,
      usedElements,
      utilization: usedElements / totalElements,
      memoryUsage: memoryUsage,
      memoryUsageMB: memoryUsage / (1024 * 1024)
    };
  }

  /**
   * Get element size in bytes
   * @returns {number} Element size
   * @private
   */
  _getElementSize() {
    switch (this.dtype) {
      case 'float32':
        return 4;
      case 'float16':
        return 2;
      case 'int8':
        return 1;
      default:
        return 4;
    }
  }

  /**
   * Resize cache
   * @param {Object} newConfig - New configuration
   */
  resize(newConfig) {
    const { maxBatchSize, maxSeqLen, nHeads, headDim } = newConfig;
    
    if (maxBatchSize > this.maxBatchSize || 
        maxSeqLen > this.maxSeqLen || 
        nHeads > this.nHeads || 
        headDim > this.headDim) {
      
      this.config.maxBatchSize = Math.max(this.maxBatchSize, maxBatchSize);
      this.config.maxSeqLen = Math.max(this.maxSeqLen, maxSeqLen);
      this.config.nHeads = Math.max(this.nHeads, nHeads);
      this.config.headDim = Math.max(this.headDim, headDim);
      
      this.maxBatchSize = this.config.maxBatchSize;
      this.maxSeqLen = this.config.maxSeqLen;
      this.nHeads = this.config.nHeads;
      this.headDim = this.config.headDim;
      
      this.cache = this._initializeCache();
      this.currentLength = 0;
      this.batchSize = 0;
    }
  }

  /**
   * Get cache configuration
   * @returns {Object} Cache configuration
   */
  getConfig() {
    return {
      ...this.config,
      currentLength: this.currentLength,
      currentBatchSize: this.batchSize
    };
  }

  /**
   * Check if position is cached
   * @param {number} pos - Position to check
   * @returns {boolean} Is cached
   */
  isCached(pos) {
    return pos < this.currentLength;
  }

  /**
   * Get cached length
   * @returns {number} Cached length
   */
  getCachedLength() {
    return this.currentLength;
  }

  /**
   * Get memory usage report
   * @returns {Object} Memory usage report
   */
  getMemoryReport() {
    const stats = this.getStats();
    const elementSize = this._getElementSize();
    
    return {
      totalMemory: stats.totalElements * elementSize,
      usedMemory: stats.usedElements * elementSize,
      freeMemory: (stats.totalElements - stats.usedElements) * elementSize,
      utilization: stats.utilization,
      memoryPerHead: this.maxSeqLen * this.headDim * elementSize,
      memoryPerBatch: this.maxSeqLen * this.nHeads * this.headDim * elementSize,
      recommendations: this._getMemoryRecommendations(stats)
    };
  }

  /**
   * Get memory optimization recommendations
   * @param {Object} stats - Cache statistics
   * @returns {Array} Recommendations
   * @private
   */
  _getMemoryRecommendations(stats) {
    const recommendations = [];
    
    if (stats.utilization < 0.1) {
      recommendations.push('Cache utilization is low. Consider reducing maxBatchSize or maxSeqLen.');
    }
    
    if (stats.utilization > 0.9) {
      recommendations.push('Cache utilization is high. Consider increasing maxBatchSize or maxSeqLen.');
    }
    
    if (stats.memoryUsageMB > 1000) {
      recommendations.push('Cache memory usage is high. Consider using a smaller headDim or reducing maxSeqLen.');
    }
    
    if (this.currentLength > this.maxSeqLen * 0.8) {
      recommendations.push('Cache is nearly full. Consider clearing old entries or increasing maxSeqLen.');
    }
    
    return recommendations;
  }

  /**
   * Export cache data
   * @returns {Object} Cache data
   */
  export() {
    return {
      config: this.getConfig(),
      cache: this.cache,
      currentLength: this.currentLength,
      batchSize: this.batchSize
    };
  }

  /**
   * Import cache data
   * @param {Object} data - Cache data
   */
  import(data) {
    this.config = data.config;
    this.cache = data.cache;
    this.currentLength = data.currentLength;
    this.batchSize = data.batchSize;
    
    this.maxBatchSize = this.config.maxBatchSize;
    this.maxSeqLen = this.config.maxSeqLen;
    this.nHeads = this.config.nHeads;
    this.headDim = this.config.headDim;
  }
}

module.exports = KVCache;
