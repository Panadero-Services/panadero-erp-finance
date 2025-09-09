/**
 * Memory Manager - Memory management for neural networks
 * 
 * Handles memory allocation, caching, and optimization for large models
 * Implements efficient memory strategies for JavaScript environments
 */

class MemoryManager {
  constructor(config = {}) {
    this.config = {
      maxMemory: config.maxMemory || 1024 * 1024 * 1024, // 1GB default
      cacheSize: config.cacheSize || 100,
      enableGC: config.enableGC !== false,
      ...config
    };
    
    this.memoryPool = new Map();
    this.cache = new Map();
    this.allocatedMemory = 0;
    this.memoryStats = {
      totalAllocations: 0,
      totalDeallocations: 0,
      peakMemory: 0,
      currentMemory: 0
    };
  }

  /**
   * Allocate memory for tensor
   * @param {Array} shape - Tensor shape
   * @param {string} dtype - Data type
   * @returns {Object} Memory allocation info
   */
  allocate(shape, dtype = 'float32') {
    const size = this._calculateSize(shape);
    const memoryId = this._generateId();
    
    // Check if we have enough memory
    if (this.allocatedMemory + size > this.config.maxMemory) {
      this._garbageCollect();
      
      if (this.allocatedMemory + size > this.config.maxMemory) {
        throw new Error('Insufficient memory for allocation');
      }
    }
    
    const allocation = {
      id: memoryId,
      shape: [...shape],
      dtype,
      size,
      data: this._createData(shape, dtype),
      timestamp: Date.now(),
      refCount: 1
    };
    
    this.memoryPool.set(memoryId, allocation);
    this.allocatedMemory += size;
    this.memoryStats.totalAllocations++;
    this.memoryStats.currentMemory = this.allocatedMemory;
    this.memoryStats.peakMemory = Math.max(this.memoryStats.peakMemory, this.allocatedMemory);
    
    return allocation;
  }

  /**
   * Deallocate memory
   * @param {string} memoryId - Memory ID
   */
  deallocate(memoryId) {
    const allocation = this.memoryPool.get(memoryId);
    if (!allocation) {
      return;
    }
    
    allocation.refCount--;
    
    if (allocation.refCount <= 0) {
      this.memoryPool.delete(memoryId);
      this.allocatedMemory -= allocation.size;
      this.memoryStats.totalDeallocations++;
      this.memoryStats.currentMemory = this.allocatedMemory;
    }
  }

  /**
   * Reference memory (increment ref count)
   * @param {string} memoryId - Memory ID
   */
  reference(memoryId) {
    const allocation = this.memoryPool.get(memoryId);
    if (allocation) {
      allocation.refCount++;
    }
  }

  /**
   * Get memory allocation
   * @param {string} memoryId - Memory ID
   * @returns {Object|null} Memory allocation
   */
  get(memoryId) {
    return this.memoryPool.get(memoryId) || null;
  }

  /**
   * Cache tensor data
   * @param {string} key - Cache key
   * @param {Object} data - Data to cache
   * @param {number} ttl - Time to live in milliseconds
   */
  cache(key, data, ttl = 300000) { // 5 minutes default
    if (this.cache.size >= this.config.cacheSize) {
      this._evictOldestCache();
    }
    
    this.cache.set(key, {
      data,
      timestamp: Date.now(),
      ttl
    });
  }

  /**
   * Get cached data
   * @param {string} key - Cache key
   * @returns {Object|null} Cached data
   */
  getCache(key) {
    const cached = this.cache.get(key);
    if (!cached) {
      return null;
    }
    
    if (Date.now() - cached.timestamp > cached.ttl) {
      this.cache.delete(key);
      return null;
    }
    
    return cached.data;
  }

  /**
   * Clear cache
   */
  clearCache() {
    this.cache.clear();
  }

  /**
   * Get memory statistics
   * @returns {Object} Memory statistics
   */
  getStats() {
    return {
      ...this.memoryStats,
      cacheSize: this.cache.size,
      poolSize: this.memoryPool.size,
      memoryUtilization: this.allocatedMemory / this.config.maxMemory
    };
  }

  /**
   * Force garbage collection
   */
  forceGC() {
    this._garbageCollect();
  }

  /**
   * Calculate size from shape
   * @param {Array} shape - Tensor shape
   * @returns {number} Size in bytes
   * @private
   */
  _calculateSize(shape) {
    const elements = shape.reduce((acc, dim) => acc * dim, 1);
    return elements * 4; // 4 bytes per float32
  }

