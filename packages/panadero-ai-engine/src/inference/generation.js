/**
 * Text Generator - Advanced text generation with multiple strategies
 * 
 * Implements various text generation strategies including greedy decoding,
 * beam search, and sampling methods for neural language models
 */

const MathUtils = require('../core/math');

class TextGenerator {
  constructor(config) {
    this.config = {
      maxLength: config.maxLength || 100,
      temperature: config.temperature || 0.7,
      topK: config.topK || 50,
      topP: config.topP || 0.9,
      repetitionPenalty: config.repetitionPenalty || 1.0,
      lengthPenalty: config.lengthPenalty || 1.0,
      earlyStopping: config.earlyStopping || false,
      ...config
    };
    
    this.maxLength = this.config.maxLength;
    this.temperature = this.config.temperature;
    this.topK = this.config.topK;
    this.topP = this.config.topP;
    this.repetitionPenalty = this.config.repetitionPenalty;
    this.lengthPenalty = this.config.lengthPenalty;
    this.earlyStopping = this.config.earlyStopping;
  }

  /**
   * Generate text using greedy decoding
   * @param {Function} model - Model forward function
   * @param {Array} prompt - Input prompt tokens
   * @param {Object} options - Generation options
   * @returns {Object} Generation result
   */
  greedyDecode(model, prompt, options = {}) {
    const config = { ...this.config, ...options };
    const generated = [...prompt];
    const logProbs = [];
    
    for (let i = 0; i < config.maxLength; i++) {
      const logits = model(generated);
      const nextToken = this._getGreedyToken(logits);
      
      generated.push(nextToken);
      logProbs.push(Math.log(Math.max(1e-15, MathUtils.softmax(logits)[nextToken])));
      
      if (this._shouldStop(generated, config)) {
        break;
      }
    }
    
    return {
      tokens: generated,
      logProbs,
      length: generated.length,
      method: 'greedy'
    };
  }

  /**
   * Generate text using beam search
   * @param {Function} model - Model forward function
   * @param {Array} prompt - Input prompt tokens
   * @param {Object} options - Generation options
   * @returns {Object} Generation result
   */
  beamSearch(model, prompt, options = {}) {
    const config = { ...this.config, ...options };
    const beamSize = config.beamSize || 4;
    
    let beams = [{
      tokens: [...prompt],
      logProb: 0,
      finished: false
    }];
    
    const finishedBeams = [];
    
    for (let step = 0; step < config.maxLength; step++) {
      const candidates = [];
      
      for (const beam of beams) {
        if (beam.finished) {
          candidates.push(beam);
          continue;
        }
        
        const logits = model(beam.tokens);
        const topTokens = this._getTopTokens(logits, beamSize * 2);
        
        for (const { token, logProb } of topTokens) {
          const newBeam = {
            tokens: [...beam.tokens, token],
            logProb: beam.logProb + logProb,
            finished: this._isFinished([...beam.tokens, token], config)
          };
          
          candidates.push(newBeam);
        }
      }
      
      // Sort by score and keep top beams
      candidates.sort((a, b) => this._beamScore(b) - this._beamScore(a));
      beams = candidates.slice(0, beamSize);
      
      // Move finished beams
      const activeBeams = [];
      for (const beam of beams) {
        if (beam.finished) {
          finishedBeams.push(beam);
        } else {
          activeBeams.push(beam);
        }
      }
      beams = activeBeams;
      
      if (beams.length === 0) {
        break;
      }
    }
    
    // Add remaining beams to finished
    finishedBeams.push(...beams);
    
    // Sort finished beams by score
    finishedBeams.sort((a, b) => this._beamScore(b) - this._beamScore(a));
    
    const bestBeam = finishedBeams[0];
    
    return {
      tokens: bestBeam.tokens,
      logProbs: this._calculateLogProbs(bestBeam.tokens, model),
      length: bestBeam.tokens.length,
      method: 'beam_search',
      beamSize,
      allBeams: finishedBeams.slice(0, beamSize)
    };
  }

