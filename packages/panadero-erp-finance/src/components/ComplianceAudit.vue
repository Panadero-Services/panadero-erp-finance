<script setup>
import { ref, onMounted } from 'vue';
import { useCompliance } from '../composables/useCompliance';
import { useInvoiceApi } from '../composables/useInvoiceApi';
import StatusBadge from './ui/StatusBadge.vue';
import FinanceValueCard from './ui/FinanceValueCard.vue';
import FinanceButton from './ui/FinanceButton.vue';
import FinanceDropdown from './ui/FinanceDropdown.vue';
import { useScaling } from '../../../shared/composables/useScaling.js'

const { fontSizes, scalingStyles, spacing } = useScaling()

// Filters state
const filters = ref({
  auditType: '',
  status: ''
});

// Dropdown options
const auditTypeOptions = [
  { label: 'All Types', value: '' },
  { label: 'Financial', value: 'financial' },
  { label: 'Operational', value: 'operational' },
  { label: 'Compliance', value: 'compliance' },
  { label: 'Internal', value: 'internal' },
  { label: 'External', value: 'external' }
];

const statusOptions = [
  { label: 'All Statuses', value: '' },
  { label: 'Success', value: 'success' },
  { label: 'Failed', value: 'failed' },
  { label: 'Pending', value: 'pending' },
  { label: 'In Progress', value: 'in_progress' }
];

// Mock data for demonstration
const totalAudits = ref(25);
const passedAudits = ref(20);
const failedAudits = ref(3);
const pendingAudits = ref(2);
const filteredAuditLogs = ref([
  { id: 1, action: 'User Login', user: 'admin@company.com', timestamp: '2025-01-15 10:30:00', ip_address: '192.168.1.100', status: 'success' },
  { id: 2, action: 'Invoice Created', user: 'finance@company.com', timestamp: '2025-01-15 09:15:00', ip_address: '192.168.1.101', status: 'success' }
]);

// Missing functions
function handleNewAudit() {
  // TODO: Implement new audit functionality
  console.log('New audit clicked');
}

function exportAudits() {
  // TODO: Implement export functionality
  console.log('Export audits clicked');
}

function refreshData() {
  // TODO: Implement refresh functionality
  console.log('Refresh data clicked');
}

function formatDate(date) {
  return new Date(date).toLocaleDateString();
}

function formatDateTime(dateTime) {
  return new Date(dateTime).toLocaleString();
}

// Additional missing functions
function viewAuditLog(log) {
  // TODO: Implement view functionality
  console.log('View audit log:', log);
}

