<script setup>
import { ref, onMounted } from 'vue';
import { useFixedAssets } from '../composables/useFixedAssets';
import { useInvoiceApi } from '../composables/useInvoiceApi';
import StatusBadge from './ui/StatusBadge.vue';
import FinanceValueCard from './ui/FinanceValueCard.vue';
import FinanceButton from './ui/FinanceButton.vue';
import FinanceDropdown from './ui/FinanceDropdown.vue';
import { useScaling } from '../../../shared/composables/useScaling.js'

const { fontSizes, scalingStyles, spacing } = useScaling()

// Filters state
const filters = ref({
  category: '',
  status: ''
});

// Dropdown options
const categoryOptions = [
  { label: 'All Categories', value: '' },
  { label: 'Building', value: 'building' },
  { label: 'Vehicle', value: 'vehicle' },
  { label: 'Equipment', value: 'equipment' },
  { label: 'Furniture', value: 'furniture' },
  { label: 'Technology', value: 'technology' }
];

const statusOptions = [
  { label: 'All Statuses', value: '' },
  { label: 'Active', value: 'active' },
  { label: 'Disposed', value: 'disposed' },
  { label: 'Maintenance', value: 'maintenance' }
];

const depreciationMethodOptions = [
  { label: 'Straight Line', value: 'straight_line' },
  { label: 'Declining Balance', value: 'declining_balance' },
  { label: 'Units of Production', value: 'units_production' }
];

// Mock data for demonstration
const totalAssets = ref(150000);
const totalDepreciation = ref(25000);
const netBookValue = ref(125000);
const filteredAssets = ref([
  { id: 1, name: 'Office Building', category: 'Building', cost: 100000, acquired_at: '2020-01-01', method: 'straight_line', useful_life_months: 360, accumulated_depreciation: 15000, status: 'active' },
  { id: 2, name: 'Company Vehicle', category: 'Vehicle', cost: 25000, acquired_at: '2022-06-01', method: 'straight_line', useful_life_months: 60, accumulated_depreciation: 5000, status: 'active' }
]);

// Missing functions
function handleNewAsset() {
  // TODO: Implement new asset functionality
  console.log('New asset clicked');
}

function exportAssets() {
  // TODO: Implement export functionality
  console.log('Export assets clicked');
}

function refreshData() {
  // TODO: Implement refresh functionality
  console.log('Refresh data clicked');
}

function formatDate(date) {
  return new Date(date).toLocaleDateString();
}

function formatCurrency(amount) {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(amount);
}

// Additional missing functions
function editAsset(asset) {
  // TODO: Implement edit functionality
  console.log('Edit asset:', asset);
}

