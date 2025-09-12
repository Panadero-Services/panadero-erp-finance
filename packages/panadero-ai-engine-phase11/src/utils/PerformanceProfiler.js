/**
 * PerformanceProfiler - Performance monitoring for mathematical operations
 * 
 * Tracks execution times and provides performance statistics
 * 
 * @author Panadero Services
 * @version 1.1.0
 */

class PerformanceProfiler {
  constructor() {
    this.stats = new Map();
    this.enabled = true;
  }

  /**
   * Record execution time for an operation
   * @param {string} operation - Operation name
   * @param {bigint} startTime - Start time from process.hrtime.bigint()
   * @param {number} iterations - Number of iterations (optional)
   */
  static record(operation, startTime, iterations = 1) {
    if (!this.instance) {
      this.instance = new PerformanceProfiler();
    }
    
    this.instance._record(operation, startTime, iterations);
  }

  /**
   * Record execution time for an operation (instance method)
   * @private
   */
  _record(operation, startTime, iterations = 1) {
    if (!this.enabled) return;
    
    const endTime = process.hrtime.bigint();
    const duration = Number(endTime - startTime) / 1000000; // Convert to milliseconds
    
    if (!this.stats.has(operation)) {
      this.stats.set(operation, {
        totalTime: 0,
        totalIterations: 0,
        minTime: Infinity,
        maxTime: -Infinity,
        callCount: 0
      });
    }
    
    const stat = this.stats.get(operation);
    stat.totalTime += duration;
    stat.totalIterations += iterations;
    stat.minTime = Math.min(stat.minTime, duration);
    stat.maxTime = Math.max(stat.maxTime, duration);
    stat.callCount++;
  }

  /**
   * Get performance statistics for an operation
   * @param {string} operation - Operation name
   * @returns {Object|null} Performance statistics or null if not found
   */
  getStats(operation) {
    return this.stats.get(operation) || null;
  }

  /**
   * Get all performance statistics
   * @returns {Object} All performance statistics
   */
  getAllStats() {
    const result = {};
    for (const [operation, stats] of this.stats) {
      result[operation] = {
        ...stats,
        averageTime: stats.totalTime / stats.callCount,
        averageIterations: stats.totalIterations / stats.callCount,
        operationsPerSecond: stats.totalIterations / (stats.totalTime / 1000)
      };
    }
    return result;
  }

  /**
   * Get performance summary
   * @returns {Object} Performance summary
   */
  getSummary() {
    const allStats = this.getAllStats();
    const operations = Object.keys(allStats);
    
    if (operations.length === 0) {
      return {
        totalOperations: 0,
        totalTime: 0,
        averageTime: 0,
        slowestOperation: null,
        fastestOperation: null
      };
    }
    
    let totalTime = 0;
    let slowestOperation = null;
    let fastestOperation = null;
    let slowestTime = -Infinity;
    let fastestTime = Infinity;
    
    for (const [operation, stats] of Object.entries(allStats)) {
      totalTime += stats.totalTime;
      
      if (stats.averageTime > slowestTime) {
        slowestTime = stats.averageTime;
        slowestOperation = operation;
      }
      
      if (stats.averageTime < fastestTime) {
        fastestTime = stats.averageTime;
        fastestOperation = operation;
      }
    }
    
    return {
      totalOperations: operations.length,
      totalTime: totalTime,
      averageTime: totalTime / operations.length,
      slowestOperation: slowestOperation,
      fastestOperation: fastestOperation,
      operations: allStats
    };
  }

  /**
   * Reset all performance statistics
   */
  reset() {
    this.stats.clear();
  }

  /**
   * Enable performance profiling
   */
  enable() {
    this.enabled = true;
  }

  /**
   * Disable performance profiling
   */
  disable() {
    this.enabled = false;
  }

  /**
   * Check if profiling is enabled
   * @returns {boolean} True if enabled
   */
  isEnabled() {
    return this.enabled;
  }

  /**
   * Export performance data to JSON
   * @returns {string} JSON string
   */
  exportToJSON() {
    return JSON.stringify(this.getAllStats(), null, 2);
  }

  /**
   * Export performance data to CSV
   * @returns {string} CSV string
   */
  exportToCSV() {
    const allStats = this.getAllStats();
    const operations = Object.keys(allStats);
    
    if (operations.length === 0) {
      return 'Operation,Call Count,Total Time (ms),Average Time (ms),Min Time (ms),Max Time (ms),Operations/sec\n';
    }
    
    let csv = 'Operation,Call Count,Total Time (ms),Average Time (ms),Min Time (ms),Max Time (ms),Operations/sec\n';
    
    for (const operation of operations) {
      const stats = allStats[operation];
      csv += `${operation},${stats.callCount},${stats.totalTime.toFixed(6)},${stats.averageTime.toFixed(6)},${stats.minTime.toFixed(6)},${stats.maxTime.toFixed(6)},${stats.operationsPerSecond.toFixed(2)}\n`;
    }
    
    return csv;
  }

  /**
   * Print performance report to console
   */
  printReport() {
    const summary = this.getSummary();
    
    console.log('\n=== Performance Report ===');
    console.log(`Total Operations: ${summary.totalOperations}`);
    console.log(`Total Time: ${summary.totalTime.toFixed(6)} ms`);
    console.log(`Average Time: ${summary.averageTime.toFixed(6)} ms`);
    console.log(`Slowest Operation: ${summary.slowestOperation} (${summary.operations[summary.slowestOperation]?.averageTime.toFixed(6)} ms)`);
    console.log(`Fastest Operation: ${summary.fastestOperation} (${summary.operations[summary.fastestOperation]?.averageTime.toFixed(6)} ms)`);
    
    console.log('\n--- Detailed Statistics ---');
    for (const [operation, stats] of Object.entries(summary.operations)) {
      console.log(`\n${operation}:`);
      console.log(`  Call Count: ${stats.callCount}`);
      console.log(`  Total Time: ${stats.totalTime.toFixed(6)} ms`);
      console.log(`  Average Time: ${stats.averageTime.toFixed(6)} ms`);
      console.log(`  Min Time: ${stats.minTime.toFixed(6)} ms`);
      console.log(`  Max Time: ${stats.maxTime.toFixed(6)} ms`);
      console.log(`  Operations/sec: ${stats.operationsPerSecond.toFixed(2)}`);
    }
    console.log('\n========================\n');
  }

  /**
   * Get static instance
   * @returns {PerformanceProfiler} Static instance
   */
  static getInstance() {
    if (!this.instance) {
      this.instance = new PerformanceProfiler();
    }
    return this.instance;
  }

  /**
   * Get static stats
   * @returns {Object} Static stats
   */
  static getStats() {
    return this.getInstance().getAllStats();
  }

  /**
   * Reset static stats
   */
  static reset() {
    this.getInstance().reset();
  }
}

module.exports = PerformanceProfiler;
