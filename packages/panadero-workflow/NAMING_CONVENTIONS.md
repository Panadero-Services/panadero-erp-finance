# Panadero ERP Workflow Package - Naming Conventions

## Overview
This document defines the standardized naming conventions for the Panadero ERP Workflow Management Package. Following these conventions ensures consistency, clarity, and maintainability across the entire workflow system.

## 📁 File Naming Conventions

### 1. Vue Component Files

#### External Components (Root Level)
**Purpose**: Main entry points for external consumption
**Naming**: `[ComponentName].vue` (no prefix needed)
**Examples**:
```
Dashboard.vue          # Main workflow dashboard
Demo.vue               # Workflow demonstration interface
Manager.vue            # Workflow creation/management
```

#### Internal Components (components/ subdirectory)
**Purpose**: Reusable workflow-specific components
**Naming**: `Workflow[ComponentName].vue` (Workflow prefix required)
**Examples**:
```
WorkflowForm.vue       # Dynamic form rendering
WorkflowStepper.vue    # Step navigation component
WorkflowInfo.vue       # Workflow information display
WorkflowModal.vue      # Workflow modal interface
```

#### Generic Components (components/ subdirectory)
**Purpose**: Reusable components not specific to workflows
**Naming**: `[ComponentName].vue` (no prefix needed)
**Examples**:
```
Card.vue               # Generic workflow card
Statistics.vue         # Generic workflow statistics
Template.vue           # Generic workflow template
ActiveCard.vue         # Generic active workflow card
DetailModal.vue        # Generic detailed view modal
```

### 2. JavaScript/TypeScript Files

#### Composables
**Purpose**: Reusable logic and state management
**Naming**: `use[Domain][Function].js`
**Examples**:
```
useWorkflowStore.js    # Workflow state management
useWorkflowConfig.js   # Workflow configuration
useWorkflowDashboard.js # Dashboard logic
```

#### Stores
**Purpose**: Pinia store modules
**Naming**: `[domain]Store.js`
**Examples**:
```
workflowStore.js       # Workflow state store
financeStore.js        # Finance module store
```

#### Utilities
**Purpose**: Helper functions and utilities
**Naming**: `[functionName].js` or `[domain]Utils.js`
**Examples**:
```
workflowUtils.js       # Workflow utility functions
validationUtils.js     # Validation helper functions
```

### 3. Configuration Files

#### JSON Configuration
**Purpose**: Static configuration data
**Naming**: `[purpose]-[type].json`
**Examples**:
```
workflow-config.json   # Global workflow settings
workflow-index.json    # Workflow registry
vendor-onboarding.workflow.json # Specific workflow definition
```

#### Package Configuration
**Purpose**: Package metadata and dependencies
**Naming**: `package.json`
**Examples**:
```
package.json           # Main package configuration
```

## 🔗 Import Path Conventions

### 1. Internal Package Imports

#### From Root Level Components
```javascript
// ✅ Correct - Direct import
import WorkflowManager from './Manager.vue'
import WorkflowDemo from './Demo.vue'
import WorkflowDashboard from './Dashboard.vue'
```

#### From Components Subdirectory
```javascript
// ✅ Correct - Components subdirectory
import WorkflowForm from './components/WorkflowForm.vue'
import WorkflowStepper from './components/WorkflowStepper.vue'
import WorkflowInfo from './components/WorkflowInfo.vue'

// ✅ Correct - Generic components
import Card from './components/Card.vue'
import Statistics from './components/Statistics.vue'
```

### 2. External Package Imports

#### From Other ERP Modules
```javascript
// ✅ Correct - Using package index
import { 
  WorkflowDashboard, 
  WorkflowDemo, 
  WorkflowManager 
} from '@panadero-erp/workflow-management'

// ✅ Correct - Individual component import
import { WorkflowDashboard } from '@panadero-erp/workflow-management/Dashboard.vue'
```

#### From Finance Package
```javascript
// ✅ Correct - Using finance package exports
import { 
  WorkflowDashboard, 
  WorkflowDemo, 
  WorkflowManager 
} from '../../packages/panadero-workflow/index.js'
```

## 📦 Export Conventions

### 1. Package Index Exports

#### Main External Components
```javascript
// ✅ Correct - Main external interface
export { default as WorkflowDashboard } from './Dashboard.vue'
export { default as WorkflowDemo } from './Demo.vue'
export { default as WorkflowManager } from './Manager.vue'
```

#### Internal Components
```javascript
// ✅ Correct - Internal components for advanced usage
export { default as WorkflowForm } from './components/WorkflowForm.vue'
export { default as WorkflowStepper } from './components/WorkflowStepper.vue'
export { default as WorkflowInfo } from './components/Info.vue'
export { default as WorkflowCard } from './components/Card.vue'
```

#### Composables and Utilities
```javascript
// ✅ Correct - Reusable logic
export { default as useWorkflowStore } from './composables/WorkflowStore.js'
```

### 2. Component Props and Emits

#### Props Naming
**Convention**: `camelCase` with descriptive names
**Examples**:
```javascript
const props = defineProps({
  workflowStep: { type: Object, default: () => ({}) },
  formData: { type: Object, default: () => ({}) },
  showStats: { type: Boolean, default: false },
  activeWorkflow: { type: Object, default: null }
})
```

