<script setup>
import { ref, computed, onMounted } from 'vue';
import { useHrStore } from '../../stores/hrStore';
import { useScaling } from '../../../../shared/composables/useScaling.js';

// UI Components
import HrValueCard from '../ui/HrValueCard.vue';
import HrButton from '../ui/HrButton.vue';
import StatusBadge from '../ui/StatusBadge.vue';

const store = useHrStore();
const { fontSizes, scalingStyles } = useScaling();

// State
const isLoading = ref(false);
const selectedPeriod = ref('30d');

// Computed
const totalEmployees = computed(() => store.getTotalEmployees);
const activeEmployees = computed(() => store.getActiveEmployees.length);
const totalVacancies = computed(() => store.getActiveVacancies.length);
const totalApplications = computed(() => store.getTotalApplications);
const pendingApplications = computed(() => store.getPendingApplications.length);

const departmentStats = computed(() => {
  return store.departments.map(dept => ({
    ...dept,
    utilizationRate: Math.round((dept.employeeCount / 20) * 100) // Assuming max 20 employees per dept
  }));
});

const recentActivity = computed(() => {
  const activities = [
    {
      id: 1,
      type: 'hire',
      message: 'Sarah Johnson joined as Senior Developer',
      timestamp: '2025-08-30T10:00:00Z',
      icon: 'fas fa-user-plus',
      color: 'text-green-600'
    },
    {
      id: 2,
      type: 'application',
      message: 'New application for Marketing Specialist position',
      timestamp: '2025-08-30T09:30:00Z',
      icon: 'fas fa-file-alt',
      color: 'text-blue-600'
    },
    {
      id: 3,
      type: 'review',
      message: 'Performance review completed for John Smith',
      timestamp: '2025-08-29T16:00:00Z',
      icon: 'fas fa-star',
      color: 'text-yellow-600'
    },
    {
      id: 4,
      type: 'vacancy',
      message: 'New vacancy posted: Senior Frontend Developer',
      timestamp: '2025-08-29T14:00:00Z',
      icon: 'fas fa-briefcase',
      color: 'text-purple-600'
    }
  ];
  
  return activities.sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));
});

const upcomingEvents = computed(() => {
  return [
    {
      id: 1,
      title: 'Team Meeting',
      date: '2025-09-02T10:00:00Z',
      type: 'meeting',
      attendees: 8
    },
    {
      id: 2,
      title: 'Performance Review - Michael Brown',
      date: '2025-09-03T14:00:00Z',
      type: 'review',
      attendees: 2
    },
    {
      id: 3,
      title: 'Interview - Alex Thompson',
      date: '2025-09-04T11:00:00Z',
      type: 'interview',
      attendees: 3
    }
  ];
});

