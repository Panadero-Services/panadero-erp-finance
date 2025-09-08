<script setup>
import { ref, computed, onMounted } from 'vue';
import { useHrStore } from '../stores/hrStore';
import { useScaling } from '../../../shared/composables/useScaling.js';

// UI Components
import HrButton from './ui/HrButton.vue';
import HrInput from './ui/HrInput.vue';
import HrDropdown from './ui/HrDropdown.vue';
import HrValueCard from './ui/HrValueCard.vue';

const store = useHrStore();
const { fontSizes, scalingStyles } = useScaling();

// State
const isLoading = ref(false);
const selectedReportType = ref('');
const reportPeriod = ref('30d');
const generatedReports = ref([]);

// Computed
const reportTypes = [
  { value: 'employee_summary', label: 'Employee Summary' },
  { value: 'recruitment_analytics', label: 'Recruitment Analytics' },
  { value: 'department_performance', label: 'Department Performance' },
  { value: 'turnover_analysis', label: 'Turnover Analysis' },
  { value: 'salary_analysis', label: 'Salary Analysis' },
  { value: 'performance_reviews', label: 'Performance Reviews' }
];

const periodOptions = [
  { value: '7d', label: 'Last 7 days' },
  { value: '30d', label: 'Last 30 days' },
  { value: '90d', label: 'Last 90 days' },
  { value: '1y', label: 'Last year' },
  { value: 'custom', label: 'Custom range' }
];

const hrMetrics = computed(() => {
  return {
    totalEmployees: store.getTotalEmployees,
    activeEmployees: store.getActiveEmployees.length,
    totalVacancies: store.getActiveVacancies.length,
    totalApplications: store.getTotalApplications,
    departments: store.departments.length,
    avgSalary: store.employees.reduce((sum, emp) => sum + emp.salary, 0) / store.employees.length,
    retentionRate: 94.2,
    timeToHire: 21
  };
});

// Actions
const handleGenerateReport = async () => {
  if (!selectedReportType.value) return;
  
  isLoading.value = true;
  try {
    const report = await store.generateHrReport(selectedReportType.value, {
      period: reportPeriod.value
    });
    
    generatedReports.value.unshift({
      ...report,
      reportType: selectedReportType.value,
      period: reportPeriod.value,
      generatedAt: new Date().toISOString()
    });
  } catch (error) {
    console.error('Error generating report:', error);
  } finally {
    isLoading.value = false;
  }
};

const handleExportReport = (report) => {
  // Implementation for exporting report
  console.log('Export report:', report);
};

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(amount);
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleString();
};

onMounted(() => {
  store.loadSettings();
});
</script>

