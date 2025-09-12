/**
 * Enhanced MathUtils - Phase 11 Mathematical Operations
 * 
 * Extends the base MathUtils from @panadero/ai-engine v1.0.0
 * with advanced mathematical operations for Phase 11
 * 
 * @author Panadero Services
 * @version 1.1.0
 */

const { MathUtils: BaseMathUtils } = require('@panadero/ai-engine');
const ComplexNumber = require('../../classes/ComplexNumber');
const MathValidation = require('../../utils/MathValidation');
const PerformanceProfiler = require('../../utils/PerformanceProfiler');

class EnhancedMathUtils extends BaseMathUtils {
  constructor() {
    super();
    this.profiler = new PerformanceProfiler();
    this.validator = new MathValidation();
  }

  /**
   * Complex number operations
   */
  
  /**
   * Add two complex numbers
   * @param {ComplexNumber|Object} z1 - First complex number
   * @param {ComplexNumber|Object} z2 - Second complex number
   * @returns {ComplexNumber} Sum of complex numbers
   */
  static complexAdd(z1, z2) {
    const startTime = process.hrtime.bigint();
    
    try {
      const c1 = ComplexNumber.from(z1);
      const c2 = ComplexNumber.from(z2);
      
      const result = new ComplexNumber(
        c1.real + c2.real,
        c1.imag + c2.imag
      );
      
      PerformanceProfiler.record('complexAdd', startTime);
      return result;
    } catch (error) {
      throw new Error(`Complex addition failed: ${error.message}`);
    }
  }

  /**
   * Multiply two complex numbers
   * @param {ComplexNumber|Object} z1 - First complex number
   * @param {ComplexNumber|Object} z2 - Second complex number
   * @returns {ComplexNumber} Product of complex numbers
   */
  static complexMultiply(z1, z2) {
    const startTime = process.hrtime.bigint();
    
    try {
      const c1 = ComplexNumber.from(z1);
      const c2 = ComplexNumber.from(z2);
      
      const real = c1.real * c2.real - c1.imag * c2.imag;
      const imag = c1.real * c2.imag + c1.imag * c2.real;
      
      const result = new ComplexNumber(real, imag);
      
      PerformanceProfiler.record('complexMultiply', startTime);
      return result;
    } catch (error) {
      throw new Error(`Complex multiplication failed: ${error.message}`);
    }
  }

  /**
   * Complex power operation
   * @param {ComplexNumber|Object} z - Complex number
   * @param {number} n - Power exponent
   * @returns {ComplexNumber} Complex number raised to power n
   */
  static complexPower(z, n) {
    const startTime = process.hrtime.bigint();
    
    try {
      const c = ComplexNumber.from(z);
      
      if (Number.isInteger(n)) {
        return this._integerPower(c, n);
      } else {
        return this._fractionalPower(c, n);
      }
    } catch (error) {
      throw new Error(`Complex power operation failed: ${error.message}`);
    } finally {
      PerformanceProfiler.record('complexPower', startTime);
    }
  }

  /**
   * Integer power using binary exponentiation
   * @private
   */
  static _integerPower(z, n) {
    if (n === 0) return new ComplexNumber(1, 0);
    if (n === 1) return z;
    if (n < 0) return this.complexDivide(new ComplexNumber(1, 0), this._integerPower(z, -n));
    
    let result = new ComplexNumber(1, 0);
    let base = z;
    
    while (n > 0) {
      if (n % 2 === 1) {
        result = this.complexMultiply(result, base);
      }
      base = this.complexMultiply(base, base);
      n = Math.floor(n / 2);
    }
    
    return result;
  }

  /**
   * Fractional power using polar form
   * @private
   */
  static _fractionalPower(z, n) {
    const r = z.magnitude();
    const theta = z.argument();
    
    const newR = Math.pow(r, n);
    const newTheta = theta * n;
    
    return new ComplexNumber(
      newR * Math.cos(newTheta),
      newR * Math.sin(newTheta)
    );
  }

  /**
   * Advanced calculus operations
   */
  
  /**
   * Numerical differentiation using central difference
   * @param {Function} func - Function to differentiate
   * @param {number} x - Point to differentiate at
   * @param {number} h - Step size (optional)
   * @returns {number} Derivative at point x
   */
  static numericalDerivative(func, x, h = 1e-6) {
    const startTime = process.hrtime.bigint();
    
    try {
      this.validator.validateFunction(func);
      this.validator.validateNumber(x);
      this.validator.validateNumber(h);
      
      const fPlus = func(x + h);
      const fMinus = func(x - h);
      
      const derivative = (fPlus - fMinus) / (2 * h);
      
      PerformanceProfiler.record('numericalDerivative', startTime);
      return derivative;
    } catch (error) {
      throw new Error(`Numerical differentiation failed: ${error.message}`);
    }
  }

