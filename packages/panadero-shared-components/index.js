// Panadero Shared Components Package
// Main entry point for shared UI components

// Export all shared components
export { default as FrameworkSettingsPanel } from './src/components/FrameworkSettingsPanel.vue';
export { default as GenericInfoSection } from './src/components/genericInfoSection.vue';
export { default as SharedButton } from './src/components/SharedButton.vue';
export { default as SharedDropdown } from './src/components/SharedDropdown.vue';
export { default as KPICard } from './src/components/KPICard.vue';
export { default as DistributionCard } from './src/components/DistributionCard.vue';
export { default as ActionCard } from './src/components/ActionCard.vue';
export { default as ValidatedForm } from './src/components/ValidatedForm.vue';
export { default as ConfirmationModal } from './src/components/ConfirmationModal.vue';

// Package information
export const packageInfo = {
  name: 'panadero-shared-components',
  version: '1.0.6',
  description: 'Shared UI components for all Panadero packages',
  author: 'JaWsome.Orbit',
  components: [
    'FrameworkSettingsPanel',
    'GenericInfoSection',
    'SharedButton',
    'SharedDropdown',
    'KPICard',
    'DistributionCard',
    'ActionCard',
    'ValidatedForm',
    'ConfirmationModal'
  ]
};





