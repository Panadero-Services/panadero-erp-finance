/**
 * Data Loader - Data loading and preprocessing utilities
 * 
 * Implements data loading, preprocessing, and batching for training
 * neural networks with support for various data formats
 */

const MathUtils = require('../core/math');

class DataLoader {
  constructor(config) {
    this.config = {
      batchSize: config.batchSize || 32,
      shuffle: config.shuffle !== false,
      dropLast: config.dropLast || false,
      numWorkers: config.numWorkers || 1,
      pinMemory: config.pinMemory || false,
      ...config
    };
    
    this.batchSize = this.config.batchSize;
    this.shuffle = this.config.shuffle;
    this.dropLast = this.config.dropLast;
    
    this.data = null;
    this.labels = null;
    this.indices = [];
    this.currentEpoch = 0;
    this.currentBatch = 0;
  }

  /**
   * Load data from various sources
   * @param {Array|Object} data - Input data
   * @param {Array} labels - Data labels
   * @param {Object} options - Loading options
   */
  loadData(data, labels = null, options = {}) {
    this.data = data;
    this.labels = labels;
    this.indices = Array.from({ length: data.length }, (_, i) => i);
    
    // Apply preprocessing
    if (options.preprocess) {
      this._preprocessData(options.preprocess);
    }
    
    // Shuffle if enabled
    if (this.shuffle) {
      this._shuffleIndices();
    }
    
    this.currentEpoch = 0;
    this.currentBatch = 0;
  }

  /**
   * Get next batch
   * @returns {Object|null} Next batch or null if epoch complete
   */
  nextBatch() {
    if (this.currentBatch * this.batchSize >= this.data.length) {
      return null;
    }
    
    const startIdx = this.currentBatch * this.batchSize;
    const endIdx = Math.min(startIdx + this.batchSize, this.data.length);
    
    // Check if we should drop the last incomplete batch
    if (this.dropLast && endIdx - startIdx < this.batchSize) {
      return null;
    }
    
    const batchIndices = this.indices.slice(startIdx, endIdx);
    const batchData = batchIndices.map(idx => this.data[idx]);
    const batchLabels = this.labels ? batchIndices.map(idx => this.labels[idx]) : null;
    
    this.currentBatch++;
    
    return {
      data: batchData,
      labels: batchLabels,
      indices: batchIndices,
      batchIndex: this.currentBatch - 1,
      epoch: this.currentEpoch
    };
  }

  /**
   * Reset data loader for new epoch
   */
  reset() {
    this.currentBatch = 0;
    this.currentEpoch++;
    
    if (this.shuffle) {
      this._shuffleIndices();
    }
  }

  /**
   * Get number of batches per epoch
   * @returns {number} Number of batches
   */
  getBatchCount() {
    const totalBatches = Math.ceil(this.data.length / this.batchSize);
    return this.dropLast ? Math.floor(this.data.length / this.batchSize) : totalBatches;
  }

  /**
   * Get total number of samples
   * @returns {number} Total samples
   */
  getTotalSamples() {
    return this.data ? this.data.length : 0;
  }

  /**
   * Shuffle indices
   * @private
   */
  _shuffleIndices() {
    for (let i = this.indices.length - 1; i > 0; i--) {
      const j = Math.floor(Math.random() * (i + 1));
      [this.indices[i], this.indices[j]] = [this.indices[j], this.indices[i]];
    }
  }

  /**
   * Preprocess data
   * @param {Object} preprocessConfig - Preprocessing configuration
   * @private
   */
  _preprocessData(preprocessConfig) {
    const { normalize, standardize, augment, tokenize } = preprocessConfig;
    
    if (normalize) {
      this._normalizeData(normalize);
    }
    
    if (standardize) {
      this._standardizeData(standardize);
    }
    
    if (augment) {
      this._augmentData(augment);
    }
    
    if (tokenize) {
      this._tokenizeData(tokenize);
    }
  }

  /**
   * Normalize data to [0, 1] range
   * @param {Object} config - Normalization config
   * @private
   */
  _normalizeData(config) {
    const { min = 0, max = 1 } = config;
    
    for (let i = 0; i < this.data.length; i++) {
      const sample = this.data[i];
      const sampleMin = Math.min(...sample);
      const sampleMax = Math.max(...sample);
      
      this.data[i] = sample.map(val => 
        min + (val - sampleMin) * (max - min) / (sampleMax - sampleMin)
      );
    }
  }

