// Panadero Shared Components Package
// Main entry point for shared UI components

// Export all shared components
export { default as FrameworkSettingsPanel } from './src/components/FrameworkSettingsPanel.vue';
export { default as GenericInfoSection } from './src/components/GenericInfoSection.vue';

// Package information
export const packageInfo = {
  name: 'panadero-shared-components',
  version: '1.0.0',
  description: 'Shared UI components for all Panadero packages',
  author: 'JaWsome.Orbit',
  components: [
    'FrameworkSettingsPanel',
    'GenericInfoSection'
  ]
};





