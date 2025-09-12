/**
 * ComplexNumber Class - Phase 11 Complex Number Operations
 * 
 * Represents complex numbers with comprehensive mathematical operations
 * 
 * @author Panadero Services
 * @version 1.1.0
 */

const MathValidation = require('../utils/MathValidation');

class ComplexNumber {
  /**
   * Create a complex number
   * @param {number} real - Real part
   * @param {number} imag - Imaginary part
   */
  constructor(real = 0, imag = 0) {
    this.validator = new MathValidation();
    this.validator.validateNumber(real);
    this.validator.validateNumber(imag);
    
    this.real = real;
    this.imag = imag;
  }

  /**
   * Create ComplexNumber from various input types
   * @param {ComplexNumber|Object|Array|number} input - Input data
   * @returns {ComplexNumber} Complex number instance
   */
  static from(input) {
    if (input instanceof ComplexNumber) {
      return input;
    }
    
    if (typeof input === 'number') {
      return new ComplexNumber(input, 0);
    }
    
    if (Array.isArray(input) && input.length >= 2) {
      return new ComplexNumber(input[0], input[1]);
    }
    
    if (typeof input === 'object' && input !== null) {
      if ('real' in input && 'imag' in input) {
        return new ComplexNumber(input.real, input.imag);
      }
      if ('r' in input && 'i' in input) {
        return new ComplexNumber(input.r, input.i);
      }
    }
    
    throw new Error(`Cannot create ComplexNumber from: ${typeof input}`);
  }

  /**
   * Add another complex number
   * @param {ComplexNumber|Object|number} other - Other complex number
   * @returns {ComplexNumber} Sum
   */
  add(other) {
    const c = ComplexNumber.from(other);
    return new ComplexNumber(this.real + c.real, this.imag + c.imag);
  }

  /**
   * Subtract another complex number
   * @param {ComplexNumber|Object|number} other - Other complex number
   * @returns {ComplexNumber} Difference
   */
  subtract(other) {
    const c = ComplexNumber.from(other);
    return new ComplexNumber(this.real - c.real, this.imag - c.imag);
  }

  /**
   * Multiply by another complex number
   * @param {ComplexNumber|Object|number} other - Other complex number
   * @returns {ComplexNumber} Product
   */
  multiply(other) {
    const c = ComplexNumber.from(other);
    const real = this.real * c.real - this.imag * c.imag;
    const imag = this.real * c.imag + this.imag * c.real;
    return new ComplexNumber(real, imag);
  }

  /**
   * Divide by another complex number
   * @param {ComplexNumber|Object|number} other - Other complex number
   * @returns {ComplexNumber} Quotient
   */
  divide(other) {
    const c = ComplexNumber.from(other);
    const denominator = c.real * c.real + c.imag * c.imag;
    
    if (denominator === 0) {
      throw new Error('Division by zero complex number');
    }
    
    const real = (this.real * c.real + this.imag * c.imag) / denominator;
    const imag = (this.imag * c.real - this.real * c.imag) / denominator;
    
    return new ComplexNumber(real, imag);
  }

  /**
   * Calculate magnitude (absolute value)
   * @returns {number} Magnitude
   */
  magnitude() {
    return Math.sqrt(this.real * this.real + this.imag * this.imag);
  }

  /**
   * Calculate argument (phase angle)
   * @returns {number} Argument in radians
   */
  argument() {
    return Math.atan2(this.imag, this.real);
  }

  /**
   * Calculate conjugate
   * @returns {ComplexNumber} Conjugate
   */
  conjugate() {
    return new ComplexNumber(this.real, -this.imag);
  }

  /**
   * Calculate reciprocal
   * @returns {ComplexNumber} Reciprocal
   */
  reciprocal() {
    const mag = this.magnitude();
    if (mag === 0) {
      throw new Error('Cannot calculate reciprocal of zero complex number');
    }
    
    return this.conjugate().multiply(1 / (mag * mag));
  }

  /**
   * Raise to a power
   * @param {number} n - Power exponent
   * @returns {ComplexNumber} Result
   */
  pow(n) {
    if (Number.isInteger(n)) {
      return this._integerPower(n);
    } else {
      return this._fractionalPower(n);
    }
  }

