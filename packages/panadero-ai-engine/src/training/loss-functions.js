/**
 * Loss Functions - Neural network loss functions
 * 
 * Implements various loss functions for training neural networks
 * including cross-entropy, MSE, and custom loss functions
 */

const MathUtils = require('../core/math');

class LossFunctions {
  /**
   * Cross-entropy loss
   * @param {Array} predictions - Predicted probabilities
   * @param {Array} targets - Target probabilities
   * @param {Object} options - Loss options
   * @returns {number} Cross-entropy loss
   */
  static crossEntropyLoss(predictions, targets, options = {}) {
    const { reduction = 'mean', ignoreIndex = -1, labelSmoothing = 0 } = options;
    
    let totalLoss = 0;
    let count = 0;
    
    for (let i = 0; i < predictions.length; i++) {
      if (ignoreIndex !== -1 && targets[i] === ignoreIndex) {
        continue;
      }
      
      const pred = Math.max(1e-15, Math.min(1 - 1e-15, predictions[i]));
      const target = targets[i];
      
      // Label smoothing
      const smoothedTarget = target * (1 - labelSmoothing) + labelSmoothing / predictions[i].length;
      
      totalLoss -= smoothedTarget * Math.log(pred);
      count++;
    }
    
    return reduction === 'mean' ? totalLoss / count : totalLoss;
  }

  /**
   * Binary cross-entropy loss
   * @param {Array} predictions - Predicted probabilities
   * @param {Array} targets - Target binary values
   * @param {Object} options - Loss options
   * @returns {number} Binary cross-entropy loss
   */
  static binaryCrossEntropyLoss(predictions, targets, options = {}) {
    const { reduction = 'mean', posWeight = 1.0 } = options;
    
    let totalLoss = 0;
    let count = 0;
    
    for (let i = 0; i < predictions.length; i++) {
      const pred = Math.max(1e-15, Math.min(1 - 1e-15, predictions[i]));
      const target = targets[i];
      
      const loss = -target * Math.log(pred) - (1 - target) * Math.log(1 - pred);
      totalLoss += loss * (target === 1 ? posWeight : 1);
      count++;
    }
    
    return reduction === 'mean' ? totalLoss / count : totalLoss;
  }

  /**
   * Mean squared error loss
   * @param {Array} predictions - Predicted values
   * @param {Array} targets - Target values
   * @param {Object} options - Loss options
   * @returns {number} MSE loss
   */
  static mseLoss(predictions, targets, options = {}) {
    const { reduction = 'mean' } = options;
    
    let totalLoss = 0;
    let count = 0;
    
    for (let i = 0; i < predictions.length; i++) {
      const diff = predictions[i] - targets[i];
      totalLoss += diff * diff;
      count++;
    }
    
    return reduction === 'mean' ? totalLoss / count : totalLoss;
  }

  /**
   * Mean absolute error loss
   * @param {Array} predictions - Predicted values
   * @param {Array} targets - Target values
   * @param {Object} options - Loss options
   * @returns {number} MAE loss
   */
  static maeLoss(predictions, targets, options = {}) {
    const { reduction = 'mean' } = options;
    
    let totalLoss = 0;
    let count = 0;
    
    for (let i = 0; i < predictions.length; i++) {
      totalLoss += Math.abs(predictions[i] - targets[i]);
      count++;
    }
    
    return reduction === 'mean' ? totalLoss / count : totalLoss;
  }

  /**
   * Huber loss (smooth L1)
   * @param {Array} predictions - Predicted values
   * @param {Array} targets - Target values
   * @param {Object} options - Loss options
   * @returns {number} Huber loss
   */
  static huberLoss(predictions, targets, options = {}) {
    const { delta = 1.0, reduction = 'mean' } = options;
    
    let totalLoss = 0;
    let count = 0;
    
    for (let i = 0; i < predictions.length; i++) {
      const diff = Math.abs(predictions[i] - targets[i]);
      
      if (diff <= delta) {
        totalLoss += 0.5 * diff * diff;
      } else {
        totalLoss += delta * (diff - 0.5 * delta);
      }
      count++;
    }
    
    return reduction === 'mean' ? totalLoss / count : totalLoss;
  }

