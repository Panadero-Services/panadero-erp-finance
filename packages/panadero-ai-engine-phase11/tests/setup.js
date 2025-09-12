/**
 * Jest Test Setup - Phase 11
 * 
 * Global test setup and configuration
 */

// Set test environment
process.env.NODE_ENV = 'test';

// Global test utilities
global.testUtils = {
  // Generate random test data
  randomArray: (length, min = 0, max = 100) => {
    return Array.from({ length }, () => Math.random() * (max - min) + min);
  },
  
  // Generate random matrix
  randomMatrix: (rows, cols, min = 0, max = 100) => {
    return Array.from({ length: rows }, () => 
      Array.from({ length: cols }, () => Math.random() * (max - min) + min)
    );
  },
  
  // Generate random complex number
  randomComplex: (minReal = -10, maxReal = 10, minImag = -10, maxImag = 10) => {
    const real = Math.random() * (maxReal - minReal) + minReal;
    const imag = Math.random() * (maxImag - minImag) + minImag;
    return { real, imag };
  },
  
  // Assert approximately equal (for floating point comparisons)
  expectApproximatelyEqual: (actual, expected, tolerance = 1e-10) => {
    expect(Math.abs(actual - expected)).toBeLessThan(tolerance);
  }
};

// Mock console methods in tests to reduce noise
const originalConsole = console;
global.console = {
  ...originalConsole,
  log: jest.fn(),
  warn: jest.fn(),
  error: jest.fn()
};

// Restore console after tests
afterAll(() => {
  global.console = originalConsole;
});
