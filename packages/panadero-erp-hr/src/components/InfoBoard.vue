<script setup>
import { ref, computed, onMounted } from 'vue';
import { useHrStore } from '../stores/hrStore';
import { useScaling } from '../../../shared/composables/useScaling.js';

// UI Components
import HrValueCard from './ui/HrValueCard.vue';
import HrButton from './ui/HrButton.vue';
import StatusBadge from './ui/StatusBadge.vue';

const store = useHrStore();
const { fontSizes, scalingStyles } = useScaling();

// State
const isLoading = ref(false);

// Computed
const totalEmployees = computed(() => store.getTotalEmployees);
const activeEmployees = computed(() => store.getActiveEmployees.length);
const totalVacancies = computed(() => store.getActiveVacancies.length);
const totalApplications = computed(() => store.getTotalApplications);
const pendingApplications = computed(() => store.getPendingApplications.length);

const recentHires = computed(() => {
  return store.employees
    .filter(emp => emp.status === 'active')
    .sort((a, b) => new Date(b.hireDate) - new Date(a.hireDate))
    .slice(0, 5);
});

const topDepartments = computed(() => {
  return store.departments
    .sort((a, b) => b.employeeCount - a.employeeCount)
    .slice(0, 3);
});

const recentApplications = computed(() => {
  return store.applications
    .sort((a, b) => new Date(b.appliedDate) - new Date(a.appliedDate))
    .slice(0, 5);
});

// Actions
const handleRefresh = async () => {
  isLoading.value = true;
  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1000));
    console.log('HR data refreshed');
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  store.loadSettings();
});
</script>

