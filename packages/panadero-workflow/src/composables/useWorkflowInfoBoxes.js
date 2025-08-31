import { useGenericInfoBoxes } from '../../../shared/composables/useGenericInfoBoxes.js';
import { useWorkflowStore } from '../composables/workflowStore.js';
import versionsData from '../data/versions.json';

export function useWorkflowInfoBoxes() {
  const store = useWorkflowStore();
  
  const workflowConfig = {
    store,
    packageName: 'workflow',
    tablePrefix: 'workflow_',
    title: 'Workflow Management Module',
    description: 'Business process automation and workflow management with comprehensive step tracking and approval workflows.',
    
    // Table configurations
    tableConfigs: [
      {
        name: 'workflow_instances',
        getRecords: (store) => store.workflows.length,
        getData: (store) => store.workflows,
        fallbackText: 'workflow instances'
      },
      {
        name: 'workflow_templates',
        getRecords: (store) => store.workflowTemplates.length,
        getData: (store) => store.workflowTemplates,
        fallbackText: 'workflow templates'
      },
      {
        name: 'workflow_actions',
        getRecords: (store) => store.workflowActions.length,
        getData: (store) => store.workflowActions,
        fallbackText: 'workflow actions'
      },
      {
        name: 'workflow_steps',
        getRecords: (store) => store.workflows.reduce((total, wf) => total + (wf.steps?.length || 0), 0),
        getData: (store) => store.workflows.flatMap(wf => wf.steps || []),
        fallbackText: 'workflow steps'
      },
      {
        name: 'workflow_approvals',
        getRecords: (store) => store.workflows.filter(wf => wf.status === 'pending').length,
        getData: (store) => store.workflows.filter(wf => wf.status === 'pending'),
        fallbackText: 'approvals'
      }
    ],
    
    // Shared entities configuration
    sharedEntitiesConfig: [
      {
        id: 'users',
        name: 'Users',
        color: 'blue',
        icon: 'fas fa-users',
        path: 'Framework Core',
        getRecords: () => 0, // This would come from framework
        getLastUpdated: () => 'today'
      },
      {
        id: 'roles',
        name: 'Roles',
        color: 'green',
        icon: 'fas fa-user-shield',
        path: 'Framework Core',
        getRecords: () => 0,
        getLastUpdated: () => 'today'
      },
      {
        id: 'permissions',
        name: 'Permissions',
        color: 'purple',
        icon: 'fas fa-key',
        path: 'Framework Core',
        getRecords: () => 0,
        getLastUpdated: () => 'today'
      }
    ],
    
    // Version data
    versionData: versionsData,
    
    // Navigation routes
    navigationRoutes: {
      'users': '/admin/users',
      'roles': '/admin/roles',
      'permissions': '/admin/permissions',
      'workflow_instances': '#instances',
      'workflow_templates': '#templates',
      'workflow_actions': '#actions',
      'workflow_steps': '#steps',
      'workflow_approvals': '#approvals'
    },
    
    // Module descriptions
    descriptions: [
      {
        id: 1,
        title: 'Workflow Instances',
        count: 0, // Will be computed
        list: [
          'Active workflows',
          'Completed processes',
          'Pending approvals',
          'Failed workflows'
        ]
      },
      {
        id: 2,
        title: 'Workflow Templates',
        count: 0, // Will be computed
        list: [
          'Predefined workflows',
          'Custom templates',
          'Approval chains',
          'Business processes'
        ]
      },
      {
        id: 3,
        title: 'Workflow Actions',
        count: 0, // Will be computed
        list: [
          'Approval actions',
          'Data collection',
          'Integration tasks',
          'Notification triggers'
        ]
      }
    ]
  };
  
  return useGenericInfoBoxes(workflowConfig);
}
