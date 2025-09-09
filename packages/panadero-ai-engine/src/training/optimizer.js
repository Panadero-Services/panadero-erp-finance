/**
 * Optimizer - Neural network optimization algorithms
 * 
 * Implements various optimization algorithms including AdamW, SGD,
 * and learning rate scheduling for training neural networks
 */

const MathUtils = require('../core/math');

class Optimizer {
  constructor(config) {
    this.config = {
      algorithm: config.algorithm || 'adamw',
      learningRate: config.learningRate || 0.001,
      weightDecay: config.weightDecay || 0.01,
      beta1: config.beta1 || 0.9,
      beta2: config.beta2 || 0.999,
      epsilon: config.epsilon || 1e-8,
      momentum: config.momentum || 0.9,
      ...config
    };
    
    this.algorithm = this.config.algorithm;
    this.learningRate = this.config.learningRate;
    this.weightDecay = this.config.weightDecay;
    
    // Initialize optimizer state
    this.state = new Map();
    this.stepCount = 0;
  }

  /**
   * Update parameters using gradients
   * @param {Object} parameters - Model parameters
   * @param {Object} gradients - Parameter gradients
   * @returns {Object} Updated parameters
   */
  update(parameters, gradients) {
    this.stepCount++;
    
    switch (this.algorithm) {
      case 'adamw':
        return this._adamWUpdate(parameters, gradients);
      case 'adam':
        return this._adamUpdate(parameters, gradients);
      case 'sgd':
        return this._sgdUpdate(parameters, gradients);
      case 'rmsprop':
        return this._rmspropUpdate(parameters, gradients);
      default:
        throw new Error(`Unknown optimizer algorithm: ${this.algorithm}`);
    }
  }

  /**
   * AdamW update
   * @param {Object} parameters - Model parameters
   * @param {Object} gradients - Parameter gradients
   * @returns {Object} Updated parameters
   * @private
   */
  _adamWUpdate(parameters, gradients) {
    const { learningRate, weightDecay, beta1, beta2, epsilon } = this.config;
    const updated = {};
    
    for (const [name, param] of Object.entries(parameters)) {
      if (!this.state.has(name)) {
        this.state.set(name, {
          m: new Array(param.length).fill(0),
          v: new Array(param.length).fill(0)
        });
      }
      
      const state = this.state.get(name);
      const grad = gradients[name];
      
      // Update biased first moment estimate
      for (let i = 0; i < param.length; i++) {
        state.m[i] = beta1 * state.m[i] + (1 - beta1) * grad[i];
      }
      
      // Update biased second raw moment estimate
      for (let i = 0; i < param.length; i++) {
        state.v[i] = beta2 * state.v[i] + (1 - beta2) * grad[i] * grad[i];
      }
      
      // Bias correction
      const mHat = state.m.map(m => m / (1 - Math.pow(beta1, this.stepCount)));
      const vHat = state.v.map(v => v / (1 - Math.pow(beta2, this.stepCount)));
      
      // Update parameters
      updated[name] = [];
      for (let i = 0; i < param.length; i++) {
        const update = learningRate * mHat[i] / (Math.sqrt(vHat[i]) + epsilon);
        updated[name][i] = param[i] - update - learningRate * weightDecay * param[i];
      }
    }
    
    return updated;
  }

