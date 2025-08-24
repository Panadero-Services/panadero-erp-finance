# Panadero ERP Workflow Management Package

## Overview
A comprehensive workflow management system for Panadero ERP, providing dynamic workflow creation, execution, and monitoring capabilities.

## Package Structure
```
panadero-workflow/
├── index.js                 # Main export file
├── package.json            # Package configuration
├── README.md               # This file
├── Dashboard.vue           # Main workflow dashboard (External)
├── Demo.vue                # Workflow demonstration (External)
├── Manager.vue             # Workflow management (External)
├── components/             # Internal components
│   ├── Card.vue           # Workflow card component
│   ├── WorkflowForm.vue   # Dynamic form component
│   ├── Modal.vue          # Workflow modal component
│   ├── WorkflowStepper.vue # Step navigation component
│   ├── Info.vue           # Workflow information display
│   ├── Statistics.vue     # Workflow statistics
│   ├── DetailModal.vue    # Detailed workflow view
│   ├── ActiveCard.vue     # Active workflow card
│   └── Template.vue       # Workflow template component
└── composables/            # Reusable logic
    └── WorkflowStore.js   # Workflow state management
```

## External Components

### 1. WorkflowDashboard.vue
**Purpose**: Main workflow dashboard interface
**Usage**: Primary entry point for workflow management
**Props**: 
- `showStats`: Boolean to show/hide statistics
**Emits**: 
- `workflow-selected`: When a workflow is selected
- `workflow-started`: When a workflow is started

### 2. WorkflowDemo.vue
**Purpose**: Workflow demonstration and testing interface
**Usage**: Development and testing of workflow functionality
**Features**: Live workflow execution, step-by-step demonstration

### 3. WorkflowManager.vue
**Purpose**: Workflow creation and management interface
**Usage**: Creating new workflows, editing existing ones
**Props**:
- `workflowId`: String ID for existing workflow
- `mode`: 'create', 'view', or 'edit'
- `templateId`: String ID for workflow template

## Internal Components

### Core Components
- **Card.vue**: Individual workflow display card
- **WorkflowForm.vue**: Dynamic form rendering based on workflow schema
- **Modal.vue**: Workflow modal interface
- **WorkflowStepper.vue**: Step navigation and progress display
- **Info.vue**: Workflow information display
- **Statistics.vue**: Workflow metrics and analytics

### Specialized Components
- **DetailModal.vue**: Comprehensive workflow details
- **ActiveCard.vue**: Active workflow status card
- **Template.vue**: Workflow template management

## Usage

### Basic Import
```javascript
import { WorkflowDashboard, WorkflowDemo, WorkflowManager } from '../../packages/panadero-workflow/index.js'
```

### Individual Component Import
```javascript
import { WorkflowDashboard } from '../../packages/panadero-workflow/Dashboard.vue'
```

### In Template
```vue
<template>
  <WorkflowDashboard 
    :showStats="true"
    @workflow-selected="handleWorkflowSelected"
    @workflow-started="handleWorkflowStarted"
  />
</template>
```

## Dependencies
- Vue 3.3.0+
- Pinia 2.1.0+
- Tailwind CSS (for styling)

## Development Rules
- Follow Vue.js SFC standard structure
- Use dynamic font scaling via store.fontSizes.base
- No watchers or event listeners (use props/emits)
- Keep styling basic with Tailwind CSS
- Maintain component reusability

## Version History
- **v1.0.12**: Wrapper Refactoring, Dynamic Col-Span System, Statistics Configuration, Enhanced Info Section
- **v1.0.11**: DRY Settings Bridge, Dynamic Styling Architecture, Enhanced Component Separation
- **v1.0.10**: Independent Package Architecture
- **v1.0.9**: Package restructuring and modularization
- **v1.0.8**: Enhanced workflow components and modal system
- **v1.0.7**: Initial workflow management system

## Support
For issues or questions, please refer to the main Panadero ERP documentation or create an issue in the repository.
