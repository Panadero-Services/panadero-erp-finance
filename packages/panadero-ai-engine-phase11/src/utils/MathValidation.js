/**
 * MathValidation - Input validation for mathematical operations
 * 
 * Provides comprehensive validation for mathematical inputs
 * 
 * @author Panadero Services
 * @version 1.1.0
 */

class MathValidation {
  /**
   * Validate that input is a number
   * @param {*} value - Value to validate
   * @param {string} name - Parameter name for error messages
   * @throws {Error} If value is not a valid number
   */
  validateNumber(value, name = 'value') {
    if (typeof value !== 'number' || isNaN(value)) {
      throw new Error(`${name} must be a valid number, got: ${typeof value}`);
    }
    
    if (!isFinite(value)) {
      throw new Error(`${name} must be finite, got: ${value}`);
    }
  }

  /**
   * Validate that input is a positive number
   * @param {*} value - Value to validate
   * @param {string} name - Parameter name for error messages
   * @throws {Error} If value is not a positive number
   */
  validatePositiveNumber(value, name = 'value') {
    this.validateNumber(value, name);
    
    if (value <= 0) {
      throw new Error(`${name} must be positive, got: ${value}`);
    }
  }

  /**
   * Validate that input is a non-negative number
   * @param {*} value - Value to validate
   * @param {string} name - Parameter name for error messages
   * @throws {Error} If value is not a non-negative number
   */
  validateNonNegativeNumber(value, name = 'value') {
    this.validateNumber(value, name);
    
    if (value < 0) {
      throw new Error(`${name} must be non-negative, got: ${value}`);
    }
  }

  /**
   * Validate that input is an integer
   * @param {*} value - Value to validate
   * @param {string} name - Parameter name for error messages
   * @throws {Error} If value is not an integer
   */
  validateInteger(value, name = 'value') {
    this.validateNumber(value, name);
    
    if (!Number.isInteger(value)) {
      throw new Error(`${name} must be an integer, got: ${value}`);
    }
  }

  /**
   * Validate that input is a positive integer
   * @param {*} value - Value to validate
   * @param {string} name - Parameter name for error messages
   * @throws {Error} If value is not a positive integer
   */
  validatePositiveInteger(value, name = 'value') {
    this.validateInteger(value, name);
    
    if (value <= 0) {
      throw new Error(`${name} must be positive, got: ${value}`);
    }
  }

  /**
   * Validate that input is a function
   * @param {*} value - Value to validate
   * @param {string} name - Parameter name for error messages
   * @throws {Error} If value is not a function
   */
  validateFunction(value, name = 'function') {
    if (typeof value !== 'function') {
      throw new Error(`${name} must be a function, got: ${typeof value}`);
    }
  }

  /**
   * Validate that input is an array
   * @param {*} value - Value to validate
   * @param {string} name - Parameter name for error messages
   * @throws {Error} If value is not an array
   */
  validateArray(value, name = 'array') {
    if (!Array.isArray(value)) {
      throw new Error(`${name} must be an array, got: ${typeof value}`);
    }
  }

  /**
   * Validate that input is a non-empty array
   * @param {*} value - Value to validate
   * @param {string} name - Parameter name for error messages
   * @throws {Error} If value is not a non-empty array
   */
  validateNonEmptyArray(value, name = 'array') {
    this.validateArray(value, name);
    
    if (value.length === 0) {
      throw new Error(`${name} must not be empty`);
    }
  }

  /**
   * Validate that input is a numeric array
   * @param {*} value - Value to validate
   * @param {string} name - Parameter name for error messages
   * @throws {Error} If value is not a numeric array
   */
  validateNumericArray(value, name = 'array') {
    this.validateNonEmptyArray(value, name);
    
    for (let i = 0; i < value.length; i++) {
      this.validateNumber(value[i], `${name}[${i}]`);
    }
  }

  /**
   * Validate that input is a 2D numeric array (matrix)
   * @param {*} value - Value to validate
   * @param {string} name - Parameter name for error messages
   * @throws {Error} If value is not a 2D numeric array
   */
  validateMatrix(value, name = 'matrix') {
    this.validateArray(value, name);
    
    if (value.length === 0) {
      throw new Error(`${name} must not be empty`);
    }
    
    for (let i = 0; i < value.length; i++) {
      this.validateNumericArray(value[i], `${name}[${i}]`);
    }
  }

  /**
   * Validate that input is a square matrix
   * @param {*} value - Value to validate
   * @param {string} name - Parameter name for error messages
   * @throws {Error} If value is not a square matrix
   */
  validateSquareMatrix(value, name = 'matrix') {
    this.validateMatrix(value, name);
    
    const rows = value.length;
    for (let i = 0; i < rows; i++) {
      if (value[i].length !== rows) {
        throw new Error(`${name} must be square, got ${rows}x${value[i].length}`);
      }
    }
  }

  /**
   * Validate that input is within a range
   * @param {number} value - Value to validate
   * @param {number} min - Minimum value (inclusive)
   * @param {number} max - Maximum value (inclusive)
   * @param {string} name - Parameter name for error messages
   * @throws {Error} If value is not within range
   */
  validateRange(value, min, max, name = 'value') {
    this.validateNumber(value, name);
    
    if (value < min || value > max) {
      throw new Error(`${name} must be between ${min} and ${max}, got: ${value}`);
    }
  }

  /**
   * Validate that input is a valid probability (0-1)
   * @param {number} value - Value to validate
   * @param {string} name - Parameter name for error messages
   * @throws {Error} If value is not a valid probability
   */
  validateProbability(value, name = 'probability') {
    this.validateRange(value, 0, 1, name);
  }

  /**
   * Validate that input is a valid angle in radians
   * @param {number} value - Value to validate
   * @param {string} name - Parameter name for error messages
   * @throws {Error} If value is not a valid angle
   */
  validateAngle(value, name = 'angle') {
    this.validateNumber(value, name);
    
    if (value < -Math.PI || value > Math.PI) {
      throw new Error(`${name} must be between -π and π, got: ${value}`);
    }
  }

  /**
   * Validate that input is a valid step size for numerical methods
   * @param {number} value - Value to validate
   * @param {string} name - Parameter name for error messages
   * @throws {Error} If value is not a valid step size
   */
  validateStepSize(value, name = 'step size') {
    this.validatePositiveNumber(value, name);
    
    if (value > 1) {
      throw new Error(`${name} should typically be less than 1, got: ${value}`);
    }
  }

  /**
   * Validate that input is a valid tolerance for convergence
   * @param {number} value - Value to validate
   * @param {string} name - Parameter name for error messages
   * @throws {Error} If value is not a valid tolerance
   */
  validateTolerance(value, name = 'tolerance') {
    this.validatePositiveNumber(value, name);
    
    if (value >= 1) {
      throw new Error(`${name} should typically be less than 1, got: ${value}`);
    }
  }

  /**
   * Validate that input is a valid iteration count
   * @param {number} value - Value to validate
   * @param {string} name - Parameter name for error messages
   * @throws {Error} If value is not a valid iteration count
   */
  validateIterationCount(value, name = 'iteration count') {
    this.validatePositiveInteger(value, name);
    
    if (value > 1000000) {
      throw new Error(`${name} should typically be less than 1,000,000, got: ${value}`);
    }
  }
}

module.exports = MathValidation;
