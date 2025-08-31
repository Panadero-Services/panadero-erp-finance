<script setup>
import GenericInfoSection from '../../../../shared/components/GenericInfoSection.vue';
import { useWorkflowStore } from '../../composables/workflowStore';
import { useWorkflowInfoBoxes } from '../../composables/useWorkflowInfoBoxes';

const store = useWorkflowStore();
const { 
  moduleDescription, 
  versionUpdates, 
  packageTables, 
  sharedEntities,
  toggleInfoBox,
  navigateToEntity
} = useWorkflowInfoBoxes();

const workflowConfig = {
  name: 'Workflow',
  title: 'Workflow Management System',
  description: 'Business process automation and workflow management',
  icon: 'fas fa-project-diagram',
  color: 'blue'
};
</script>

<template>
  <GenericInfoSection
    :package-config="workflowConfig"
    :module-description="moduleDescription"
    :version-updates="versionUpdates"
    :package-tables="packageTables"
    :shared-entities="sharedEntities"
    :table-name-prefix="'workflow_'"
    :table-name-replacement="'_'"
    :on-toggle-info-box="toggleInfoBox"
    :on-navigate-to-entity="navigateToEntity"
    :show-version-updates="true"
    :show-package-tables="true"
    :show-shared-entities="true"
    :show-dependencies="true"
  />
</template>