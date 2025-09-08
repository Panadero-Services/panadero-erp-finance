/**
 * ERP HR Module
 * @version 1.0.0
 * @description Complete human resources management system with AI-powered recruitment
 */

// Main wrapper component
export { default as HrWrapper } from './src/HrWrapper.vue';

// Store
export { useHrStore } from './src/stores/hrStore.js';

// UI Components
export { default as HrButton } from './src/components/ui/HrButton.vue';
export { default as HrInput } from './src/components/ui/HrInput.vue';
export { default as HrDropdown } from './src/components/ui/HrDropdown.vue';
export { default as HrValueCard } from './src/components/ui/HrValueCard.vue';
export { default as StatusBadge } from './src/components/ui/StatusBadge.vue';

// Layout Components
export { default as HrLayout } from './src/components/layout/HrLayout.vue';
export { default as HrNavigation } from './src/components/navigation/HrNavigation.vue';

// Main Components
export { default as InfoBoard } from './src/components/InfoBoard.vue';
export { default as HrDashboard } from './src/components/dashboard/HrDashboard.vue';
export { default as EmployeeManagement } from './src/components/EmployeeManagement.vue';
export { default as Recruitment } from './src/components/Recruitment.vue';
export { default as VacancyManagement } from './src/components/VacancyManagement.vue';
export { default as DepartmentManagement } from './src/components/DepartmentManagement.vue';
export { default as HrReporting } from './src/components/HrReporting.vue';
export { default as AgentPortal } from './src/components/AgentPortal.vue';

// Configuration
export { default as config } from './config.json';

// Package information
export const packageInfo = {
  name: 'panadero-erp-hr',
  version: '1.0.0',
  description: 'ERP HR Module - Complete human resources management with AI-powered recruitment',
  type: 'erp-module',
  category: 'hr',
  color: 'green',
  icon: 'UserGroupIcon'
};