  /**
   * Generate text using sampling
   * @param {Function} model - Model forward function
   * @param {Array} prompt - Input prompt tokens
   * @param {Object} options - Generation options
   * @returns {Object} Generation result
   */
  sample(model, prompt, options = {}) {
    const config = { ...this.config, ...options };
    const generated = [...prompt];
    const logProbs = [];
    
    for (let i = 0; i < config.maxLength; i++) {
      const logits = model(generated);
      const { token, logProb } = this._sampleToken(logits, config);
      
      generated.push(token);
      logProbs.push(logProb);
      
      if (this._shouldStop(generated, config)) {
        break;
      }
    }
    
    return {
      tokens: generated,
      logProbs,
      length: generated.length,
      method: 'sampling'
    };
  }

  /**
   * Generate text using nucleus sampling (top-p)
   * @param {Function} model - Model forward function
   * @param {Array} prompt - Input prompt tokens
   * @param {Object} options - Generation options
   * @returns {Object} Generation result
   */
  nucleusSample(model, prompt, options = {}) {
    const config = { ...this.config, ...options };
    const generated = [...prompt];
    const logProbs = [];
    
    for (let i = 0; i < config.maxLength; i++) {
      const logits = model(generated);
      const { token, logProb } = this._nucleusSample(logits, config);
      
      generated.push(token);
      logProbs.push(logProb);
      
      if (this._shouldStop(generated, config)) {
        break;
      }
    }
    
    return {
      tokens: generated,
      logProbs,
      length: generated.length,
      method: 'nucleus_sampling'
    };
  }

  /**
   * Get greedy token
   * @param {Array} logits - Model logits
   * @returns {number} Token index
   * @private
   */
  _getGreedyToken(logits) {
    let maxLogit = -Infinity;
    let maxIndex = 0;
    
    for (let i = 0; i < logits.length; i++) {
      if (logits[i] > maxLogit) {
        maxLogit = logits[i];
        maxIndex = i;
      }
    }
    
    return maxIndex;
  }

  /**
   * Get top-k tokens
   * @param {Array} logits - Model logits
   * @param {number} k - Number of top tokens
   * @returns {Array} Top tokens with probabilities
   * @private
   */
  _getTopTokens(logits, k) {
    const sorted = logits
      .map((logit, index) => ({ logit, index }))
      .sort((a, b) => b.logit - a.logit)
      .slice(0, k);
    
    const probs = MathUtils.softmax(logits);
    
    return sorted.map(({ logit, index }) => ({
      token: index,
      logProb: Math.log(Math.max(1e-15, probs[index]))
    }));
  }

  /**
   * Sample token from logits
   * @param {Array} logits - Model logits
   * @param {Object} config - Generation config
   * @returns {Object} Sampled token and log probability
   * @private
   */
  _sampleToken(logits, config) {
    const { temperature, topK } = config;
    
    // Apply temperature
    const scaledLogits = logits.map(logit => logit / temperature);
    
    // Apply top-k filtering
    const filteredLogits = this._applyTopK(scaledLogits, topK);
    
    // Convert to probabilities
    const probs = MathUtils.softmax(filteredLogits);
    
    // Sample
    const random = Math.random();
    let cumulativeProb = 0;
    let selectedToken = 0;
    
    for (let i = 0; i < probs.length; i++) {
      cumulativeProb += probs[i];
      if (random <= cumulativeProb) {
        selectedToken = i;
        break;
      }
    }
    
    return {
      token: selectedToken,
      logProb: Math.log(Math.max(1e-15, probs[selectedToken]))
    };
  }

  /**
   * Nucleus sampling (top-p)
   * @param {Array} logits - Model logits
   * @param {Object} config - Generation config
   * @returns {Object} Sampled token and log probability
   * @private
   */
  _nucleusSample(logits, config) {
    const { temperature, topP } = config;
    
    // Apply temperature
    const scaledLogits = logits.map(logit => logit / temperature);
    
    // Sort by probability
    const sorted = scaledLogits
      .map((logit, index) => ({ logit, index }))
      .sort((a, b) => b.logit - a.logit);
    
    const probs = MathUtils.softmax(scaledLogits);
    
    // Find cutoff point
    let cumulativeProb = 0;
    const cutoff = sorted.findIndex(({ index }) => {
      cumulativeProb += probs[index];
      return cumulativeProb >= topP;
    });
    
    // Filter logits
    const filteredLogits = new Array(logits.length).fill(-Infinity);
    for (let i = 0; i <= cutoff; i++) {
      filteredLogits[sorted[i].index] = sorted[i].logit;
    }
    
    // Sample from filtered distribution
    const filteredProbs = MathUtils.softmax(filteredLogits);
    const random = Math.random();
    let cumulative = 0;
    let selectedToken = 0;
    
    for (let i = 0; i < filteredProbs.length; i++) {
      cumulative += filteredProbs[i];
      if (random <= cumulative) {
        selectedToken = i;
        break;
      }
    }
    
    return {
      token: selectedToken,
      logProb: Math.log(Math.max(1e-15, filteredProbs[selectedToken]))
    };
  }