function deleteAsset(assetId) {
  if (confirm('Are you sure you want to delete this asset?')) {
    // TODO: Implement delete functionality
    console.log('Delete asset:', assetId);
    alert(`Deleted asset with ID: ${assetId}`);
  }
}
</script>
<template>
  <div class="fixed-assets dark:bg-gray-900">
    <div class="flex items-center justify-between mb-6">
      <h2 :style="scalingStyles.titleFontSize" class="font-semibold dark:text-white">Fixed Assets</h2>
      <div :style="scalingStyles.buttonGap" class="flex items-center">
        <!-- Filters -->
        <div class="flex items-center gap-2 mr-4">
          <FinanceDropdown
            v-model="filters.category"
            :options="categoryOptions"
            option-label="label"
            option-value="value"
            placeholder="All Categories"
            variant="ghost"
            size="normal"
          />
          <FinanceDropdown
            v-model="filters.status"
            :options="statusOptions"
            option-label="label"
            option-value="value"
            placeholder="All Statuses"
            variant="ghost"
            size="normal"
          />
        </div>
        <!-- Buttons -->
        <div :style="scalingStyles.buttonGap" class="flex items-center">
          <FinanceButton
            @click="handleNewAsset"
            variant="primary"
            size="normal"
            icon-left="fas fa-plus"
          >
            New Asset
          </FinanceButton>
          <FinanceButton
            @click="exportAssets"
            variant="success"
            size="normal"
            icon-left="fas fa-download"
          >
            Export
          </FinanceButton>
          <FinanceButton
            @click="refreshData"
            variant="info"
            size="normal"
            icon-left="fas fa-refresh"
          >
            Refresh
          </FinanceButton>
        </div>
      </div>
    </div>

    <!-- Summary Cards -->
    <div :style="scalingStyles.sectionMargin" class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <FinanceValueCard title="Total Assets" :value="totalAssets" rows="2-row" format="currency" color="neutral" icon="fas fa-building" />

      <FinanceValueCard title="Total Depreciation" :value="totalDepreciation" rows="2-row" format="currency" color="warning" icon="fas fa-chart-line-down" />

      <FinanceValueCard title="Net Book Value" :value="netBookValue" rows="2-row" format="currency" color="positive" icon="fas fa-calculator" />
    </div>

    <!-- Assets Table -->
    <div :style="scalingStyles.sectionMargin" class="bg-white dark:bg-gray-800 rounded-lg shadow border dark:border-gray-700">
      <div :style="scalingStyles.cardPadding" class="border-b dark:border-gray-700">
        <h3 :style="scalingStyles.subtitleFontSize" class="font-semibold text-gray-700 dark:text-gray-300">Assets List</h3>
      </div>
      <div class="overflow-x-auto">
        <table :style="scalingStyles.borderRadius" class="w-full border-collapse">
        <thead>
            <tr class="bg-gray-50 dark:bg-gray-700">
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200 text-left">Asset Name</th>
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200 text-left">Category</th>
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200 text-left">Status</th>
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200 text-left">Cost</th>
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200 text-left">Acquired</th>
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200 text-left">Depreciation</th>
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200 text-left">Net Value</th>
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200 text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
            <tr v-for="asset in filteredAssets" :key="asset.id" class="dark:text-gray-100 dark:border-gray-600" :style="scalingStyles.tableRowHeight">
              <td :style="[scalingStyles.textFontSize, scalingStyles.paddingScale]" class="border dark:border-gray-600">{{ asset.name }}</td>
              <td :style="[scalingStyles.textFontSize, scalingStyles.paddingScale]" class="border dark:border-gray-600">{{ asset.category }}</td>
              <td :style="scalingStyles.paddingScale" class="border dark:border-gray-600">
                <StatusBadge :status="asset.status" />
              </td>
              <td :style="[scalingStyles.textFontSize, scalingStyles.paddingScale]" class="border dark:border-gray-600">{{ formatCurrency(asset.cost) }}</td>
              <td :style="[scalingStyles.textFontSize, scalingStyles.paddingScale]" class="border dark:border-gray-600">{{ formatDate(asset.acquired_at) }}</td>
              <td :style="[scalingStyles.textFontSize, scalingStyles.paddingScale]" class="border dark:border-gray-600">{{ formatCurrency(asset.accumulated_depreciation) }}</td>
              <td :style="[scalingStyles.textFontSize, scalingStyles.paddingScale]" class="border dark:border-gray-600">{{ formatCurrency(asset.cost - asset.accumulated_depreciation) }}</td>
              <td :style="scalingStyles.paddingScale" class="border dark:border-gray-600">
                <div class="flex gap-2">
                  <button 
                    @click="editAsset(asset)" 
                    class="p-2 rounded-md hover:bg-blue-50 dark:hover:bg-blue-900/20 text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors"
                    title="Edit Asset"
                  >
                    <i class="fas fa-edit" :style="scalingStyles.iconSize"></i>
                  </button>
                  <button 
                    @click="deleteAsset(asset.id)" 
                    class="p-2 rounded-md hover:bg-red-50 dark:hover:bg-red-900/20 text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 transition-colors"
                    title="Delete Asset"
                  >
                    <i class="fas fa-trash" :style="scalingStyles.iconSize"></i>
                  </button>
                </div>
              </td>
          </tr>
        </tbody>
      </table>
      </div>
    </div>
  </div>
</template>