  /**
   * Standardize data to zero mean, unit variance
   * @param {Object} config - Standardization config
   * @private
   */
  _standardizeData(config) {
    const { featureWise = false } = config;
    
    if (featureWise) {
      // Standardize each feature across all samples
      const nFeatures = this.data[0].length;
      const means = new Array(nFeatures).fill(0);
      const stds = new Array(nFeatures).fill(0);
      
      // Calculate means
      for (let i = 0; i < this.data.length; i++) {
        for (let j = 0; j < nFeatures; j++) {
          means[j] += this.data[i][j];
        }
      }
      means.forEach((mean, i) => means[i] = mean / this.data.length);
      
      // Calculate standard deviations
      for (let i = 0; i < this.data.length; i++) {
        for (let j = 0; j < nFeatures; j++) {
          stds[j] += Math.pow(this.data[i][j] - means[j], 2);
        }
      }
      stds.forEach((std, i) => stds[i] = Math.sqrt(std / this.data.length));
      
      // Apply standardization
      for (let i = 0; i < this.data.length; i++) {
        for (let j = 0; j < nFeatures; j++) {
          this.data[i][j] = (this.data[i][j] - means[j]) / stds[j];
        }
      }
    } else {
      // Standardize each sample individually
      for (let i = 0; i < this.data.length; i++) {
        const sample = this.data[i];
        const mean = MathUtils.mean(sample);
        const std = MathUtils.std(sample, mean);
        
        this.data[i] = sample.map(val => (val - mean) / std);
      }
    }
  }

  /**
   * Augment data
   * @param {Object} config - Augmentation config
   * @private
   */
  _augmentData(config) {
    const { noise, rotation, flip, crop } = config;
    const augmentedData = [...this.data];
    const augmentedLabels = this.labels ? [...this.labels] : null;
    
    if (noise) {
      const { std = 0.1, probability = 0.5 } = noise;
      for (let i = 0; i < this.data.length; i++) {
        if (Math.random() < probability) {
          const noisySample = this.data[i].map(val => 
            val + MathUtils.randomNormal(0, std)
          );
          augmentedData.push(noisySample);
          if (augmentedLabels) {
            augmentedLabels.push(this.labels[i]);
          }
        }
      }
    }
    
    if (rotation) {
      const { probability = 0.5 } = rotation;
      for (let i = 0; i < this.data.length; i++) {
        if (Math.random() < probability) {
          const rotatedSample = this._rotateSample(this.data[i]);
          augmentedData.push(rotatedSample);
          if (augmentedLabels) {
            augmentedLabels.push(this.labels[i]);
          }
        }
      }
    }
    
    if (flip) {
      const { probability = 0.5 } = flip;
      for (let i = 0; i < this.data.length; i++) {
        if (Math.random() < probability) {
          const flippedSample = [...this.data[i]].reverse();
          augmentedData.push(flippedSample);
          if (augmentedLabels) {
            augmentedLabels.push(this.labels[i]);
          }
        }
      }
    }
    
    this.data = augmentedData;
    this.labels = augmentedLabels;
    this.indices = Array.from({ length: this.data.length }, (_, i) => i);
  }

  /**
   * Rotate sample (for 1D data, this means shifting)
   * @param {Array} sample - Input sample
   * @returns {Array} Rotated sample
   * @private
   */
  _rotateSample(sample) {
    const shift = Math.floor(Math.random() * sample.length);
    return [...sample.slice(shift), ...sample.slice(0, shift)];
  }

  /**
   * Tokenize text data
   * @param {Object} config - Tokenization config
   * @private
   */
  _tokenizeData(config) {
    const { vocabSize = 10000, maxLength = 100, padding = 'post' } = config;
    
    // Simple tokenization (in practice, you'd use a proper tokenizer)
    const tokenizedData = [];
    
    for (let i = 0; i < this.data.length; i++) {
      const text = this.data[i];
      const tokens = text.split(' ').map(word => 
        word.charCodeAt(0) % vocabSize
      );
      
      // Pad or truncate
      if (tokens.length < maxLength) {
        if (padding === 'post') {
          tokens.push(...new Array(maxLength - tokens.length).fill(0));
        } else {
          tokens.unshift(...new Array(maxLength - tokens.length).fill(0));
        }
      } else {
        tokens.splice(maxLength);
      }
      
      tokenizedData.push(tokens);
    }
    
    this.data = tokenizedData;
  }