function deleteAuditLog(logId) {
  if (confirm('Are you sure you want to delete this audit log?')) {
    // TODO: Implement delete functionality
    console.log('Delete audit log:', logId);
    alert(`Deleted audit log with ID: ${logId}`);
  }
}
</script>
<template>
  <div class="compliance-audit dark:bg-gray-900">
    <div class="flex items-center justify-between mb-6">
      <h2 :style="scalingStyles.titleFontSize" class="font-semibold dark:text-white">Compliance & Audit</h2>
      <div :style="scalingStyles.buttonGap" class="flex items-center">
        <!-- Filters -->
        <div class="flex items-center gap-2 mr-4">
          <FinanceDropdown
            v-model="filters.auditType"
            :options="auditTypeOptions"
            option-label="label"
            option-value="value"
            placeholder="All Audit Types"
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
            @click="handleNewAudit"
            variant="primary"
            size="normal"
            icon-left="fas fa-plus"
          >
            New Audit
          </FinanceButton>
          <FinanceButton
            @click="exportAudits"
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
    <div :style="scalingStyles.sectionMargin" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
      <FinanceValueCard title="Total Audits" :value="totalAudits" rows="2-row" format="number" color="neutral" icon="fas fa-clipboard-list" />

      <FinanceValueCard title="Passed" :value="passedAudits" rows="2-row" format="number" color="positive" icon="fas fa-check-circle" />

      <FinanceValueCard title="Failed" :value="failedAudits" rows="2-row" format="number" color="auto" :max-good="1" :max-warning="5" icon="fas fa-times-circle" />

      <FinanceValueCard title="Pending" :value="pendingAudits" rows="2-row" format="number" color="auto" :max-good="3" :max-warning="8" icon="fas fa-clock" />
    </div>

    <!-- Audit Logs Table -->
    <div :style="scalingStyles.sectionMargin" class="bg-white dark:bg-gray-800 rounded-lg shadow border dark:border-gray-700">
      <div :style="scalingStyles.cardPadding" class="border-b dark:border-gray-700">
        <h3 :style="scalingStyles.subtitleFontSize" class="font-semibold text-gray-700 dark:text-gray-300">Audit Logs</h3>
      </div>
      <div class="overflow-x-auto">
        <table :style="scalingStyles.borderRadius" class="w-full border-collapse">
        <thead>
            <tr class="bg-gray-50 dark:bg-gray-700">
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200 text-left">Action</th>
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200 text-left">User</th>
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200 text-left">Timestamp</th>
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200 text-left">IP Address</th>
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200 text-left">Status</th>
              <th :style="[scalingStyles.tableHeader, scalingStyles.paddingScale]" class="border dark:border-gray-600 dark:text-gray-200 text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
            <tr v-for="log in filteredAuditLogs" :key="log.id" class="dark:text-gray-100 dark:border-gray-600" :style="scalingStyles.tableRowHeight">
              <td :style="[scalingStyles.textFontSize, scalingStyles.paddingScale]" class="border dark:border-gray-600">{{ log.action }}</td>
              <td :style="[scalingStyles.textFontSize, scalingStyles.paddingScale]" class="border dark:border-gray-600">{{ log.user }}</td>
              <td :style="[scalingStyles.textFontSize, scalingStyles.paddingScale]" class="border dark:border-gray-600">{{ formatDateTime(log.timestamp) }}</td>
              <td :style="[scalingStyles.textFontSize, scalingStyles.paddingScale]" class="border dark:border-gray-600">{{ log.ip_address }}</td>
              <td :style="[scalingStyles.textFontSize, scalingStyles.paddingScale]" class="border dark:border-gray-600">
                <StatusBadge :status="log.status" />
              </td>
              <td :style="scalingStyles.paddingScale" class="border dark:border-gray-600">
                <div class="flex gap-2">
                  <button 
                    @click="viewAuditLog(log)" 
                    class="p-2 rounded-md hover:bg-blue-50 dark:hover:bg-blue-900/20 text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors"
                    title="View Details"
                  >
                    <i class="fas fa-eye" :style="scalingStyles.iconSize"></i>
                  </button>
                  <button 
                    @click="deleteAuditLog(log.id)" 
                    class="p-2 rounded-md hover:bg-red-50 dark:hover:bg-red-900/20 text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 transition-colors"
                    title="Delete Log"
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

<style scoped>
/* CashFlow-style summary cards */
.cf-summary {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
  margin-bottom: 2rem;
}

.summary-card {
  padding: 1rem;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  text-align: center;
  @apply dark:bg-gray-800 dark:shadow-gray-900/50;
}

.summary-card h3 {
  font-size: 0.875rem;
  font-weight: 500;
  color: #6b7280;
  margin-bottom: 0.5rem;
  @apply dark:text-gray-300;
}

.summary-card p {
  font-size: 1.5rem;
  font-weight: 600;
  margin: 0;
  @apply dark:text-white;
}

.cf-totals {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
  margin-bottom: 2rem;
}

.total-card {
  padding: 1rem;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  text-align: center;
  @apply dark:bg-gray-800 dark:shadow-gray-900/50;
}

.total-card h4 {
  font-size: 0.875rem;
  font-weight: 500;
  color: #6b7280;
  margin-bottom: 0.5rem;
  @apply dark:text-gray-300;
}

.total-card p {
  font-size: 1.125rem;
  font-weight: 600;
  margin: 0;
}

/* Color classes */
.positive {
  color: #059669;
  @apply dark:text-green-400;
}

.warning {
  color: #d97706;
  @apply dark:text-yellow-400;
}

.amount {
  color: #111827;
  @apply dark:text-white;
}
</style>