  /**
   * Integer power using binary exponentiation
   * @private
   */
  _integerPower(n) {
    if (n === 0) return new ComplexNumber(1, 0);
    if (n === 1) return this;
    if (n < 0) return this.reciprocal()._integerPower(-n);
    
    let result = new ComplexNumber(1, 0);
    let base = this;
    
    while (n > 0) {
      if (n % 2 === 1) {
        result = result.multiply(base);
      }
      base = base.multiply(base);
      n = Math.floor(n / 2);
    }
    
    return result;
  }

  /**
   * Fractional power using polar form
   * @private
   */
  _fractionalPower(n) {
    const r = this.magnitude();
    const theta = this.argument();
    
    if (r === 0) {
      return new ComplexNumber(0, 0);
    }
    
    const newR = Math.pow(r, n);
    const newTheta = theta * n;
    
    return new ComplexNumber(
      newR * Math.cos(newTheta),
      newR * Math.sin(newTheta)
    );
  }

  /**
   * Calculate square root
   * @returns {ComplexNumber} Square root
   */
  sqrt() {
    return this.pow(0.5);
  }

  /**
   * Calculate exponential
   * @returns {ComplexNumber} e^z
   */
  exp() {
    const eReal = Math.exp(this.real);
    return new ComplexNumber(
      eReal * Math.cos(this.imag),
      eReal * Math.sin(this.imag)
    );
  }

  /**
   * Calculate natural logarithm
   * @returns {ComplexNumber} ln(z)
   */
  log() {
    const r = this.magnitude();
    const theta = this.argument();
    
    if (r === 0) {
      throw new Error('Cannot calculate logarithm of zero complex number');
    }
    
    return new ComplexNumber(Math.log(r), theta);
  }

  /**
   * Calculate sine
   * @returns {ComplexNumber} sin(z)
   */
  sin() {
    const eiz = this.multiply(new ComplexNumber(0, 1)).exp();
    const eNegiz = this.multiply(new ComplexNumber(0, -1)).exp();
    return eiz.subtract(eNegiz).divide(new ComplexNumber(0, 2));
  }

  /**
   * Calculate cosine
   * @returns {ComplexNumber} cos(z)
   */
  cos() {
    const eiz = this.multiply(new ComplexNumber(0, 1)).exp();
    const eNegiz = this.multiply(new ComplexNumber(0, -1)).exp();
    return eiz.add(eNegiz).divide(2);
  }

  /**
   * Check if equal to another complex number
   * @param {ComplexNumber|Object|number} other - Other complex number
   * @param {number} tolerance - Tolerance for comparison
   * @returns {boolean} True if equal
   */
  equals(other, tolerance = 1e-10) {
    const c = ComplexNumber.from(other);
    const realDiff = Math.abs(this.real - c.real);
    const imagDiff = Math.abs(this.imag - c.imag);
    
    return realDiff < tolerance && imagDiff < tolerance;
  }

  /**
   * Convert to string representation
   * @returns {string} String representation
   */
  toString() {
    if (this.imag === 0) {
      return this.real.toString();
    }
    
    if (this.real === 0) {
      return `${this.imag}i`;
    }
    
    const imagStr = this.imag > 0 ? `+${this.imag}i` : `${this.imag}i`;
    return `${this.real}${imagStr}`;
  }

  /**
   * Convert to array [real, imag]
   * @returns {Array} Array representation
   */
  toArray() {
    return [this.real, this.imag];
  }

  /**
   * Convert to object {real, imag}
   * @returns {Object} Object representation
   */
  toObject() {
    return { real: this.real, imag: this.imag };
  }

  /**
   * Create from polar coordinates
   * @param {number} r - Magnitude
   * @param {number} theta - Argument in radians
   * @returns {ComplexNumber} Complex number
   */
  static fromPolar(r, theta) {
    return new ComplexNumber(
      r * Math.cos(theta),
      r * Math.sin(theta)
    );
  }

  /**
   * Create zero complex number
   * @returns {ComplexNumber} Zero
   */
  static zero() {
    return new ComplexNumber(0, 0);
  }

  /**
   * Create unit complex number
   * @returns {ComplexNumber} Unit
   */
  static one() {
    return new ComplexNumber(1, 0);
  }

  /**
   * Create imaginary unit
   * @returns {ComplexNumber} Imaginary unit
   */
  static i() {
    return new ComplexNumber(0, 1);
  }
}

module.exports = ComplexNumber;