  /**
   * Generate unique ID
   * @returns {string} Unique ID
   * @private
   */
  _generateId() {
    return `mem_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
  }

  /**
   * Create data array
   * @param {Array} shape - Tensor shape
   * @param {string} dtype - Data type
   * @returns {Array} Data array
   * @private
   */
  _createData(shape, dtype) {
    const size = this._calculateSize(shape) / 4; // Convert to element count
    const data = new Array(size);
    
    // Initialize with zeros
    for (let i = 0; i < size; i++) {
      data[i] = 0;
    }
    
    return data;
  }

  /**
   * Garbage collect unused memory
   * @private
   */
  _garbageCollect() {
    if (!this.config.enableGC) {
      return;
    }
    
    const now = Date.now();
    const maxAge = 300000; // 5 minutes
    
    for (const [id, allocation] of this.memoryPool.entries()) {
      if (allocation.refCount <= 0 || (now - allocation.timestamp) > maxAge) {
        this.memoryPool.delete(id);
        this.allocatedMemory -= allocation.size;
        this.memoryStats.totalDeallocations++;
      }
    }
    
    this.memoryStats.currentMemory = this.allocatedMemory;
  }

  /**
   * Evict oldest cache entry
   * @private
   */
  _evictOldestCache() {
    let oldestKey = null;
    let oldestTime = Infinity;
    
    for (const [key, cached] of this.cache.entries()) {
      if (cached.timestamp < oldestTime) {
        oldestTime = cached.timestamp;
        oldestKey = key;
      }
    }
    
    if (oldestKey) {
      this.cache.delete(oldestKey);
    }
  }

  /**
   * Create memory pool for batch operations
   * @param {number} poolSize - Pool size
   * @returns {Object} Memory pool
   */
  createPool(poolSize = 1000) {
    const pool = {
      allocations: [],
      currentIndex: 0,
      size: poolSize
    };
    
    // Pre-allocate memory
    for (let i = 0; i < poolSize; i++) {
      const allocation = this.allocate([1], 'float32');
      pool.allocations.push(allocation);
    }
    
    return pool;
  }

  /**
   * Get allocation from pool
   * @param {Object} pool - Memory pool
   * @param {Array} shape - Required shape
   * @returns {Object|null} Memory allocation
   */
  getFromPool(pool, shape) {
    if (pool.currentIndex >= pool.allocations.length) {
      return null;
    }
    
    const allocation = pool.allocations[pool.currentIndex];
    pool.currentIndex++;
    
    // Resize if needed
    if (allocation.shape[0] !== shape[0]) {
      allocation.shape = [...shape];
      allocation.data = this._createData(shape, allocation.dtype);
    }
    
    return allocation;
  }

  /**
   * Reset memory pool
   * @param {Object} pool - Memory pool
   */
  resetPool(pool) {
    pool.currentIndex = 0;
  }

  /**
   * Monitor memory usage
   * @param {Function} callback - Callback function
   * @param {number} interval - Monitoring interval in ms
   * @returns {number} Interval ID
   */
  monitor(callback, interval = 1000) {
    return setInterval(() => {
      const stats = this.getStats();
      callback(stats);
    }, interval);
  }

  /**
   * Cleanup all memory
   */
  cleanup() {
    this.memoryPool.clear();
    this.cache.clear();
    this.allocatedMemory = 0;
    this.memoryStats.currentMemory = 0;
  }

  /**
   * Get memory usage report
   * @returns {Object} Detailed memory report
   */
  getReport() {
    const stats = this.getStats();
    const allocations = Array.from(this.memoryPool.values());
    
    const report = {
      ...stats,
      allocations: allocations.map(alloc => ({
        id: alloc.id,
        shape: alloc.shape,
        size: alloc.size,
        refCount: alloc.refCount,
        age: Date.now() - alloc.timestamp
      })),
      recommendations: this._getRecommendations(stats)
    };
    
    return report;
  }

  /**
   * Get memory optimization recommendations
   * @param {Object} stats - Memory statistics
   * @returns {Array} Recommendations
   * @private
   */
  _getRecommendations(stats) {
    const recommendations = [];
    
    if (stats.memoryUtilization > 0.8) {
      recommendations.push('High memory usage detected. Consider reducing batch size or model size.');
    }
    
    if (stats.cacheSize > this.config.cacheSize * 0.9) {
      recommendations.push('Cache is nearly full. Consider clearing old cache entries.');
    }
    
    if (stats.poolSize > 1000) {
      recommendations.push('Large number of allocations. Consider using memory pooling.');
    }
    
    return recommendations;
  }
}

module.exports = MemoryManager;
