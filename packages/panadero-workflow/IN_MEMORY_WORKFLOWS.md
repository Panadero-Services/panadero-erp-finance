# In-Memory Workflow System

## Overview

The workflow system now supports **in-memory workflows** that are created when the start button is clicked and persist in memory until completion. The final step automatically saves the completed workflow to the database.

## How It Works

### 1. Workflow Creation (Start Button)
```javascript
// When start button is clicked:
const instance = workflowStore.createInMemoryWorkflow(templateId, {
  created_by: 'current_user',
  workflow_data: { /* ... */ }
})
```

### 2. Workflow Execution (In Memory)
```javascript
// Start the in-memory workflow:
await workflowStore.startInMemoryWorkflow(instance.id)

// Complete steps:
workflowStore.completeInMemoryWorkflowStep(instance.id, stepIndex, stepData, user)
```

### 3. Database Persistence (Final Step)
```javascript
// When the last step is completed:
// - Workflow status changes to 'completed'
// - Workflow moves from inMemoryWorkflows to workflowInstances
// - Ready for database save
```

## Store Structure

### New State Properties
```javascript
// In-memory workflows array
inMemoryWorkflows: ref([])

// Computed properties
activeInMemoryWorkflows: computed(() => 
  inMemoryWorkflows.value.filter(w => w.status === 'active')
)

draftInMemoryWorkflows: computed(() => 
  inMemoryWorkflows.value.filter(w => w.status === 'draft')
)
```

### New Methods
```javascript
// Create in-memory workflow
createInMemoryWorkflow(templateId, data)

// Start in-memory workflow
startInMemoryWorkflow(instanceId)

// Complete in-memory workflow step
completeInMemoryWorkflowStep(instanceId, stepIndex, data, user)
```

## Workflow Lifecycle

1. **DRAFT** - Created in memory, not yet started
2. **ACTIVE** - Running in memory, steps being executed
3. **COMPLETED** - All steps finished, moved to persistent storage
4. **Database Save** - Final step saves to database

## Benefits

- **Performance** - No database writes during execution
- **Rollback** - Can discard incomplete workflows
- **Memory Management** - Automatic cleanup of completed workflows
- **Database Integration** - Seamless persistence when complete

## Usage Example

```javascript
// 1. Create workflow
const workflow = workflowStore.createInMemoryWorkflow('vendor-onboarding', {
  created_by: 'john.doe',
  vendor_data: { /* ... */ }
})

// 2. Start workflow
await workflowStore.startInMemoryWorkflow(workflow.id)

// 3. Execute steps
workflowStore.completeInMemoryWorkflowStep(workflow.id, 0, { vendor: 'Acme Corp' })
workflowStore.completeInMemoryWorkflowStep(workflow.id, 1, { financial_data: { /* ... */ } })

// 4. Final step automatically saves to database
workflowStore.completeInMemoryWorkflowStep(workflow.id, 2, { approval: 'granted' })
// Workflow now in workflowInstances array, ready for database persistence
```

## Integration Points

- **Start Button** → `createInMemoryWorkflow()`
- **Step Completion** → `completeInMemoryWorkflowStep()`
- **Final Step** → Automatic database save preparation
- **Dashboard** → Shows both in-memory and persistent workflows

## Version History

- **v1.1.0** - Added in-memory workflow system
- **v1.1.1** - Updated dashboard to use in-memory workflows

