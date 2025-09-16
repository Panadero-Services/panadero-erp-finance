<!--
  ERP Inventory Page
  @version 1.0.12
  @date 16-Sep-2025
  @description ERP framework page component for ERP.Inventory
-->
<script setup>
import { GenericInfoSection } from 'panadero-shared-components';
import { useInventoryStore } from '../../stores/inventoryStore';
import { useInfoBoxes } from '../../composables/useInfoBoxes';

const store = useInventoryStore();
const { 
  moduleDescription, 
  versionUpdates, 
  packageTables, 
  sharedEntities,
  toggleInfoBox,
  navigateToEntity
} = useInfoBoxes();

const inventoryConfig = {
  name: 'Inventory',
  title: 'ERP Inventory ModuleByyGeneric',
  description: 'Comprehensive inventory management system',
  icon: 'fas fa-boxes',
  color: 'blue'
};
</script>

<template>
  <GenericInfoSection
    :package-config="inventoryConfig"
    :module-description="moduleDescription"
    :version-updates="versionUpdates"
    :package-tables="packageTables"
    :shared-entities="sharedEntities"
    :table-name-prefix="'inventory_'"
    :table-name-replacement="'_'"
    :on-toggle-info-box="toggleInfoBox"
    :on-navigate-to-entity="navigateToEntity"
    :show-version-updates="true"
    :show-package-tables="true"
    :show-shared-entities="true"
    :show-dependencies="true"
  />
</template>