  /**
   * Adam update
   * @param {Object} parameters - Model parameters
   * @param {Object} gradients - Parameter gradients
   * @returns {Object} Updated parameters
   * @private
   */
  _adamUpdate(parameters, gradients) {
    const { learningRate, beta1, beta2, epsilon } = this.config;
    const updated = {};
    
    for (const [name, param] of Object.entries(parameters)) {
      if (!this.state.has(name)) {
        this.state.set(name, {
          m: new Array(param.length).fill(0),
          v: new Array(param.length).fill(0)
        });
      }
      
      const state = this.state.get(name);
      const grad = gradients[name];
      
      // Update biased first moment estimate
      for (let i = 0; i < param.length; i++) {
        state.m[i] = beta1 * state.m[i] + (1 - beta1) * grad[i];
      }
      
      // Update biased second raw moment estimate
      for (let i = 0; i < param.length; i++) {
        state.v[i] = beta2 * state.v[i] + (1 - beta2) * grad[i] * grad[i];
      }
      
      // Bias correction
      const mHat = state.m.map(m => m / (1 - Math.pow(beta1, this.stepCount)));
      const vHat = state.v.map(v => v / (1 - Math.pow(beta2, this.stepCount)));
      
      // Update parameters
      updated[name] = [];
      for (let i = 0; i < param.length; i++) {
        const update = learningRate * mHat[i] / (Math.sqrt(vHat[i]) + epsilon);
        updated[name][i] = param[i] - update;
      }
    }
    
    return updated;
  }

  /**
   * SGD update
   * @param {Object} parameters - Model parameters
   * @param {Object} gradients - Parameter gradients
   * @returns {Object} Updated parameters
   * @private
   */
  _sgdUpdate(parameters, gradients) {
    const { learningRate, momentum, weightDecay } = this.config;
    const updated = {};
    
    for (const [name, param] of Object.entries(parameters)) {
      if (!this.state.has(name)) {
        this.state.set(name, {
          velocity: new Array(param.length).fill(0)
        });
      }
      
      const state = this.state.get(name);
      const grad = gradients[name];
      
      // Update velocity
      for (let i = 0; i < param.length; i++) {
        state.velocity[i] = momentum * state.velocity[i] + grad[i];
      }
      
      // Update parameters
      updated[name] = [];
      for (let i = 0; i < param.length; i++) {
        const update = learningRate * state.velocity[i] + weightDecay * param[i];
        updated[name][i] = param[i] - update;
      }
    }
    
    return updated;
  }

  /**
   * RMSprop update
   * @param {Object} parameters - Model parameters
   * @param {Object} gradients - Parameter gradients
   * @returns {Object} Updated parameters
   * @private
   */
  _rmspropUpdate(parameters, gradients) {
    const { learningRate, beta2, epsilon, weightDecay } = this.config;
    const updated = {};
    
    for (const [name, param] of Object.entries(parameters)) {
      if (!this.state.has(name)) {
        this.state.set(name, {
          v: new Array(param.length).fill(0)
        });
      }
      
      const state = this.state.get(name);
      const grad = gradients[name];
      
      // Update squared gradient estimate
      for (let i = 0; i < param.length; i++) {
        state.v[i] = beta2 * state.v[i] + (1 - beta2) * grad[i] * grad[i];
      }
      
      // Update parameters
      updated[name] = [];
      for (let i = 0; i < param.length; i++) {
        const update = learningRate * grad[i] / (Math.sqrt(state.v[i]) + epsilon);
        updated[name][i] = param[i] - update - learningRate * weightDecay * param[i];
      }
    }
    
    return updated;
  }

  /**
   * Set learning rate
   * @param {number} lr - New learning rate
   */
  setLearningRate(lr) {
    this.learningRate = lr;
    this.config.learningRate = lr;
  }

  /**
   * Get learning rate
   * @returns {number} Current learning rate
   */
  getLearningRate() {
    return this.learningRate;
  }

  /**
   * Get optimizer state
   * @returns {Object} Optimizer state
   */
  getState() {
    return {
      algorithm: this.algorithm,
      learningRate: this.learningRate,
      stepCount: this.stepCount,
      state: Object.fromEntries(this.state)
    };
  }

  /**
   * Set optimizer state
   * @param {Object} state - Optimizer state
   */
  setState(state) {
    this.algorithm = state.algorithm;
    this.learningRate = state.learningRate;
    this.stepCount = state.stepCount;
    this.state = new Map(Object.entries(state.state));
  }

  /**
   * Reset optimizer state
   */
  reset() {
    this.state.clear();
    this.stepCount = 0;
  }

