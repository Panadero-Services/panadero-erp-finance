/**
 * Math Utilities - Core mathematical operations
 * 
 * Provides essential mathematical functions for neural network computations
 * Optimized for JavaScript performance
 */

class MathUtils {
  /**
   * Sigmoid activation function
   * @param {number} x - Input value
   * @returns {number} Sigmoid output
   */
  static sigmoid(x) {
    return 1 / (1 + Math.exp(-x));
  }

  /**
   * Tanh activation function
   * @param {number} x - Input value
   * @returns {number} Tanh output
   */
  static tanh(x) {
    return Math.tanh(x);
  }

  /**
   * ReLU activation function
   * @param {number} x - Input value
   * @returns {number} ReLU output
   */
  static relu(x) {
    return Math.max(0, x);
  }

  /**
   * GELU activation function
   * @param {number} x - Input value
   * @returns {number} GELU output
   */
  static gelu(x) {
    return 0.5 * x * (1 + Math.tanh(Math.sqrt(2 / Math.PI) * (x + 0.044715 * Math.pow(x, 3))));
  }

  /**
   * Swish activation function
   * @param {number} x - Input value
   * @returns {number} Swish output
   */
  static swish(x) {
    return x * this.sigmoid(x);
  }

  /**
   * Softmax function
   * @param {Array} x - Input array
   * @returns {Array} Softmax output
   */
  static softmax(x) {
    const max = Math.max(...x);
    const exp = x.map(val => Math.exp(val - max));
    const sum = exp.reduce((acc, val) => acc + val, 0);
    return exp.map(val => val / sum);
  }

  /**
   * Log softmax function
   * @param {Array} x - Input array
   * @returns {Array} Log softmax output
   */
  static logSoftmax(x) {
    const max = Math.max(...x);
    const exp = x.map(val => Math.exp(val - max));
    const sum = exp.reduce((acc, val) => acc + val, 0);
    return x.map(val => val - max - Math.log(sum));
  }

  /**
   * Mean of array
   * @param {Array} arr - Input array
   * @returns {number} Mean value
   */
  static mean(arr) {
    return arr.reduce((sum, val) => sum + val, 0) / arr.length;
  }

  /**
   * Variance of array
   * @param {Array} arr - Input array
   * @param {number} mean - Precomputed mean (optional)
   * @returns {number} Variance value
   */
  static variance(arr, mean = null) {
    const m = mean !== null ? mean : this.mean(arr);
    const squaredDiffs = arr.map(val => Math.pow(val - m, 2));
    return this.mean(squaredDiffs);
  }

  /**
   * Standard deviation of array
   * @param {Array} arr - Input array
   * @param {number} mean - Precomputed mean (optional)
   * @returns {number} Standard deviation value
   */
  static std(arr, mean = null) {
    return Math.sqrt(this.variance(arr, mean));
  }

  /**
   * Matrix multiplication
   * @param {Array} a - First matrix
   * @param {Array} b - Second matrix
   * @returns {Array} Result matrix
   */
  static matmul(a, b) {
    const rows = a.length;
    const cols = b[0].length;
    const inner = a[0].length;
    
    if (inner !== b.length) {
      throw new Error('Matrix dimensions incompatible for multiplication');
    }
    
    const result = [];
    for (let i = 0; i < rows; i++) {
      result[i] = [];
      for (let j = 0; j < cols; j++) {
        let sum = 0;
        for (let k = 0; k < inner; k++) {
          sum += a[i][k] * b[k][j];
        }
        result[i][j] = sum;
      }
    }
    
    return result;
  }

  /**
   * Matrix transpose
   * @param {Array} matrix - Input matrix
   * @returns {Array} Transposed matrix
   */
  static transpose(matrix) {
    const rows = matrix.length;
    const cols = matrix[0].length;
    const result = [];
    
    for (let j = 0; j < cols; j++) {
      result[j] = [];
      for (let i = 0; i < rows; i++) {
        result[j][i] = matrix[i][j];
      }
    }
    
    return result;
  }

  /**
   * Dot product of two vectors
   * @param {Array} a - First vector
   * @param {Array} b - Second vector
   * @returns {number} Dot product
   */
  static dot(a, b) {
    if (a.length !== b.length) {
      throw new Error('Vectors must have the same length for dot product');
    }
    
    let sum = 0;
    for (let i = 0; i < a.length; i++) {
      sum += a[i] * b[i];
    }
    
    return sum;
  }

  /**
   * Euclidean norm of vector
   * @param {Array} vector - Input vector
   * @returns {number} Euclidean norm
   */
  static norm(vector) {
    const sumSquares = vector.reduce((sum, val) => sum + val * val, 0);
    return Math.sqrt(sumSquares);
  }

  /**
   * Normalize vector to unit length
   * @param {Array} vector - Input vector
   * @returns {Array} Normalized vector
   */
  static normalize(vector) {
    const n = this.norm(vector);
    if (n === 0) return vector;
    return vector.map(val => val / n);
  }

  /**
   * Generate random number from normal distribution
   * @param {number} mean - Mean value
   * @param {number} std - Standard deviation
   * @returns {number} Random number
   */
  static randomNormal(mean = 0, std = 1) {
    // Box-Muller transformation
    const u1 = Math.random();
    const u2 = Math.random();
    const z0 = Math.sqrt(-2 * Math.log(u1)) * Math.cos(2 * Math.PI * u2);
    return z0 * std + mean;
  }

