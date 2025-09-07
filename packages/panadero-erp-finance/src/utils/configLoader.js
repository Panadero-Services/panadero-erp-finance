// Import from shared packages location - works in both dev and production
import frameworkConfig from '../../../shared/config/panadero-packages-config.json';

export async function loadPackageConfig() {
  try {
    // Fixed: Use relative path from utils/ to package root
    const config = await import('../../config.json');
    return config.default || config;
  } catch (error) {
    console.error(`Could not load package config:`, error);
    throw error;
  }
}

// FIXED: Remove async since we're doing a direct return
export function loadFrameworkConfig() {
  console.debug('Framework config loaded:', frameworkConfig);
  console.debug('Shared entities:', frameworkConfig.sharedEntities);
  return frameworkConfig; // Direct return, not wrapped in Promise
}

export async function loadFullConfig() {
  try {
    const packageConfig = await loadPackageConfig();
    const framework = loadFrameworkConfig(); // Now this returns the actual object, not a Promise

    console.debug('Package config:', packageConfig);
    console.debug('Framework config:', framework);

    const fullConfig = {
      ...packageConfig,
      colorScheme: framework.colorScheme,
      sharedEntities: framework.sharedEntities,
      framework: framework.framework,
      packageTypes: framework.packageTypes,
      ui: framework.ui,
      timestampFields: framework.timestampFields,
      navigation: {
        ...framework.navigation,
        ...packageConfig.navigation
      }
    };

    console.debug('Full merged config:', fullConfig);
    console.debug('Final shared entities:', fullConfig.sharedEntities);

    return fullConfig;
  } catch (error) {
    console.error(`Failed to load full config:`, error);
    throw error;
  }
}

export async function loadVersionsData() {
  try {
    // Fixed: Use relative path from utils/ to package data
    const versionsData = await import('../data/versions.json');
    return versionsData.default || versionsData;
  } catch (error) {
    console.warn(`Could not load versions.json:`, error);
    return {
      current: "1.0.0",
      releases: []
    };
  }
}

// Helper functions
export function getColorClasses(color, framework) {
  return framework.colorScheme?.[color] || framework.colorScheme?.blue || '';
}

export function getTimestampFields(packageType, framework) {
  if (!framework.timestampFields) return ['timestamp'];
  
  const common = framework.timestampFields.common || ['timestamp'];
  
  switch (packageType) {
    case 'erp-finance':
      return [...common, ...(framework.timestampFields.financial || [])];
    case 'workflow':
      return [...common, ...(framework.timestampFields.operational || [])];
    default:
      return common;
  }
}

export function getPackageTypeInfo(packageType, framework) {
  return framework.packageTypes?.[packageType] || {
    category: "Unknown",
    defaultIcon: "fas fa-question",
    description: "Unknown package type"
  };
}