  /**
   * Apply top-k filtering
   * @param {Array} logits - Input logits
   * @param {number} k - Top-k value
   * @returns {Array} Filtered logits
   * @private
   */
  _applyTopK(logits, k) {
    const sorted = logits
      .map((logit, index) => ({ logit, index }))
      .sort((a, b) => b.logit - a.logit)
      .slice(0, k);
    
    const filtered = new Array(logits.length).fill(-Infinity);
    sorted.forEach(({ logit, index }) => {
      filtered[index] = logit;
    });
    
    return filtered;
  }

  /**
   * Calculate beam score
   * @param {Object} beam - Beam object
   * @returns {number} Beam score
   * @private
   */
  _beamScore(beam) {
    const lengthPenalty = Math.pow(beam.tokens.length, this.lengthPenalty);
    return beam.logProb / lengthPenalty;
  }

  /**
   * Check if generation should stop
   * @param {Array} tokens - Current tokens
   * @param {Object} config - Generation config
   * @returns {boolean} Should stop
   * @private
   */
  _shouldStop(tokens, config) {
    if (tokens.length >= config.maxLength) {
      return true;
    }
    
    if (config.stopTokens && config.stopTokens.length > 0) {
      const lastToken = tokens[tokens.length - 1];
      if (config.stopTokens.includes(lastToken)) {
        return true;
      }
    }
    
    if (config.earlyStopping && this._hasRepeatedSequence(tokens)) {
      return true;
    }
    
    return false;
  }

  /**
   * Check for repeated sequences
   * @param {Array} tokens - Current tokens
   * @returns {boolean} Has repeated sequence
   * @private
   */
  _hasRepeatedSequence(tokens) {
    const minLength = 3;
    const maxLength = Math.floor(tokens.length / 2);
    
    for (let length = minLength; length <= maxLength; length++) {
      const lastSequence = tokens.slice(-length);
      const beforeLast = tokens.slice(-length * 2, -length);
      
      if (this._arraysEqual(lastSequence, beforeLast)) {
        return true;
      }
    }
    
    return false;
  }

  /**
   * Check if two arrays are equal
   * @param {Array} a - First array
   * @param {Array} b - Second array
   * @returns {boolean} Are equal
   * @private
   */
  _arraysEqual(a, b) {
    if (a.length !== b.length) return false;
    for (let i = 0; i < a.length; i++) {
      if (a[i] !== b[i]) return false;
    }
    return true;
  }

  /**
   * Calculate log probabilities for tokens
   * @param {Array} tokens - Token sequence
   * @param {Function} model - Model function
   * @returns {Array} Log probabilities
   * @private
   */
  _calculateLogProbs(tokens, model) {
    const logProbs = [];
    
    for (let i = 1; i < tokens.length; i++) {
      const input = tokens.slice(0, i);
      const logits = model(input);
      const probs = MathUtils.softmax(logits);
      const targetToken = tokens[i];
      
      logProbs.push(Math.log(Math.max(1e-15, probs[targetToken])));
    }
    
    return logProbs;
  }

  /**
   * Check if generation is finished
   * @param {Array} tokens - Current tokens
   * @param {Object} config - Generation config
   * @returns {boolean} Is finished
   * @private
   */
  _isFinished(tokens, config) {
    return this._shouldStop(tokens, config);
  }

  /**
   * Get generation statistics
   * @returns {Object} Generation statistics
   */
  getStats() {
    return {
      maxLength: this.maxLength,
      temperature: this.temperature,
      topK: this.topK,
      topP: this.topP,
      repetitionPenalty: this.repetitionPenalty,
      lengthPenalty: this.lengthPenalty,
      earlyStopping: this.earlyStopping
    };
  }

  /**
   * Update generation configuration
   * @param {Object} newConfig - New configuration
   */
  updateConfig(newConfig) {
    Object.assign(this.config, newConfig);
    Object.assign(this, newConfig);
  }
}

module.exports = TextGenerator;