  /**
   * Get optimizer statistics
   * @returns {Object} Optimizer statistics
   */
  getStats() {
    return {
      algorithm: this.algorithm,
      learningRate: this.learningRate,
      stepCount: this.stepCount,
      parameterCount: this.state.size,
      config: this.config
    };
  }
}

/**
 * Learning Rate Scheduler
 */
class LearningRateScheduler {
  constructor(config) {
    this.config = {
      type: config.type || 'cosine',
      initialLR: config.initialLR || 0.001,
      minLR: config.minLR || 1e-6,
      maxEpochs: config.maxEpochs || 100,
      warmupEpochs: config.warmupEpochs || 10,
      decayRate: config.decayRate || 0.1,
      decaySteps: config.decaySteps || 30,
      ...config
    };
    
    this.type = this.config.type;
    this.initialLR = this.config.initialLR;
    this.minLR = this.config.minLR;
    this.maxEpochs = this.config.maxEpochs;
  }

  /**
   * Get learning rate for current step
   * @param {number} step - Current step
   * @returns {number} Learning rate
   */
  getLearningRate(step) {
    switch (this.type) {
      case 'cosine':
        return this._cosineSchedule(step);
      case 'linear':
        return this._linearSchedule(step);
      case 'exponential':
        return this._exponentialSchedule(step);
      case 'step':
        return this._stepSchedule(step);
      case 'warmup_cosine':
        return this._warmupCosineSchedule(step);
      default:
        return this.initialLR;
    }
  }

  /**
   * Cosine annealing schedule
   * @param {number} step - Current step
   * @returns {number} Learning rate
   * @private
   */
  _cosineSchedule(step) {
    const progress = Math.min(step / this.maxEpochs, 1);
    const cosine = 0.5 * (1 + Math.cos(Math.PI * progress));
    return this.minLR + (this.initialLR - this.minLR) * cosine;
  }

  /**
   * Linear decay schedule
   * @param {number} step - Current step
   * @returns {number} Learning rate
   * @private
   */
  _linearSchedule(step) {
    const progress = Math.min(step / this.maxEpochs, 1);
    return this.initialLR * (1 - progress) + this.minLR * progress;
  }

  /**
   * Exponential decay schedule
   * @param {number} step - Current step
   * @returns {number} Learning rate
   * @private
   */
  _exponentialSchedule(step) {
    const decayRate = this.config.decayRate;
    const decaySteps = this.config.decaySteps;
    const decay = Math.pow(decayRate, step / decaySteps);
    return Math.max(this.initialLR * decay, this.minLR);
  }

  /**
   * Step decay schedule
   * @param {number} step - Current step
   * @returns {number} Learning rate
   * @private
   */
  _stepSchedule(step) {
    const decayRate = this.config.decayRate;
    const decaySteps = this.config.decaySteps;
    const steps = Math.floor(step / decaySteps);
    return Math.max(this.initialLR * Math.pow(decayRate, steps), this.minLR);
  }

  /**
   * Warmup cosine schedule
   * @param {number} step - Current step
   * @returns {number} Learning rate
   * @private
   */
  _warmupCosineSchedule(step) {
    const warmupEpochs = this.config.warmupEpochs;
    
    if (step < warmupEpochs) {
      // Warmup phase
      return this.initialLR * (step / warmupEpochs);
    } else {
      // Cosine annealing phase
      const progress = (step - warmupEpochs) / (this.maxEpochs - warmupEpochs);
      const cosine = 0.5 * (1 + Math.cos(Math.PI * progress));
      return this.minLR + (this.initialLR - this.minLR) * cosine;
    }
  }

  /**
   * Get schedule configuration
   * @returns {Object} Schedule configuration
   */
  getConfig() {
    return {
      ...this.config,
      currentLR: this.getLearningRate(0)
    };
  }
}

module.exports = { Optimizer, LearningRateScheduler };
