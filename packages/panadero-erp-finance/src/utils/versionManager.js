/**
 * Version Management Utility
 * @description Centralized version management for the ERP Finance Module
 */

import versionsData from '../data/versions.json';
import rulesData from '../data/rules.json';

/**
 * Get current version
 * @returns {string} Current version number
 */
export function getCurrentVersion() {
  return versionsData.current;
}

/**
 * Get all version releases
 * @returns {Array} Array of version objects
 */
export function getAllVersions() {
  return versionsData.releases;
}

/**
 * Get version by number
 * @param {string} version - Version number (e.g., "1.0.9")
 * @returns {Object|null} Version object or null if not found
 */
export function getVersion(version) {
  return versionsData.releases.find(v => v.version === `v${version}` || v.version === version);
}

/**
 * Get latest features from current version
 * @returns {Array} Array of feature strings
 */
export function getLatestFeatures() {
  const current = versionsData.releases.find(v => v.status === 'current');
  return current ? current.features : [];
}

/**
 * Get development rules
 * @returns {Object} Development rules object
 */
export function getDevelopmentRules() {
  return rulesData.development_rules;
}

/**
 * Get specific rule category
 * @param {string} category - Rule category (e.g., 'vue_standards', 'code_quality')
 * @returns {Object|null} Rule category object or null if not found
 */
export function getRule(category) {
  return rulesData.development_rules[category] || null;
}

/**
 * Get files that need updating for version changes
 * @returns {Array} Array of file paths
 */
export function getVersionUpdateFiles() {
  return rulesData.version_management.versioning.update_files;
}

/**
 * Check if version exists
 * @param {string} version - Version number to check
 * @returns {boolean} True if version exists
 */
export function versionExists(version) {
  return versionsData.releases.some(v => v.version === `v${version}` || v.version === version);
}

/**
 * Get version status
 * @param {string} version - Version number
 * @returns {string|null} Version status or null if not found
 */
export function getVersionStatus(version) {
  const versionObj = getVersion(version);
  return versionObj ? versionObj.status : null;
}

/**
 * Get forbidden actions list
 * @returns {Array} Array of forbidden action strings
 */
export function getForbiddenActions() {
  return rulesData.forbidden_actions.never_do;
}

/**
 * Get emergency procedures
 * @param {string} type - Type of emergency ('if_breaking_functionality', 'if_user_frustrated')
 * @returns {Object|null} Emergency procedure object or null if not found
 */
export function getEmergencyProcedure(type) {
  return rulesData.emergency_procedures[type] || null;
}

/**
 * Validate version format
 * @param {string} version - Version string to validate
 * @returns {boolean} True if valid semantic version format
 */
export function isValidVersionFormat(version) {
  const semanticVersionRegex = /^v?\d+\.\d+\.\d+$/;
  return semanticVersionRegex.test(version);
}

/**
 * Get next version number (auto-increment patch)
 * @returns {string} Next patch version
 */
export function getNextPatchVersion() {
  const current = versionsData.current;
  const [major, minor, patch] = current.split('.').map(Number);
  return `${major}.${minor}.${patch + 1}`;
}

/**
 * Export for easy access to raw data
 */
export { versionsData, rulesData };
