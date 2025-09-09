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
export * from './core/tensor';
export * from './core/math';
export * from './core/memory';

// Layer exports
export * from './layers/linear';
export * from './layers/attention';
export * from './layers/feedforward';
export * from './layers/normalization';

// Transformer exports
export * from './transformer/transformer-block';
export * from './transformer/language-model';
export * from './transformer/positional-encoding';

// Attention exports
export * from './attention/multi-head-attention';
export * from './attention/group-query-attention';
export * from './attention/rope-encoding';

// Training exports
export * from './training/optimizer';
export * from './training/loss-functions';
export * from './training/data-loader';

// Inference exports
export * from './inference/generation';
export * from './inference/sampling';
export * from './inference/kv-cache';

// Main AI Engine class
export { AIEngine } from './core/ai-engine';

// Types
export * from './types/common';
export * from './types/model';
export * from './types/training';
export * from './types/inference';

// Version info
export const VERSION = '1.0.0';
export const PACKAGE_NAME = '@panadero/ai-engine';
