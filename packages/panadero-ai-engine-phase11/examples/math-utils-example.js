/**
 * Math Utils Example - Phase 11 Enhanced MathUtils Usage
 * 
 * Demonstrates how to use the enhanced mathematical operations
 * 
 * @author Panadero Services
 * @version 1.1.0
 */

const { EnhancedMathUtils, ComplexNumber } = require('../src/index');

async function mathUtilsExample() {
  console.log('🚀 Panadero AI Engine Phase 11 - Math Utils Example\n');
  
  try {
    // Complex number operations
    console.log('=== Complex Number Operations ===');
    const z1 = new ComplexNumber(3, 4);
    const z2 = new ComplexNumber(1, 2);
    
    console.log(`z1 = ${z1}`);
    console.log(`z2 = ${z2}`);
    console.log(`z1 + z2 = ${z1.add(z2)}`);
    console.log(`z1 * z2 = ${z1.multiply(z2)}`);
    console.log(`z1^2 = ${z1.pow(2)}`);
    console.log(`|z1| = ${z1.magnitude()}`);
    console.log(`arg(z1) = ${z1.argument()}`);
    
    // Advanced mathematical functions
    console.log('\n=== Advanced Mathematical Functions ===');
    console.log(`Gamma(5) = ${EnhancedMathUtils.gamma(5)}`);
    console.log(`Beta(3, 4) = ${EnhancedMathUtils.beta(3, 4)}`);
    console.log(`Bessel J(0, 1) = ${EnhancedMathUtils.besselJ(0, 1)}`);
    console.log(`5! = ${EnhancedMathUtils.factorial(5)}`);
    
    // Numerical calculus
    console.log('\n=== Numerical Calculus ===');
    const func = x => x * x; // f(x) = x²
    const derivative = EnhancedMathUtils.numericalDerivative(func, 2);
    console.log(`d/dx(x²) at x=2: ${derivative} (expected: 4)`);
    
    const integral = EnhancedMathUtils.numericalIntegrate(func, 0, 2);
    console.log(`∫₀² x² dx: ${integral} (expected: 8/3 ≈ 2.667)`);
    
    // Complex power operations
    console.log('\n=== Complex Power Operations ===');
    const z = new ComplexNumber(1, 1);
    console.log(`z = ${z}`);
    console.log(`z^2 = ${EnhancedMathUtils.complexPower(z, 2)}`);
    console.log(`z^0.5 = ${EnhancedMathUtils.complexPower(z, 0.5)}`);
    console.log(`z^(-1) = ${EnhancedMathUtils.complexPower(z, -1)}`);
    
    // Performance statistics
    console.log('\n=== Performance Statistics ===');
    const stats = EnhancedMathUtils.getPerformanceStats();
    console.log('Performance stats:', JSON.stringify(stats, null, 2));
    
    console.log('\n✅ Math Utils Example completed successfully!');
    
  } catch (error) {
    console.error('❌ Error in Math Utils Example:', error.message);
    console.error(error.stack);
  }
}

// Run the example
if (require.main === module) {
  mathUtilsExample();
}

module.exports = mathUtilsExample;