  /**
   * Focal loss for imbalanced datasets
   * @param {Array} predictions - Predicted probabilities
   * @param {Array} targets - Target probabilities
   * @param {Object} options - Loss options
   * @returns {number} Focal loss
   */
  static focalLoss(predictions, targets, options = {}) {
    const { alpha = 1.0, gamma = 2.0, reduction = 'mean' } = options;
    
    let totalLoss = 0;
    let count = 0;
    
    for (let i = 0; i < predictions.length; i++) {
      const pred = Math.max(1e-15, Math.min(1 - 1e-15, predictions[i]));
      const target = targets[i];
      
      const ce = -target * Math.log(pred) - (1 - target) * Math.log(1 - pred);
      const pT = target * pred + (1 - target) * (1 - pred);
      const focal = alpha * Math.pow(1 - pT, gamma) * ce;
      
      totalLoss += focal;
      count++;
    }
    
    return reduction === 'mean' ? totalLoss / count : totalLoss;
  }

  /**
   * KL divergence loss
   * @param {Array} predictions - Predicted probabilities
   * @param {Array} targets - Target probabilities
   * @param {Object} options - Loss options
   * @returns {number} KL divergence loss
   */
  static klDivergenceLoss(predictions, targets, options = {}) {
    const { reduction = 'mean' } = options;
    
    let totalLoss = 0;
    let count = 0;
    
    for (let i = 0; i < predictions.length; i++) {
      const pred = Math.max(1e-15, Math.min(1 - 1e-15, predictions[i]));
      const target = Math.max(1e-15, Math.min(1 - 1e-15, targets[i]));
      
      totalLoss += target * Math.log(target / pred);
      count++;
    }
    
    return reduction === 'mean' ? totalLoss / count : totalLoss;
  }

  /**
   * Cosine similarity loss
   * @param {Array} predictions - Predicted vectors
   * @param {Array} targets - Target vectors
   * @param {Object} options - Loss options
   * @returns {number} Cosine similarity loss
   */
  static cosineSimilarityLoss(predictions, targets, options = {}) {
    const { reduction = 'mean' } = options;
    
    let totalLoss = 0;
    let count = 0;
    
    for (let i = 0; i < predictions.length; i++) {
      const pred = predictions[i];
      const target = targets[i];
      
      const cosineSim = MathUtils.cosineSimilarity(pred, target);
      totalLoss += 1 - cosineSim;
      count++;
    }
    
    return reduction === 'mean' ? totalLoss / count : totalLoss;
  }

  /**
   * Triplet loss for metric learning
   * @param {Array} anchors - Anchor embeddings
   * @param {Array} positives - Positive embeddings
   * @param {Array} negatives - Negative embeddings
   * @param {Object} options - Loss options
   * @returns {number} Triplet loss
   */
  static tripletLoss(anchors, positives, negatives, options = {}) {
    const { margin = 1.0, reduction = 'mean' } = options;
    
    let totalLoss = 0;
    let count = 0;
    
    for (let i = 0; i < anchors.length; i++) {
      const anchor = anchors[i];
      const positive = positives[i];
      const negative = negatives[i];
      
      const posDist = MathUtils.norm(anchor.map((a, idx) => a - positive[idx]));
      const negDist = MathUtils.norm(anchor.map((a, idx) => a - negative[idx]));
      
      const loss = Math.max(0, posDist - negDist + margin);
      totalLoss += loss;
      count++;
    }
    
    return reduction === 'mean' ? totalLoss / count : totalLoss;
  }

  /**
   * Contrastive loss for representation learning
   * @param {Array} embeddings - Input embeddings
   * @param {Array} labels - Labels
   * @param {Object} options - Loss options
   * @returns {number} Contrastive loss
   */
  static contrastiveLoss(embeddings, labels, options = {}) {
    const { margin = 1.0, temperature = 0.1, reduction = 'mean' } = options;
    
    let totalLoss = 0;
    let count = 0;
    
    for (let i = 0; i < embeddings.length; i++) {
      for (let j = i + 1; j < embeddings.length; j++) {
        const emb1 = embeddings[i];
        const emb2 = embeddings[j];
        const label1 = labels[i];
        const label2 = labels[j];
        
        const distance = MathUtils.norm(emb1.map((e, idx) => e - emb2[idx]));
        const isSame = label1 === label2;
        
        if (isSame) {
          totalLoss += distance * distance;
        } else {
          totalLoss += Math.max(0, margin - distance) * Math.max(0, margin - distance);
        }
        count++;
      }
    }
    
    return reduction === 'mean' ? totalLoss / count : totalLoss;
  }