// Actions
const handleRefresh = async () => {
  isLoading.value = true;
  try {
    await new Promise(resolve => setTimeout(resolve, 1000));
    console.log('Dashboard data refreshed');
  } finally {
    isLoading.value = false;
  }
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

onMounted(() => {
  store.loadSettings();
});
</script>

<template>
  <div class="hr-dashboard">
    <!-- Header -->
    <div class="mb-8">
      <div class="flex items-center justify-between">
        <div>
          <h1 :style="{ fontSize: `${fontSizes.heading}` }" class="font-bold text-gray-900 dark:text-white mb-2">
            HR Dashboard
          </h1>
          <p :style="{ fontSize: `${fontSizes.base}px` }" class="text-gray-600 dark:text-gray-400">
            Real-time overview of human resources metrics and activities
          </p>
        </div>
        <div class="flex items-center space-x-3">
          <select
            v-model="selectedPeriod"
            :style="{ fontSize: `${fontSizes.label}` }"
            class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
          >
            <option value="7d">Last 7 days</option>
            <option value="30d">Last 30 days</option>
            <option value="90d">Last 90 days</option>
            <option value="1y">Last year</option>
          </select>
          <HrButton
            @click="handleRefresh"
            variant="secondary"
            size="md"
            :loading="isLoading"
          >
            <i class="fas fa-sync-alt mr-2"></i>
            Refresh
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
        title="Open Positions"
        :value="totalVacancies"
        subtitle="Active vacancies"
        icon="fas fa-briefcase"
        color="orange"
        :trend="{ direction: 'up', value: '+3', period: 'new this week' }"
      />
      <HrValueCard
        title="Applications"
        :value="totalApplications"
        subtitle="Total received"
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
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Department Utilization -->
      <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-6">
          <h2 :style="{ fontSize: `${fontSizes.h2}` }" class="font-semibold text-gray-900 dark:text-white">
            Department Utilization
          </h2>
          <HrButton variant="ghost" size="sm">
            View Details
            <i class="fas fa-arrow-right ml-1"></i>
          </HrButton>
        </div>
        
        <div class="space-y-4">
          <div
            v-for="dept in departmentStats"
            :key="dept.id"
            class="space-y-2"
          >
            <div class="flex items-center justify-between">
              <span :style="{ fontSize: `${fontSizes.subtitle}` }" class="font-medium text-gray-900 dark:text-white">
                {{ dept.name }}
              </span>
              <span :style="{ fontSize: `${fontSizes.label}` }" class="text-gray-600 dark:text-gray-400">
                {{ dept.employeeCount }}/20 employees
              </span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
              <div
                class="bg-green-600 h-2 rounded-full transition-all duration-300"
                :style="{ width: `${dept.utilizationRate}%` }"
              ></div>
            </div>
            <div class="flex items-center justify-between text-sm">
              <span :style="{ fontSize: `${fontSizes.xs}` }" class="text-gray-500 dark:text-gray-400">
                {{ dept.utilizationRate }}% utilization
              </span>
              <span :style="{ fontSize: `${fontSizes.xs}` }" class="text-gray-500 dark:text-gray-400">
                ${{ dept.budget.toLocaleString() }} budget
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Activity -->
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-4">
          <h2 :style="{ fontSize: `${fontSizes.h2}` }" class="font-semibold text-gray-900 dark:text-white">
            Recent Activity
          </h2>
          <HrButton variant="ghost" size="sm">
            View All
            <i class="fas fa-arrow-right ml-1"></i>
          </HrButton>
        </div>
        
        <div class="space-y-4">
          <div
            v-for="activity in recentActivity"
            :key="activity.id"
            class="flex items-start space-x-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"
          >
            <div :class="['flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center', activity.color, 'bg-opacity-20']">
              <i :class="[activity.icon, 'text-sm']"></i>
            </div>
            <div class="flex-1 min-w-0">
              <p :style="{ fontSize: `${fontSizes.xs}` }" class="text-gray-900 dark:text-white">
                {{ activity.message }}
              </p>
              <p :style="{ fontSize: `${fontSizes.xxs}` }" class="text-gray-500 dark:text-gray-400 mt-1">
                {{ formatDate(activity.timestamp) }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Upcoming Events -->
      <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-4">
          <h2 :style="{ fontSize: `${fontSizes.h2}` }" class="font-semibold text-gray-900 dark:text-white">
            Upcoming Events
          </h2>
          <HrButton variant="ghost" size="sm">
            View Calendar
            <i class="fas fa-arrow-right ml-1"></i>
          </HrButton>
        </div>
        
        <div class="space-y-4">
          <div
            v-for="event in upcomingEvents"
            :key="event.id"
            class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg"
          >
            <div class="flex items-center space-x-4">
              <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                <i :class="[
                  event.type === 'meeting' ? 'fas fa-users' : 
                  event.type === 'review' ? 'fas fa-star' : 
                  'fas fa-user-tie',
                  'text-blue-600 dark:text-blue-400'
                ]"></i>
              </div>
              <div>
                <p :style="{ fontSize: `${fontSizes.subtitle}` }" class="font-medium text-gray-900 dark:text-white">
                  {{ event.title }}
                </p>
                <p :style="{ fontSize: `${fontSizes.xs}` }" class="text-gray-600 dark:text-gray-400">
                  {{ event.attendees }} attendees
                </p>
              </div>
            </div>
            <div class="text-right">
              <p :style="{ fontSize: `${fontSizes.label}` }" class="font-medium text-gray-900 dark:text-white">
                {{ formatDate(event.date) }}
              </p>
              <StatusBadge 
                :status="event.type === 'meeting' ? 'info' : event.type === 'review' ? 'warning' : 'success'"
                :text="event.type === 'meeting' ? 'Meeting' : event.type === 'review' ? 'Review' : 'Interview'"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Stats -->
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h2 :style="{ fontSize: `${fontSizes.h2}` }" class="font-semibold text-gray-900 dark:text-white mb-4">
          Quick Stats
        </h2>
        
        <div class="space-y-4">
          <div class="flex items-center justify-between">
            <span :style="{ fontSize: `${fontSizes.label}` }" class="text-gray-600 dark:text-gray-400">
              Avg. Time to Hire
            </span>
            <span :style="{ fontSize: `${fontSizes.subtitle}` }" class="font-semibold text-gray-900 dark:text-white">
              21 days
            </span>
          </div>
          <div class="flex items-center justify-between">
            <span :style="{ fontSize: `${fontSizes.label}` }" class="text-gray-600 dark:text-gray-400">
              Employee Satisfaction
            </span>
            <span :style="{ fontSize: `${fontSizes.subtitle}` }" class="font-semibold text-gray-900 dark:text-white">
              4.2/5
            </span>
          </div>
          <div class="flex items-center justify-between">
            <span :style="{ fontSize: `${fontSizes.label}` }" class="text-gray-600 dark:text-gray-400">
              Retention Rate
            </span>
            <span :style="{ fontSize: `${fontSizes.subtitle}` }" class="font-semibold text-gray-900 dark:text-white">
              94%
            </span>
          </div>
          <div class="flex items-center justify-between">
            <span :style="{ fontSize: `${fontSizes.label}` }" class="text-gray-600 dark:text-gray-400">
              Training Hours
            </span>
            <span :style="{ fontSize: `${fontSizes.subtitle}` }" class="font-semibold text-gray-900 dark:text-white">
              1,240h
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.hr-dashboard {
  padding: 0;
}
</style>
