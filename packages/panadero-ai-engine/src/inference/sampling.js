/**
 * Sampling Strategies - Advanced sampling methods for text generation
 * 
 * Implements various sampling strategies including temperature scaling,
 * top-k, top-p, and custom sampling methods
 */

const MathUtils = require('../core/math');

class SamplingStrategies {
  /**
   * Temperature scaling
   * @param {Array} logits - Input logits
   * @param {number} temperature - Temperature value
   * @returns {Array} Scaled logits
   */
  static temperatureScaling(logits, temperature) {
    if (temperature === 1.0) {
      return logits;
    }
    
    return logits.map(logit => logit / temperature);
  }

  /**
   * Top-k sampling
   * @param {Array} logits - Input logits
   * @param {number} k - Top-k value
   * @returns {Array} Filtered logits
   */
  static topKSampling(logits, k) {
    if (k <= 0 || k >= logits.length) {
      return logits;
    }
    
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
   * Top-p (nucleus) sampling
   * @param {Array} logits - Input logits
   * @param {number} p - Top-p value
   * @returns {Array} Filtered logits
   */
  static topPSampling(logits, p) {
    if (p >= 1.0) {
      return logits;
    }
    
    // Sort by probability
    const probs = MathUtils.softmax(logits);
    const sorted = probs
      .map((prob, index) => ({ prob, index }))
      .sort((a, b) => b.prob - a.prob);
    
    // Find cutoff point
    let cumulativeProb = 0;
    const cutoff = sorted.findIndex(({ prob }) => {
      cumulativeProb += prob;
      return cumulativeProb >= p;
    });
    
    // Filter logits
    const filtered = new Array(logits.length).fill(-Infinity);
    for (let i = 0; i <= cutoff; i++) {
      filtered[sorted[i].index] = logits[sorted[i].index];
    }
    
    return filtered;
  }

  /**
   * Typical sampling
   * @param {Array} logits - Input logits
   * @param {number} typicalP - Typical-p value
   * @returns {Array} Filtered logits
   */
  static typicalSampling(logits, typicalP) {
    const probs = MathUtils.softmax(logits);
    const logProbs = probs.map(prob => Math.log(Math.max(1e-15, prob)));
    
    // Calculate entropy
    const entropy = -probs.reduce((sum, prob) => sum + prob * logProbs[probs.indexOf(prob)], 0);
    
    // Calculate typical set
    const typicalSet = probs.map((prob, index) => ({
      prob,
      index,
      typicality: Math.abs(logProbs[index] + entropy)
    }));
    
    // Sort by typicality
    typicalSet.sort((a, b) => a.typicality - b.typicality);
    
    // Find cutoff
    let cumulativeProb = 0;
    const cutoff = typicalSet.findIndex(({ prob }) => {
      cumulativeProb += prob;
      return cumulativeProb >= typicalP;
    });
    
    // Filter logits
    const filtered = new Array(logits.length).fill(-Infinity);
    for (let i = 0; i <= cutoff; i++) {
      filtered[typicalSet[i].index] = logits[typicalSet[i].index];
    }
    
    return filtered;
  }

  /**
   * Mirostat sampling
   * @param {Array} logits - Input logits
   * @param {number} tau - Target entropy
   * @param {number} eta - Learning rate
   * @param {number} mu - Running average
   * @returns {Object} Sampling result
   */
  static mirostatSampling(logits, tau, eta = 0.1, mu = 0.0) {
    const probs = MathUtils.softmax(logits);
    const logProbs = probs.map(prob => Math.log(Math.max(1e-15, prob)));
    
    // Calculate entropy
    const entropy = -probs.reduce((sum, prob) => sum + prob * logProbs[probs.indexOf(prob)], 0);
    
    // Calculate error
    const error = entropy - tau;
    
    // Update mu
    const newMu = mu + eta * error;
    
    // Find threshold
    const threshold = Math.exp(newMu);
    
    // Filter by threshold
    const filtered = logits.map(logit => 
      Math.exp(logit) >= threshold ? logit : -Infinity
    );
    
    return {
      filteredLogits: filtered,
      mu: newMu,
      entropy,
      error
    };
  }

  /**
   * Repetition penalty
   * @param {Array} logits - Input logits
   * @param {Array} previousTokens - Previous tokens
   * @param {number} penalty - Penalty value
   * @returns {Array} Adjusted logits
   */
  static repetitionPenalty(logits, previousTokens, penalty = 1.2) {
    const adjusted = [...logits];
    
    for (const token of previousTokens) {
      if (adjusted[token] > 0) {
        adjusted[token] = adjusted[token] / penalty;
      } else {
        adjusted[token] = adjusted[token] * penalty;
      }
    }
    
    return adjusted;
  }

  /**
   * Frequency penalty
   * @param {Array} logits - Input logits
   * @param {Array} previousTokens - Previous tokens
   * @param {number} penalty - Penalty value
   * @returns {Array} Adjusted logits
   */
  static frequencyPenalty(logits, previousTokens, penalty = 0.1) {
    const adjusted = [...logits];
    const tokenCounts = new Map();
    
    // Count token frequencies
    for (const token of previousTokens) {
      tokenCounts.set(token, (tokenCounts.get(token) || 0) + 1);
    }
    
    // Apply penalty
    for (let i = 0; i < adjusted.length; i++) {
      const count = tokenCounts.get(i) || 0;
      adjusted[i] = adjusted[i] - penalty * count;
    }
    
    return adjusted;
  }

  /**
   * Presence penalty
   * @param {Array} logits - Input logits
   * @param {Array} previousTokens - Previous tokens
   * @param {number} penalty - Penalty value
   * @returns {Array} Adjusted logits
   */
  static presencePenalty(logits, previousTokens, penalty = 0.1) {
    const adjusted = [...logits];
    const presentTokens = new Set(previousTokens);
    
    // Apply penalty to present tokens
    for (const token of presentTokens) {
      if (adjusted[token] > 0) {
        adjusted[token] = adjusted[token] - penalty;
      } else {
        adjusted[token] = adjusted[token] - penalty;
      }
    }
    
    return adjusted;
  }

  /**
   * Custom sampling with multiple strategies
   * @param {Array} logits - Input logits
   * @param {Object} config - Sampling configuration
   * @returns {Array} Filtered logits
   */
  static customSampling(logits, config) {
    let filtered = [...logits];
    
    // Apply temperature scaling
    if (config.temperature && config.temperature !== 1.0) {
      filtered = this.temperatureScaling(filtered, config.temperature);
    }
    
    // Apply repetition penalty
    if (config.repetitionPenalty && config.previousTokens) {
      filtered = this.repetitionPenalty(filtered, config.previousTokens, config.repetitionPenalty);
    }
    
    // Apply frequency penalty
    if (config.frequencyPenalty && config.previousTokens) {
      filtered = this.frequencyPenalty(filtered, config.previousTokens, config.frequencyPenalty);
    }
    
    // Apply presence penalty
    if (config.presencePenalty && config.previousTokens) {
      filtered = this.presencePenalty(filtered, config.previousTokens, config.presencePenalty);
    }
    
    // Apply top-k filtering
    if (config.topK && config.topK > 0) {
      filtered = this.topKSampling(filtered, config.topK);
    }
    
    // Apply top-p filtering
    if (config.topP && config.topP < 1.0) {
      filtered = this.topPSampling(filtered, config.topP);
    }
    
    // Apply typical sampling
    if (config.typicalP && config.typicalP < 1.0) {
      filtered = this.typicalSampling(filtered, config.typicalP);
    }
    
    return filtered;
  }

  /**
   * Sample token from logits
   * @param {Array} logits - Input logits
   * @param {Object} config - Sampling configuration
   * @returns {Object} Sampled token and probability
   */
  static sampleToken(logits, config = {}) {
    const {
      temperature = 1.0,
      topK = 0,
      topP = 1.0,
      typicalP = 1.0,
      previousTokens = [],
      repetitionPenalty = 1.0,
      frequencyPenalty = 0.0,
      presencePenalty = 0.0
    } = config;
    
    // Apply custom sampling
    const filtered = this.customSampling(logits, {
      temperature,
      topK,
      topP,
      typicalP,
      previousTokens,
      repetitionPenalty,
      frequencyPenalty,
      presencePenalty
    });
    
    // Convert to probabilities
    const probs = MathUtils.softmax(filtered);
    
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
      probability: probs[selectedToken],
      logProbability: Math.log(Math.max(1e-15, probs[selectedToken]))
    };
  }

