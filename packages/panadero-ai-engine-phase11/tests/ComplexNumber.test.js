/**
 * ComplexNumber Test Suite
 * 
 * Tests for the ComplexNumber class
 */

const ComplexNumber = require('../src/classes/ComplexNumber');

describe('ComplexNumber', () => {
  describe('Constructor', () => {
    test('should create complex number with real and imaginary parts', () => {
      const z = new ComplexNumber(3, 4);
      expect(z.real).toBe(3);
      expect(z.imag).toBe(4);
    });

    test('should create complex number with default values', () => {
      const z = new ComplexNumber();
      expect(z.real).toBe(0);
      expect(z.imag).toBe(0);
    });

    test('should throw error for invalid input', () => {
      expect(() => new ComplexNumber('invalid', 4)).toThrow();
      expect(() => new ComplexNumber(3, 'invalid')).toThrow();
    });
  });

  describe('Static from() method', () => {
    test('should create from existing ComplexNumber', () => {
      const z1 = new ComplexNumber(3, 4);
      const z2 = ComplexNumber.from(z1);
      expect(z2.real).toBe(3);
      expect(z2.imag).toBe(4);
    });

    test('should create from number', () => {
      const z = ComplexNumber.from(5);
      expect(z.real).toBe(5);
      expect(z.imag).toBe(0);
    });

    test('should create from array', () => {
      const z = ComplexNumber.from([3, 4]);
      expect(z.real).toBe(3);
      expect(z.imag).toBe(4);
    });

    test('should create from object with real/imag', () => {
      const z = ComplexNumber.from({ real: 3, imag: 4 });
      expect(z.real).toBe(3);
      expect(z.imag).toBe(4);
    });

    test('should create from object with r/i', () => {
      const z = ComplexNumber.from({ r: 3, i: 4 });
      expect(z.real).toBe(3);
      expect(z.imag).toBe(4);
    });

    test('should throw error for invalid input', () => {
      expect(() => ComplexNumber.from('invalid')).toThrow();
      expect(() => ComplexNumber.from({})).toThrow();
    });
  });

  describe('Basic operations', () => {
    let z1, z2;

    beforeEach(() => {
      z1 = new ComplexNumber(3, 4);
      z2 = new ComplexNumber(1, 2);
    });

    test('should add complex numbers', () => {
      const result = z1.add(z2);
      expect(result.real).toBe(4);
      expect(result.imag).toBe(6);
    });

    test('should subtract complex numbers', () => {
      const result = z1.subtract(z2);
      expect(result.real).toBe(2);
      expect(result.imag).toBe(2);
    });

    test('should multiply complex numbers', () => {
      const result = z1.multiply(z2);
      expect(result.real).toBe(-5); // 3*1 - 4*2
      expect(result.imag).toBe(10); // 3*2 + 4*1
    });

    test('should divide complex numbers', () => {
      const result = z1.divide(z2);
      expect(result.real).toBe(2.2); // (3*1 + 4*2) / (1*1 + 2*2)
      expect(result.imag).toBe(-0.4); // (4*1 - 3*2) / (1*1 + 2*2)
    });

    test('should throw error when dividing by zero', () => {
      const zero = new ComplexNumber(0, 0);
      expect(() => z1.divide(zero)).toThrow('Division by zero complex number');
    });
  });

  describe('Properties and methods', () => {
    let z;

    beforeEach(() => {
      z = new ComplexNumber(3, 4);
    });

    test('should calculate magnitude', () => {
      expect(z.magnitude()).toBe(5); // sqrt(3^2 + 4^2)
    });

    test('should calculate argument', () => {
      const arg = z.argument();
      expect(arg).toBeCloseTo(Math.atan2(4, 3));
    });

    test('should calculate conjugate', () => {
      const conj = z.conjugate();
      expect(conj.real).toBe(3);
      expect(conj.imag).toBe(-4);
    });

    test('should calculate reciprocal', () => {
      const recip = z.reciprocal();
      expect(recip.real).toBeCloseTo(0.12); // 3/25
      expect(recip.imag).toBeCloseTo(-0.16); // -4/25
    });

    test('should throw error when calculating reciprocal of zero', () => {
      const zero = new ComplexNumber(0, 0);
      expect(() => zero.reciprocal()).toThrow('Cannot calculate reciprocal of zero complex number');
    });
  });

  describe('Power operations', () => {
    let z;

    beforeEach(() => {
      z = new ComplexNumber(1, 1);
    });

    test('should calculate integer powers', () => {
      const z2 = z.pow(2);
      expect(z2.real).toBeCloseTo(0); // (1+i)^2 = 2i
      expect(z2.imag).toBeCloseTo(2);

      const z0 = z.pow(0);
      expect(z0.real).toBe(1);
      expect(z0.imag).toBe(0);
    });

    test('should calculate negative powers', () => {
      const zNeg1 = z.pow(-1);
      const expected = z.reciprocal();
      expect(zNeg1.real).toBeCloseTo(expected.real);
      expect(zNeg1.imag).toBeCloseTo(expected.imag);
    });

    test('should calculate fractional powers', () => {
      const zHalf = z.pow(0.5);
      const zHalfSquared = zHalf.pow(2);
      expect(zHalfSquared.real).toBeCloseTo(z.real, 5);
      expect(zHalfSquared.imag).toBeCloseTo(z.imag, 5);
    });
  });

  describe('Trigonometric functions', () => {
    let z;

    beforeEach(() => {
      z = new ComplexNumber(0, 1); // i
    });

    test('should calculate exponential', () => {
      const exp = z.exp();
      expect(exp.real).toBeCloseTo(Math.cos(1));
      expect(exp.imag).toBeCloseTo(Math.sin(1));
    });

    test('should calculate sine', () => {
      const sin = z.sin();
      expect(sin.real).toBeCloseTo(0);
      expect(sin.imag).toBeCloseTo(Math.sinh(1));
    });

    test('should calculate cosine', () => {
      const cos = z.cos();
      expect(cos.real).toBeCloseTo(Math.cosh(1));
      expect(cos.imag).toBeCloseTo(0);
    });
  });

  describe('Comparison and conversion', () => {
    let z1, z2;

    beforeEach(() => {
      z1 = new ComplexNumber(3, 4);
      z2 = new ComplexNumber(3.0000001, 4.0000001);
    });

    test('should check equality with tolerance', () => {
      expect(z1.equals(z2, 1e-5)).toBe(true);
      expect(z1.equals(z2, 1e-8)).toBe(false);
    });

    test('should convert to string', () => {
      expect(z1.toString()).toBe('3+4i');
      expect(new ComplexNumber(3, -4).toString()).toBe('3-4i');
      expect(new ComplexNumber(0, 4).toString()).toBe('4i');
      expect(new ComplexNumber(3, 0).toString()).toBe('3');
    });

    test('should convert to array', () => {
      const arr = z1.toArray();
      expect(arr).toEqual([3, 4]);
    });

    test('should convert to object', () => {
      const obj = z1.toObject();
      expect(obj).toEqual({ real: 3, imag: 4 });
    });
  });

  describe('Static factory methods', () => {
    test('should create from polar coordinates', () => {
      const z = ComplexNumber.fromPolar(5, Math.PI / 4);
      expect(z.real).toBeCloseTo(5 * Math.cos(Math.PI / 4));
      expect(z.imag).toBeCloseTo(5 * Math.sin(Math.PI / 4));
    });

    test('should create zero', () => {
      const zero = ComplexNumber.zero();
      expect(zero.real).toBe(0);
      expect(zero.imag).toBe(0);
    });

    test('should create one', () => {
      const one = ComplexNumber.one();
      expect(one.real).toBe(1);
      expect(one.imag).toBe(0);
    });

    test('should create imaginary unit', () => {
      const i = ComplexNumber.i();
      expect(i.real).toBe(0);
      expect(i.imag).toBe(1);
    });
  });
});