<template>
  <div class="hr-info-board">
    <!-- Header -->
    <div class="mb-8">
      <div class="flex items-center justify-between">
        <div>
          <h1 :style="{ fontSize: `${fontSizes.heading}` }" class="font-bold text-gray-900 dark:text-white mb-2">
            HR Management Overview
          </h1>
          <p :style="{ fontSize: `${fontSizes.base}px` }" class="text-gray-600 dark:text-gray-400">
            Comprehensive human resources management and recruitment system
          </p>
        </div>
        <div class="flex items-center space-x-3">
          <HrButton
            @click="handleRefresh"
            variant="secondary"
            size="md"
            :loading="isLoading"
          >
            <i class="fas fa-sync-alt mr-2"></i>
            Refresh Data
          </HrButton>
        </div>
      </div>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <HrValueCard
        title="Total Employees"
        :value="totalEmployees"
        subtitle="Active workforce"
        icon="fas fa-users"
        color="blue"
        :trend="{ direction: 'up', value: '+12%', period: 'vs last month' }"
      />
      <HrValueCard
        title="Active Vacancies"
        :value="totalVacancies"
        subtitle="Open positions"
        icon="fas fa-briefcase"
        color="orange"
        :trend="{ direction: 'up', value: '+3', period: 'new this week' }"
      />
      <HrValueCard
        title="Total Applications"
        :value="totalApplications"
        subtitle="Job applications"
        icon="fas fa-file-alt"
        color="purple"
        :trend="{ direction: 'up', value: '+25%', period: 'vs last month' }"
      />
      <HrValueCard
        title="Pending Reviews"
        :value="pendingApplications"
        subtitle="Awaiting review"
        icon="fas fa-clock"
        color="yellow"
        :trend="{ direction: 'down', value: '-5', period: 'this week' }"
      />
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <!-- Recent Hires -->
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-4">
          <h2 :style="{ fontSize: `${fontSizes.h2}` }" class="font-semibold text-gray-900 dark:text-white">
            Recent Hires
          </h2>
          <HrButton variant="ghost" size="sm">
            View All
            <i class="fas fa-arrow-right ml-1"></i>
          </HrButton>
        </div>
        
        <div class="space-y-4">
          <div
            v-for="employee in recentHires"
            :key="employee.id"
            class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"
          >
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 bg-green-100 dark:bg-green-900/20 rounded-full flex items-center justify-center">
                <i class="fas fa-user text-green-600 dark:text-green-400"></i>
              </div>
              <div>
                <p :style="{ fontSize: `${fontSizes.subtitle}` }" class="font-medium text-gray-900 dark:text-white">
                  {{ employee.firstName }} {{ employee.lastName }}
                </p>
                <p :style="{ fontSize: `${fontSizes.xs}` }" class="text-gray-600 dark:text-gray-400">
                  {{ employee.position }} • {{ employee.department }}
                </p>
              </div>
            </div>
            <div class="text-right">
              <p :style="{ fontSize: `${fontSizes.xs}` }" class="text-gray-500 dark:text-gray-400">
                Hired {{ new Date(employee.hireDate).toLocaleDateString() }}
              </p>
              <StatusBadge status="active" text="Active" />
            </div>
          </div>
        </div>
      </div>

      <!-- Top Departments -->
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-4">
          <h2 :style="{ fontSize: `${fontSizes.h2}` }" class="font-semibold text-gray-900 dark:text-white">
            Top Departments
          </h2>
          <HrButton variant="ghost" size="sm">
            View All
            <i class="fas fa-arrow-right ml-1"></i>
          </HrButton>
        </div>
        
        <div class="space-y-4">
          <div
            v-for="(dept, index) in topDepartments"
            :key="dept.id"
            class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"
          >
            <div class="flex items-center space-x-3">
              <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/20 rounded-full flex items-center justify-center">
                <span :style="{ fontSize: `${fontSizes.label}` }" class="font-bold text-blue-600 dark:text-blue-400">
                  {{ index + 1 }}
                </span>
              </div>
              <div>
                <p :style="{ fontSize: `${fontSizes.subtitle}` }" class="font-medium text-gray-900 dark:text-white">
                  {{ dept.name }}
                </p>
                <p :style="{ fontSize: `${fontSizes.xs}` }" class="text-gray-600 dark:text-gray-400">
                  {{ dept.employeeCount }} employees
                </p>
              </div>
            </div>
            <div class="text-right">
              <p :style="{ fontSize: `${fontSizes.label}` }" class="font-semibold text-gray-900 dark:text-white">
                ${{ dept.budget.toLocaleString() }}
              </p>
              <p :style="{ fontSize: `${fontSizes.xs}` }" class="text-gray-500 dark:text-gray-400">
                Budget
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Applications -->
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-4">
          <h2 :style="{ fontSize: `${fontSizes.h2}` }" class="font-semibold text-gray-900 dark:text-white">
            Recent Applications
          </h2>
          <HrButton variant="ghost" size="sm">
            View All
            <i class="fas fa-arrow-right ml-1"></i>
          </HrButton>
        </div>
        
        <div class="space-y-4">
          <div
            v-for="application in recentApplications"
            :key="application.id"
            class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"
          >
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/20 rounded-full flex items-center justify-center">
                <i class="fas fa-user-plus text-purple-600 dark:text-purple-400"></i>
              </div>
              <div>
                <p :style="{ fontSize: `${fontSizes.subtitle}` }" class="font-medium text-gray-900 dark:text-white">
                  {{ application.firstName }} {{ application.lastName }}
                </p>
                <p :style="{ fontSize: `${fontSizes.xs}` }" class="text-gray-600 dark:text-gray-400">
                  {{ application.experience }} experience
                </p>
              </div>
            </div>
            <div class="text-right">
              <p :style="{ fontSize: `${fontSizes.xs}` }" class="text-gray-500 dark:text-gray-400">
                {{ new Date(application.appliedDate).toLocaleDateString() }}
              </p>
              <StatusBadge :status="application.status" />
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h2 :style="{ fontSize: `${fontSizes.h2}` }" class="font-semibold text-gray-900 dark:text-white mb-4">
          Quick Actions
        </h2>
        
        <div class="grid grid-cols-2 gap-4">
          <HrButton variant="primary" size="md" class="w-full">
            <i class="fas fa-user-plus mr-2"></i>
            Add Employee
          </HrButton>
          <HrButton variant="secondary" size="md" class="w-full">
            <i class="fas fa-briefcase mr-2"></i>
            Create Vacancy
          </HrButton>
          <HrButton variant="success" size="md" class="w-full">
            <i class="fas fa-chart-bar mr-2"></i>
            Generate Report
          </HrButton>
          <HrButton variant="warning" size="md" class="w-full">
            <i class="fas fa-cog mr-2"></i>
            HR Settings
          </HrButton>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.hr-info-board {
  padding: 0;
}
</style>