#### Emits Naming
**Convention**: `kebab-case` with descriptive action names
**Examples**:
```javascript
const emit = defineEmits([
  'workflow-selected',
  'workflow-started',
  'workflow-completed',
  'update-step-data'
])
```

## 🎨 CSS Class Naming Conventions

### 1. Tailwind CSS Classes
**Framework**: Use Tailwind CSS classes exclusively
**Custom CSS**: Avoid unless absolutely necessary

### 2. Dynamic Styling
**Convention**: Use `store.fontSizes.base` for dynamic font scaling
**Examples**:
```vue
<!-- ✅ Correct - Dynamic font scaling -->
<span :style="{ fontSize: `${store.fontSizes.base - 2}px` }">
  Step Description
</span>

<!-- ❌ Incorrect - Hardcoded font sizes -->
<span class="text-xs">Step Description</span>
```

### 3. Dark Mode Support
**Convention**: Use `dark:` prefixes for dark mode variants
**Examples**:
```vue
<!-- ✅ Correct - Dark mode support -->
<div class="bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
  Content
</div>
```

## 🔧 Function and Variable Naming

### 1. Function Names
**Convention**: `camelCase` with descriptive action names
**Examples**:
```javascript
// ✅ Correct - Clear action names
function handleWorkflowSelection(workflow) { }
function updateStepData(stepId, data) { }
function validateStepRequirements(step) { }
function loadWorkflowConfiguration() { }
```

### 2. Variable Names
**Convention**: `camelCase` with descriptive names
**Examples**:
```javascript
// ✅ Correct - Descriptive variable names
const selectedWorkflow = ref(null)
const currentStepData = ref({})
const workflowConfiguration = ref({})
const stepValidationErrors = ref({})
```

### 3. Constants
**Convention**: `UPPER_SNAKE_CASE` for true constants
**Examples**:
```javascript
// ✅ Correct - Constants
const WORKFLOW_STATES = {
  PENDING: 'pending',
  ACTIVE: 'active',
  COMPLETED: 'completed'
}

const STEP_TYPES = {
  USER_ACTION: 'user_action',
  APPROVAL: 'approval',
  FORM_SUBMISSION: 'form_submission'
}
```

## 📋 Component Structure Conventions

### 1. Vue SFC Order
**Convention**: Follow Vue.js SFC standard structure
**Order**:
```vue
<!-- 1. Script Setup -->
<script setup>
// Component logic
</script>

<!-- 2. Template -->
<template>
  <!-- Component markup -->
</template>

<!-- 3. Style (if needed) -->
<style>
/* Component styles */
</style>
```

### 2. Import Order
**Convention**: Group imports by type and source
**Order**:
```javascript
// 1. Vue core imports
import { ref, computed, onMounted } from 'vue'

// 2. Store imports
import { useFinanceStore } from '../../stores/financeStore.js'
import { useWorkflowStore } from '../../stores/workflowStore.js'

// 3. Composable imports
import { useWorkflowConfig } from '../../composables/useWorkflowConfig.js'

// 4. Component imports
import WorkflowStepper from './components/WorkflowStepper.vue'
import WorkflowForm from './components/WorkflowForm.vue'
import WorkflowInfo from './components/Info.vue'

// 5. UI component imports
import FinanceButton from '../ui/FinanceButton.vue'
import StatusBadge from '../ui/StatusBadge.vue'
```

## 🚫 Forbidden Naming Patterns

### 1. Never Use
- ❌ **Generic names**: `Form.vue`, `Stepper.vue`, `Modal.vue`
- ❌ **Abbreviations**: `WF.vue`, `WfForm.vue`
- ❌ **Underscores**: `workflow_form.vue`, `workflow-form.vue`
- ❌ **Numbers**: `Workflow1.vue`, `Form2.vue`

### 2. Avoid
- ❌ **Overly long names**: `WorkflowFormComponentForDynamicRendering.vue`
- ❌ **Inconsistent prefixes**: `WorkflowForm.vue` mixed with `Form.vue`
- ❌ **Unclear abbreviations**: `WfMgr.vue`, `WfFrm.vue`

## ✅ Best Practices Summary

1. **Be Descriptive**: Component names should clearly indicate their purpose
2. **Be Consistent**: Use the same naming pattern throughout the system
3. **Be Specific**: Workflow components get `Workflow` prefix, generic ones don't
4. **Be Clear**: Avoid abbreviations and unclear naming
5. **Follow Standards**: Adhere to Vue.js and JavaScript naming conventions
6. **Use Prefixes**: `Workflow` prefix for workflow-specific components
7. **Maintain Hierarchy**: Clear distinction between external and internal components

## 🔍 Naming Checklist

Before creating or renaming any component, verify:

- [ ] **Purpose Clear**: Does the name clearly indicate the component's purpose?
- [ ] **Prefix Correct**: Is the `Workflow` prefix used appropriately?
- [ ] **Consistent**: Does it follow the established naming pattern?
- [ ] **Descriptive**: Is the name descriptive enough for other developers?
- [ ] **No Conflicts**: Does it avoid naming conflicts with other components?
- [ ] **Vue Compliant**: Does it follow Vue.js naming conventions?

---

**Last Updated**: August 19, 2025  
**Version**: 1.0.0  
**Maintained By**: Panadero ERP Development Team
