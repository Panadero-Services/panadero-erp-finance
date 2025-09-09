/**
 * Panadero AI Engine - Main Entry Point
 * 
 * Advanced Neural Network Framework for Predictive Analytics and Mathematical Modeling
 * Built on 20 years of Indigo1, Indigo2, and Indigo3 foundation
 * 
 * @author Panadero Services
 * @version 1.0.0
 */

// Core exports
const Tensor = require('./core/tensor');
const MathUtils = require('./core/math');
const MemoryManager = require('./core/memory');

// Layer exports
const LinearLayer = require('./layers/linear');
const Attention = require('./layers/attention');
const FeedForward = require('./layers/feedforward');
const RMSNorm = require('./layers/normalization');

// Transformer exports
const TransformerBlock = require('./transformer/transformer-block');
const LanguageModel = require('./transformer/language-model');
const PositionalEncoding = require('./transformer/positional-encoding');

// Attention exports
const MultiHeadAttention = require('./attention/multi-head-attention');
const GroupQueryAttention = require('./attention/group-query-attention');
const RoPEEncoding = require('./attention/rope-encoding');

// Training exports
const Optimizer = require('./training/optimizer');
const LossFunctions = require('./training/loss-functions');
const DataLoader = require('./training/data-loader');

// Inference exports
const TextGenerator = require('./inference/generation');
const SamplingStrategies = require('./inference/sampling');
const KVCache = require('./inference/kv-cache');

// Main AI Engine class
const AIEngine = require('./core/ai-engine');

// Version info
const VERSION = '1.0.0';
const PACKAGE_NAME = '@panadero/ai-engine';

module.exports = {
  // Core
  Tensor,
  MathUtils,
  MemoryManager,
  
  // Layers
  LinearLayer,
  Attention,
  FeedForward,
  RMSNorm,
  
  // Transformer
  TransformerBlock,
  LanguageModel,
  PositionalEncoding,
  
  // Attention
  MultiHeadAttention,
  GroupQueryAttention,
  RoPEEncoding,
  
  // Training
  Optimizer,
  LossFunctions,
  DataLoader,
  
  // Inference
  TextGenerator,
  SamplingStrategies,
  KVCache,
  
  // Main Engine
  AIEngine,
  
  // Version
  VERSION,
  PACKAGE_NAME
};