  /**
   * Compute gradients for loss function
   * @param {string} lossType - Type of loss function
   * @param {Array} predictions - Predicted values
   * @param {Array} targets - Target values
   * @param {Object} options - Loss options
   * @returns {Array} Gradients
   */
  static computeGradients(lossType, predictions, targets, options = {}) {
    const epsilon = 1e-8;
    const gradients = [];
    
    for (let i = 0; i < predictions.length; i++) {
      const originalPred = predictions[i];
      const grad = [];
      
      for (let j = 0; j < originalPred.length; j++) {
        const predPlus = [...originalPred];
        const predMinus = [...originalPred];
        
        predPlus[j] += epsilon;
        predMinus[j] -= epsilon;
        
        const lossPlus = this._computeLoss(lossType, predPlus, targets[i], options);
        const lossMinus = this._computeLoss(lossType, predMinus, targets[i], options);
        
        grad[j] = (lossPlus - lossMinus) / (2 * epsilon);
      }
      
      gradients.push(grad);
    }
    
    return gradients;
  }

  /**
   * Compute loss for gradient calculation
   * @param {string} lossType - Type of loss function
   * @param {Array} predictions - Predicted values
   * @param {Array} targets - Target values
   * @param {Object} options - Loss options
   * @returns {number} Loss value
   * @private
   */
  static _computeLoss(lossType, predictions, targets, options) {
    switch (lossType) {
      case 'cross_entropy':
        return this.crossEntropyLoss(predictions, targets, options);
      case 'binary_cross_entropy':
        return this.binaryCrossEntropyLoss(predictions, targets, options);
      case 'mse':
        return this.mseLoss(predictions, targets, options);
      case 'mae':
        return this.maeLoss(predictions, targets, options);
      case 'huber':
        return this.huberLoss(predictions, targets, options);
      case 'focal':
        return this.focalLoss(predictions, targets, options);
      case 'kl_divergence':
        return this.klDivergenceLoss(predictions, targets, options);
      case 'cosine_similarity':
        return this.cosineSimilarityLoss(predictions, targets, options);
      default:
        return this.mseLoss(predictions, targets, options);
    }
  }

  /**
   * Get available loss functions
   * @returns {Array} List of available loss functions
   */
  static getAvailableLossFunctions() {
    return [
      'cross_entropy',
      'binary_cross_entropy',
      'mse',
      'mae',
      'huber',
      'focal',
      'kl_divergence',
      'cosine_similarity',
      'triplet',
      'contrastive'
    ];
  }

  /**
   * Get loss function information
   * @param {string} lossType - Type of loss function
   * @returns {Object} Loss function information
   */
  static getLossInfo(lossType) {
    const info = {
      cross_entropy: {
        name: 'Cross Entropy Loss',
        description: 'Standard loss for classification tasks',
        parameters: ['reduction', 'ignoreIndex', 'labelSmoothing']
      },
      binary_cross_entropy: {
        name: 'Binary Cross Entropy Loss',
        description: 'Loss for binary classification tasks',
        parameters: ['reduction', 'posWeight']
      },
      mse: {
        name: 'Mean Squared Error Loss',
        description: 'Standard loss for regression tasks',
        parameters: ['reduction']
      },
      mae: {
        name: 'Mean Absolute Error Loss',
        description: 'Robust loss for regression tasks',
        parameters: ['reduction']
      },
      huber: {
        name: 'Huber Loss',
        description: 'Smooth L1 loss, robust to outliers',
        parameters: ['delta', 'reduction']
      },
      focal: {
        name: 'Focal Loss',
        description: 'Loss for imbalanced datasets',
        parameters: ['alpha', 'gamma', 'reduction']
      },
      kl_divergence: {
        name: 'KL Divergence Loss',
        description: 'Loss for probability distribution matching',
        parameters: ['reduction']
      },
      cosine_similarity: {
        name: 'Cosine Similarity Loss',
        description: 'Loss for vector similarity tasks',
        parameters: ['reduction']
      },
      triplet: {
        name: 'Triplet Loss',
        description: 'Loss for metric learning',
        parameters: ['margin', 'reduction']
      },
      contrastive: {
        name: 'Contrastive Loss',
        description: 'Loss for representation learning',
        parameters: ['margin', 'temperature', 'reduction']
      }
    };
    
    return info[lossType] || null;
  }
}

module.exports = LossFunctions;