  /**
   * Generate random number from uniform distribution
   * @param {number} min - Minimum value
   * @param {number} max - Maximum value
   * @returns {number} Random number
   */
  static randomUniform(min = 0, max = 1) {
    return Math.random() * (max - min) + min;
  }

  /**
   * Xavier/Glorot initialization
   * @param {number} fanIn - Input size
   * @param {number} fanOut - Output size
   * @returns {number} Initialization value
   */
  static xavierInit(fanIn, fanOut) {
    const limit = Math.sqrt(6 / (fanIn + fanOut));
    return this.randomUniform(-limit, limit);
  }

  /**
   * He initialization
   * @param {number} fanIn - Input size
   * @returns {number} Initialization value
   */
  static heInit(fanIn) {
    const std = Math.sqrt(2 / fanIn);
    return this.randomNormal(0, std);
  }

  /**
   * Leaky ReLU activation function
   * @param {number} x - Input value
   * @param {number} alpha - Leaky slope (default: 0.01)
   * @returns {number} Leaky ReLU output
   */
  static leakyRelu(x, alpha = 0.01) {
    return x > 0 ? x : alpha * x;
  }

  /**
   * ELU activation function
   * @param {number} x - Input value
   * @param {number} alpha - ELU parameter (default: 1.0)
   * @returns {number} ELU output
   */
  static elu(x, alpha = 1.0) {
    return x > 0 ? x : alpha * (Math.exp(x) - 1);
  }

  /**
   * Mish activation function
   * @param {number} x - Input value
   * @returns {number} Mish output
   */
  static mish(x) {
    return x * Math.tanh(Math.log(1 + Math.exp(x)));
  }

  /**
   * Calculate cross-entropy loss
   * @param {Array} predictions - Predicted probabilities
   * @param {Array} targets - Target probabilities
   * @returns {number} Cross-entropy loss
   */
  static crossEntropyLoss(predictions, targets) {
    let loss = 0;
    for (let i = 0; i < predictions.length; i++) {
      if (targets[i] > 0) {
        loss -= targets[i] * Math.log(predictions[i] + 1e-15);
      }
    }
    return loss;
  }

  /**
   * Calculate mean squared error
   * @param {Array} predictions - Predicted values
   * @param {Array} targets - Target values
   * @returns {number} MSE loss
   */
  static mseLoss(predictions, targets) {
    let sum = 0;
    for (let i = 0; i < predictions.length; i++) {
      const diff = predictions[i] - targets[i];
      sum += diff * diff;
    }
    return sum / predictions.length;
  }

  /**
   * Calculate binary cross-entropy loss
   * @param {Array} predictions - Predicted probabilities
   * @param {Array} targets - Target binary values
   * @returns {number} Binary cross-entropy loss
   */
  static binaryCrossEntropyLoss(predictions, targets) {
    let loss = 0;
    for (let i = 0; i < predictions.length; i++) {
      const p = Math.max(1e-15, Math.min(1 - 1e-15, predictions[i]));
      loss -= targets[i] * Math.log(p) + (1 - targets[i]) * Math.log(1 - p);
    }
    return loss / predictions.length;
  }

  /**
   * Apply dropout to array
   * @param {Array} input - Input array
   * @param {number} rate - Dropout rate (0-1)
   * @param {boolean} training - Whether in training mode
   * @returns {Array} Output array
   */
  static dropout(input, rate, training = true) {
    if (!training || rate === 0) {
      return input;
    }
    
    const mask = input.map(() => Math.random() > rate ? 1 : 0);
    const scale = 1 / (1 - rate);
    
    return input.map((val, i) => val * mask[i] * scale);
  }

  /**
   * Calculate cosine similarity
   * @param {Array} a - First vector
   * @param {Array} b - Second vector
   * @returns {number} Cosine similarity
   */
  static cosineSimilarity(a, b) {
    const dotProduct = this.dot(a, b);
    const normA = this.norm(a);
    const normB = this.norm(b);
    
    if (normA === 0 || normB === 0) {
      return 0;
    }
    
    return dotProduct / (normA * normB);
  }

  /**
   * Calculate Pearson correlation coefficient
   * @param {Array} x - First array
   * @param {Array} y - Second array
   * @returns {number} Correlation coefficient
   */
  static correlation(x, y) {
    if (x.length !== y.length) {
      throw new Error('Arrays must have the same length');
    }
    
    const n = x.length;
    const meanX = this.mean(x);
    const meanY = this.mean(y);
    
    let numerator = 0;
    let sumXSquared = 0;
    let sumYSquared = 0;
    
    for (let i = 0; i < n; i++) {
      const diffX = x[i] - meanX;
      const diffY = y[i] - meanY;
      
      numerator += diffX * diffY;
      sumXSquared += diffX * diffX;
      sumYSquared += diffY * diffY;
    }
    
    const denominator = Math.sqrt(sumXSquared * sumYSquared);
    
    if (denominator === 0) {
      return 0;
    }
    
    return numerator / denominator;
  }
}

module.exports = MathUtils;