  /**
   * Split data into train/validation sets
   * @param {number} validationSplit - Validation split ratio
   * @returns {Object} Train and validation data loaders
   */
  splitData(validationSplit = 0.2) {
    const splitIdx = Math.floor(this.data.length * (1 - validationSplit));
    
    const trainData = this.data.slice(0, splitIdx);
    const valData = this.data.slice(splitIdx);
    
    const trainLabels = this.labels ? this.labels.slice(0, splitIdx) : null;
    const valLabels = this.labels ? this.labels.slice(splitIdx) : null;
    
    const trainLoader = new DataLoader({
      ...this.config,
      batchSize: this.batchSize
    });
    trainLoader.loadData(trainData, trainLabels);
    
    const valLoader = new DataLoader({
      ...this.config,
      batchSize: this.batchSize,
      shuffle: false
    });
    valLoader.loadData(valData, valLabels);
    
    return { trainLoader, valLoader };
  }

  /**
   * Get data statistics
   * @returns {Object} Data statistics
   */
  getStats() {
    if (!this.data || this.data.length === 0) {
      return null;
    }
    
    const stats = {
      totalSamples: this.data.length,
      batchSize: this.batchSize,
      batchesPerEpoch: this.getBatchCount(),
      currentEpoch: this.currentEpoch,
      currentBatch: this.currentBatch,
      dataShape: this.data[0] ? this.data[0].length : 0
    };
    
    if (this.labels) {
      const uniqueLabels = [...new Set(this.labels)];
      stats.uniqueLabels = uniqueLabels.length;
      stats.labelDistribution = uniqueLabels.map(label => ({
        label,
        count: this.labels.filter(l => l === label).length
      }));
    }
    
    return stats;
  }

  /**
   * Create iterator for data loader
   * @returns {Object} Iterator
   */
  [Symbol.iterator]() {
    return {
      next: () => {
        const batch = this.nextBatch();
        if (batch) {
          return { value: batch, done: false };
        } else {
          this.reset();
          return { done: true };
        }
      }
    };
  }
}

/**
 * Text Data Loader - Specialized for text data
 */
class TextDataLoader extends DataLoader {
  constructor(config) {
    super(config);
    this.tokenizer = null;
    this.vocab = new Map();
    this.vocabSize = 0;
  }

  /**
   * Set tokenizer
   * @param {Object} tokenizer - Tokenizer object
   */
  setTokenizer(tokenizer) {
    this.tokenizer = tokenizer;
  }

  /**
   * Build vocabulary from text data
   * @param {Array} texts - Text data
   * @param {Object} options - Vocabulary options
   */
  buildVocabulary(texts, options = {}) {
    const { minFreq = 1, maxVocabSize = 10000 } = options;
    const wordCounts = new Map();
    
    // Count word frequencies
    for (const text of texts) {
      const words = text.toLowerCase().split(/\s+/);
      for (const word of words) {
        wordCounts.set(word, (wordCounts.get(word) || 0) + 1);
      }
    }
    
    // Filter by frequency and size
    const sortedWords = Array.from(wordCounts.entries())
      .filter(([word, count]) => count >= minFreq)
      .sort((a, b) => b[1] - a[1])
      .slice(0, maxVocabSize);
    
    // Build vocabulary
    this.vocab.set('<PAD>', 0);
    this.vocab.set('<UNK>', 1);
    this.vocab.set('<START>', 2);
    this.vocab.set('<END>', 3);
    
    for (let i = 0; i < sortedWords.length; i++) {
      this.vocab.set(sortedWords[i][0], i + 4);
    }
    
    this.vocabSize = this.vocab.size;
  }

  /**
   * Tokenize text
   * @param {string} text - Input text
   * @returns {Array} Token indices
   */
  tokenize(text) {
    const words = text.toLowerCase().split(/\s+/);
    return words.map(word => this.vocab.get(word) || this.vocab.get('<UNK>'));
  }

  /**
   * Detokenize tokens
   * @param {Array} tokens - Token indices
   * @returns {string} Text
   */
  detokenize(tokens) {
    const reverseVocab = new Map();
    for (const [word, idx] of this.vocab.entries()) {
      reverseVocab.set(idx, word);
    }
    
    return tokens.map(token => reverseVocab.get(token) || '<UNK>').join(' ');
  }
}

module.exports = { DataLoader, TextDataLoader };