  /**
   * Numerical integration using Simpson's rule
   * @param {Function} func - Function to integrate
   * @param {number} a - Lower bound
   * @param {number} b - Upper bound
   * @param {number} n - Number of intervals (optional)
   * @returns {number} Integral value
   */
  static numericalIntegrate(func, a, b, n = 1000) {
    const startTime = process.hrtime.bigint();
    
    try {
      this.validator.validateFunction(func);
      this.validator.validateNumber(a);
      this.validator.validateNumber(b);
      this.validator.validateNumber(n);
      
      if (a >= b) throw new Error('Lower bound must be less than upper bound');
      
      const h = (b - a) / n;
      let sum = func(a) + func(b);
      
      for (let i = 1; i < n; i++) {
        const x = a + i * h;
        if (i % 2 === 0) {
          sum += 2 * func(x);
        } else {
          sum += 4 * func(x);
        }
      }
      
      const result = (h / 3) * sum;
      
      PerformanceProfiler.record('numericalIntegrate', startTime);
      return result;
    } catch (error) {
      throw new Error(`Numerical integration failed: ${error.message}`);
    }
  }

  /**
   * Special mathematical functions
   */
  
  /**
   * Gamma function using Lanczos approximation
   * @param {number} x - Input value
   * @returns {number} Gamma function value
   */
  static gamma(x) {
    const startTime = process.hrtime.bigint();
    
    try {
      this.validator.validateNumber(x);
      
      if (x < 0.5) {
        return Math.PI / (Math.sin(Math.PI * x) * this.gamma(1 - x));
      }
      
      const p = [
        0.99999999999980993, 676.5203681218851, -1259.1392167224028,
        771.32342877765313, -176.61502916214059, 12.507343278686905,
        -0.13857109526572012, 9.9843695780195716e-6, 1.5056327351493116e-7
      ];
      
      x -= 1;
      let a = p[0];
      const t = x + 7.5;
      
      for (let i = 1; i < p.length; i++) {
        a += p[i] / (x + i);
      }
      
      const result = Math.sqrt(2 * Math.PI) * Math.pow(t, x + 0.5) * Math.exp(-t) * a;
      
      PerformanceProfiler.record('gamma', startTime);
      return result;
    } catch (error) {
      throw new Error(`Gamma function calculation failed: ${error.message}`);
    }
  }

  /**
   * Beta function
   * @param {number} x - First parameter
   * @param {number} y - Second parameter
   * @returns {number} Beta function value
   */
  static beta(x, y) {
    const startTime = process.hrtime.bigint();
    
    try {
      this.validator.validateNumber(x);
      this.validator.validateNumber(y);
      
      if (x <= 0 || y <= 0) {
        throw new Error('Beta function parameters must be positive');
      }
      
      const result = this.gamma(x) * this.gamma(y) / this.gamma(x + y);
      
      PerformanceProfiler.record('beta', startTime);
      return result;
    } catch (error) {
      throw new Error(`Beta function calculation failed: ${error.message}`);
    }
  }

  /**
   * Bessel function of the first kind
   * @param {number} n - Order
   * @param {number} x - Argument
   * @returns {number} Bessel function value
   */
  static besselJ(n, x) {
    const startTime = process.hrtime.bigint();
    
    try {
      this.validator.validateNumber(n);
      this.validator.validateNumber(x);
      
      // Simple implementation using series expansion
      let sum = 0;
      const maxTerms = 50;
      
      for (let k = 0; k < maxTerms; k++) {
        const term = Math.pow(-1, k) * Math.pow(x / 2, n + 2 * k) / 
                    (this.factorial(k) * this.gamma(n + k + 1));
        sum += term;
      }
      
      PerformanceProfiler.record('besselJ', startTime);
      return sum;
    } catch (error) {
      throw new Error(`Bessel function calculation failed: ${error.message}`);
    }
  }

  /**
   * Factorial function
   * @param {number} n - Input value
   * @returns {number} Factorial value
   */
  static factorial(n) {
    if (n < 0) throw new Error('Factorial not defined for negative numbers');
    if (n === 0 || n === 1) return 1;
    
    let result = 1;
    for (let i = 2; i <= n; i++) {
      result *= i;
    }
    return result;
  }

  /**
   * Get performance statistics
   * @returns {Object} Performance statistics
   */
  static getPerformanceStats() {
    return PerformanceProfiler.getStats();
  }

  /**
   * Reset performance statistics
   */
  static resetPerformanceStats() {
    PerformanceProfiler.reset();
  }
}

module.exports = EnhancedMathUtils;