  /**
   * Get sampling statistics
   * @param {Array} logits - Input logits
   * @param {Object} config - Sampling configuration
   * @returns {Object} Sampling statistics
   */
  static getSamplingStats(logits, config = {}) {
    const filtered = this.customSampling(logits, config);
    const probs = MathUtils.softmax(filtered);
    
    const stats = {
      originalLength: logits.length,
      filteredLength: filtered.filter(logit => logit !== -Infinity).length,
      maxProb: Math.max(...probs),
      minProb: Math.min(...probs.filter(prob => prob > 0)),
      entropy: -probs.reduce((sum, prob) => sum + (prob > 0 ? prob * Math.log(prob) : 0), 0),
      effectiveVocabSize: probs.filter(prob => prob > 0).length
    };
    
    return stats;
  }

  /**
   * Get available sampling strategies
   * @returns {Array} List of available strategies
   */
  static getAvailableStrategies() {
    return [
      'temperature_scaling',
      'top_k',
      'top_p',
      'typical_sampling',
      'mirostat',
      'repetition_penalty',
      'frequency_penalty',
      'presence_penalty',
      'custom_sampling'
    ];
  }

  /**
   * Get strategy information
   * @param {string} strategy - Strategy name
   * @returns {Object} Strategy information
   */
  static getStrategyInfo(strategy) {
    const info = {
      temperature_scaling: {
        name: 'Temperature Scaling',
        description: 'Controls randomness by scaling logits',
        parameters: ['temperature'],
        range: { temperature: [0.1, 2.0] }
      },
      top_k: {
        name: 'Top-K Sampling',
        description: 'Sample from top-k most likely tokens',
        parameters: ['k'],
        range: { k: [1, 1000] }
      },
      top_p: {
        name: 'Top-P (Nucleus) Sampling',
        description: 'Sample from smallest set with cumulative probability p',
        parameters: ['p'],
        range: { p: [0.1, 1.0] }
      },
      typical_sampling: {
        name: 'Typical Sampling',
        description: 'Sample from typical set based on entropy',
        parameters: ['typical_p'],
        range: { typical_p: [0.1, 1.0] }
      },
      mirostat: {
        name: 'Mirostat Sampling',
        description: 'Adaptive sampling to maintain target entropy',
        parameters: ['tau', 'eta', 'mu'],
        range: { tau: [1.0, 10.0], eta: [0.01, 1.0], mu: [0.0, 10.0] }
      },
      repetition_penalty: {
        name: 'Repetition Penalty',
        description: 'Penalize repeated tokens',
        parameters: ['penalty'],
        range: { penalty: [0.1, 2.0] }
      },
      frequency_penalty: {
        name: 'Frequency Penalty',
        description: 'Penalize tokens based on frequency',
        parameters: ['penalty'],
        range: { penalty: [0.0, 1.0] }
      },
      presence_penalty: {
        name: 'Presence Penalty',
        description: 'Penalize tokens that have appeared',
        parameters: ['penalty'],
        range: { penalty: [0.0, 1.0] }
      },
      custom_sampling: {
        name: 'Custom Sampling',
        description: 'Combine multiple sampling strategies',
        parameters: ['temperature', 'topK', 'topP', 'repetitionPenalty', 'frequencyPenalty', 'presencePenalty'],
        range: { temperature: [0.1, 2.0], topK: [1, 1000], topP: [0.1, 1.0] }
      }
    };
    
    return info[strategy] || null;
  }
}

module.exports = SamplingStrategies;