<template>
  <div class="hr-reporting">
    <!-- Header -->
    <div class="mb-8">
      <div class="flex items-center justify-between">
        <div>
          <h1 :style="{ fontSize: `${fontSizes.heading}` }" class="font-bold text-gray-900 dark:text-white mb-2">
            HR Reports & Analytics
          </h1>
          <p :style="{ fontSize: `${fontSizes.base}px` }" class="text-gray-600 dark:text-gray-400">
            Generate comprehensive reports and analytics for human resources management
          </p>
        </div>
        <div class="flex items-center space-x-3">
          <HrButton
            variant="secondary"
            size="md"
          >
            <i class="fas fa-download mr-2"></i>
            Export All
          </HrButton>
        </div>
      </div>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <HrValueCard
        title="Total Employees"
        :value="hrMetrics.totalEmployees"
        subtitle="Active workforce"
        icon="fas fa-users"
        color="blue"
      />
      <HrValueCard
        title="Open Positions"
        :value="hrMetrics.totalVacancies"
        subtitle="Active vacancies"
        icon="fas fa-briefcase"
        color="orange"
      />
      <HrValueCard
        title="Applications"
        :value="hrMetrics.totalApplications"
        subtitle="Total received"
        icon="fas fa-file-alt"
        color="purple"
      />
      <HrValueCard
        title="Retention Rate"
        :value="`${hrMetrics.retentionRate}%`"
        subtitle="Employee retention"
        icon="fas fa-heart"
        color="green"
      />
    </div>

    <!-- Report Generator -->
    <div class="mb-8 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
      <h2 :style="{ fontSize: `${fontSizes.h2}` }" class="font-semibold text-gray-900 dark:text-white mb-4">
        Generate Report
      </h2>
      
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <HrDropdown
          v-model="selectedReportType"
          :options="reportTypes"
          label="Report Type"
          placeholder="Select report type"
        />
        <HrDropdown
          v-model="reportPeriod"
          :options="periodOptions"
          label="Time Period"
          placeholder="Select time period"
        />
        <div class="flex items-end">
          <HrButton
            @click="handleGenerateReport"
            variant="primary"
            size="md"
            :loading="isLoading"
            :disabled="!selectedReportType"
            class="w-full"
          >
            <i class="fas fa-chart-bar mr-2"></i>
            Generate Report
          </HrButton>
        </div>
      </div>
    </div>

    <!-- Generated Reports -->
    <div v-if="generatedReports.length > 0" class="mb-8">
      <h2 :style="{ fontSize: `${fontSizes.h2}` }" class="font-semibold text-gray-900 dark:text-white mb-4">
        Generated Reports
      </h2>
      
      <div class="space-y-4">
        <div
          v-for="report in generatedReports"
          :key="report.id"
          class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6"
        >
          <div class="flex items-center justify-between mb-4">
            <div>
              <h3 :style="{ fontSize: `${fontSizes.h3}` }" class="font-semibold text-gray-900 dark:text-white">
                {{ reportTypes.find(rt => rt.value === report.reportType)?.label }}
              </h3>
              <p :style="{ fontSize: `${fontSizes.xs}` }" class="text-gray-500 dark:text-gray-400">
                Generated {{ formatDate(report.generatedAt) }} • {{ report.period.toUpperCase() }}
              </p>
            </div>
            <div class="flex items-center space-x-2">
              <HrButton
                @click="handleExportReport(report)"
                variant="secondary"
                size="sm"
              >
                <i class="fas fa-download mr-1"></i>
                Export
              </HrButton>
              <HrButton
                variant="ghost"
                size="sm"
              >
                <i class="fas fa-eye mr-1"></i>
                View
              </HrButton>
            </div>
          </div>
          
          <!-- Report Preview -->
          <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div class="text-center">
                <div :style="{ fontSize: `${fontSizes.h2}` }" class="font-bold text-gray-900 dark:text-white">
                  {{ hrMetrics.totalEmployees }}
                </div>
                <div :style="{ fontSize: `${fontSizes.xs}` }" class="text-gray-500 dark:text-gray-400">
                  Total Employees
                </div>
              </div>
              <div class="text-center">
                <div :style="{ fontSize: `${fontSizes.h2}` }" class="font-bold text-gray-900 dark:text-white">
                  {{ formatCurrency(hrMetrics.avgSalary) }}
                </div>
                <div :style="{ fontSize: `${fontSizes.xs}` }" class="text-gray-500 dark:text-gray-400">
                  Average Salary
                </div>
              </div>
              <div class="text-center">
                <div :style="{ fontSize: `${fontSizes.h2}` }" class="font-bold text-gray-900 dark:text-white">
                  {{ hrMetrics.timeToHire }} days
                </div>
                <div :style="{ fontSize: `${fontSizes.xs}` }" class="text-gray-500 dark:text-gray-400">
                  Time to Hire
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <!-- Department Distribution -->
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 :style="{ fontSize: `${fontSizes.h3}` }" class="font-semibold text-gray-900 dark:text-white mb-4">
          Department Distribution
        </h3>
        
        <div class="space-y-4">
          <div
            v-for="dept in store.departments"
            :key="dept.id"
            class="flex items-center justify-between"
          >
            <div class="flex items-center space-x-3">
              <div class="w-4 h-4 bg-blue-500 rounded-full"></div>
              <span :style="{ fontSize: `${fontSizes.label}` }" class="text-gray-900 dark:text-white">
                {{ dept.name }}
              </span>
            </div>
            <div class="flex items-center space-x-2">
              <div class="w-32 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                <div
                  class="bg-blue-500 h-2 rounded-full"
                  :style="{ width: `${(dept.employeeCount / 20) * 100}%` }"
                ></div>
              </div>
              <span :style="{ fontSize: `${fontSizes.xs}` }" class="text-gray-500 dark:text-gray-400 w-8">
                {{ dept.employeeCount }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Recruitment Pipeline -->
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 :style="{ fontSize: `${fontSizes.h3}` }" class="font-semibold text-gray-900 dark:text-white mb-4">
          Recruitment Pipeline
        </h3>
        
        <div class="space-y-4">
          <div class="flex items-center justify-between">
            <span :style="{ fontSize: `${fontSizes.label}` }" class="text-gray-600 dark:text-gray-400">
              Submitted
            </span>
            <span :style="{ fontSize: `${fontSizes.subtitle}` }" class="font-semibold text-gray-900 dark:text-white">
              {{ store.applications.filter(app => app.status === 'submitted').length }}
            </span>
          </div>
          <div class="flex items-center justify-between">
            <span :style="{ fontSize: `${fontSizes.label}` }" class="text-gray-600 dark:text-gray-400">
              Screening
            </span>
            <span :style="{ fontSize: `${fontSizes.subtitle}` }" class="font-semibold text-gray-900 dark:text-white">
              {{ store.applications.filter(app => app.status === 'screening').length }}
            </span>
          </div>
          <div class="flex items-center justify-between">
            <span :style="{ fontSize: `${fontSizes.label}` }" class="text-gray-600 dark:text-gray-400">
              Interview
            </span>
            <span :style="{ fontSize: `${fontSizes.subtitle}` }" class="font-semibold text-gray-900 dark:text-white">
              {{ store.applications.filter(app => app.status === 'interview').length }}
            </span>
          </div>
          <div class="flex items-center justify-between">
            <span :style="{ fontSize: `${fontSizes.label}` }" class="text-gray-600 dark:text-gray-400">
              Offer
            </span>
            <span :style="{ fontSize: `${fontSizes.subtitle}` }" class="font-semibold text-gray-900 dark:text-white">
              {{ store.applications.filter(app => app.status === 'offer').length }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.hr-reporting {
  padding: 0;
}
</style>
