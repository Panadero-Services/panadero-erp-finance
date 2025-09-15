<!--
  ERP Finance Page
  @version 1.0.1
  @date 19-Aug-2025
  @description ERP framework page component for ERP.Finance
-->
<script setup>
import { GenericInfoSection } from 'panadero-shared-components';
import { useFinanceStore } from '../../stores/financeStore';
import { useInfoBoxes } from '../../composables/useInfoBoxes';

const store = useFinanceStore();
const { 
  moduleDescription, 
  versionUpdates, 
  packageTables, 
  sharedEntities,
  toggleInfoBox,
  navigateToEntity
} = useInfoBoxes();

const financeConfig = {
  name: 'Finance',
  title: 'ERP Finance ModuleByyGeneric',
  description: 'Comprehensive financial management system',
  icon: 'fas fa-dollar-sign',
  color: 'green'
};
</script>

<template>
  <GenericInfoSection
    :package-config="financeConfig"
    :module-description="moduleDescription"
    :version-updates="versionUpdates"
    :package-tables="packageTables"
    :shared-entities="sharedEntities"
    :table-name-prefix="'finance_'"
    :table-name-replacement="'_'"
    :on-toggle-info-box="toggleInfoBox"
    :on-navigate-to-entity="navigateToEntity"
    :show-version-updates="true"
    :show-package-tables="true"
    :show-shared-entities="true"
    :show-dependencies="true"
  />
</template>