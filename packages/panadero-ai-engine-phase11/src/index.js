/**
 * Panadero AI Engine Phase 11 - Math & Predictive Foundation
 * 
 * Enhanced mathematical and statistical capabilities for Panadero AI Engine
 * Built on @panadero/ai-engine v1.0.0
 * 
 * @author Panadero Services
 * @version 1.1.0
 */

// Core AI Engine (v1.0.0) - Required dependency
const { Tensor, MathUtils, MemoryManager } = require('@panadero/ai-engine');

// Phase 11 Enhanced Modules
const EnhancedMathUtils = require('./modules/math-utils/EnhancedMathUtils');
const TensorStatistics = require('./modules/tensor-stats/TensorStatistics');
const TimeSeriesAnalysis = require('./modules/time-series/TimeSeriesAnalysis');
const MatrixOperations = require('./modules/matrix-ops/MatrixOperations');
const MathematicalModeling = require('./modules/math-modeling/MathematicalModeling');

// Utility Classes
const ComplexNumber = require('./classes/ComplexNumber');
const StatisticalDistribution = require('./classes/StatisticalDistribution');
const OptimizationResult = require('./classes/OptimizationResult');

// Utility Functions
const MathValidation = require('./utils/MathValidation');
const PerformanceProfiler = require('./utils/PerformanceProfiler');
const ErrorHandler = require('./utils/ErrorHandler');

// Version info
const VERSION = '1.1.0';
const PACKAGE_NAME = '@panadero/ai-engine-phase11';

module.exports = {
  // Version info
  VERSION,
  PACKAGE_NAME,
  
  // Core AI Engine (v1.0.0) - Re-exported
  Tensor,
  MathUtils,
  MemoryManager,
  
  // Phase 11 Enhanced Modules
  EnhancedMathUtils,
  TensorStatistics,
  TimeSeriesAnalysis,
  MatrixOperations,
  MathematicalModeling,
  
  // Utility Classes
  ComplexNumber,
  StatisticalDistribution,
  OptimizationResult,
  
  // Utility Functions
  MathValidation,
  PerformanceProfiler,
  ErrorHandler,
  
  // Convenience exports
  Math: EnhancedMathUtils,
  Stats: TensorStatistics,
  TimeSeries: TimeSeriesAnalysis,
  Matrix: MatrixOperations,
  Modeling: MathematicalModeling
};
