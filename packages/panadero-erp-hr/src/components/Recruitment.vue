<script setup>
import { ref, computed, onMounted } from 'vue';
import { useHrStore } from '../stores/hrStore';
import { useScaling } from '../../../shared/composables/useScaling.js';

// UI Components
import HrButton from './ui/HrButton.vue';
import HrInput from './ui/HrInput.vue';
import HrDropdown from './ui/HrDropdown.vue';
import StatusBadge from './ui/StatusBadge.vue';

const store = useHrStore();
const { fontSizes, scalingStyles } = useScaling();

// State
const isLoading = ref(false);
const searchTerm = ref('');
const filterStatus = ref('');
const filterVacancy = ref('');

// Computed
const filteredApplications = computed(() => {
  let filtered = store.applications;
  
  if (searchTerm.value) {
    const term = searchTerm.value.toLowerCase();
    filtered = filtered.filter(app => 
      app.firstName.toLowerCase().includes(term) ||
      app.lastName.toLowerCase().includes(term) ||
      app.email.toLowerCase().includes(term)
    );
  }
  
  if (filterStatus.value) {
    filtered = filtered.filter(app => app.status === filterStatus.value);
  }
  
  if (filterVacancy.value) {
    filtered = filtered.filter(app => app.vacancyId === parseInt(filterVacancy.value));
  }
  
  return filtered;
});

const vacancyOptions = computed(() => 
  store.vacancies.map(vacancy => ({ value: vacancy.id, label: vacancy.title }))
);

const statusOptions = [
  { value: '', label: 'All Status' },
  { value: 'submitted', label: 'Submitted' },
  { value: 'screening', label: 'Screening' },
  { value: 'interview', label: 'Interview' },
  { value: 'offer', label: 'Offer' },
  { value: 'rejected', label: 'Rejected' },
  { value: 'withdrawn', label: 'Withdrawn' }
];

// Actions
const handleUpdateStatus = async (applicationId, newStatus) => {
  await store.updateApplicationStatus(applicationId, newStatus);
};

const handleViewResume = (application) => {
  if (application.resumePath) {
    window.open(application.resumePath, '_blank');
  } else {
    alert('Resume not available');
  }
};

const getVacancyTitle = (vacancyId) => {
  const vacancy = store.vacancies.find(v => v.id === vacancyId);
  return vacancy ? vacancy.title : 'Unknown Position';
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString();
};

onMounted(() => {
  store.loadSettings();
});
</script>

<template>
  <div class="recruitment">
    <!-- Header -->
    <div class="mb-8">
      <div class="flex items-center justify-between">
        <div>
          <h1 :style="{ fontSize: `${fontSizes.heading}` }" class="font-bold text-gray-900 dark:text-white mb-2">
            Recruitment Management
          </h1>
          <p :style="{ fontSize: `${fontSizes.base}px` }" class="text-gray-600 dark:text-gray-400">
            Review and manage job applications and recruitment pipeline
          </p>
        </div>
        <div class="flex items-center space-x-3">
          <HrButton
            variant="secondary"
            size="md"
          >
            <i class="fas fa-download mr-2"></i>
            Export Applications
          </HrButton>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <HrInput
          v-model="searchTerm"
          placeholder="Search applications..."
          type="text"
        />
        <HrDropdown
          v-model="filterStatus"
          :options="statusOptions"
          placeholder="Filter by status"
        />
        <HrDropdown
          v-model="filterVacancy"
          :options="[{ value: '', label: 'All Positions' }, ...vacancyOptions]"
          placeholder="Filter by position"
        />
        <HrButton
          @click="searchTerm = ''; filterStatus = ''; filterVacancy = ''"
          variant="ghost"
          size="md"
        >
          <i class="fas fa-eraser mr-2"></i>
          Clear Filters
        </HrButton>
      </div>
    </div>

    <!-- Applications Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
              <th :style="{ fontSize: `${fontSizes.label}` }" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Candidate
              </th>
              <th :style="{ fontSize: `${fontSizes.label}` }" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Position
              </th>
              <th :style="{ fontSize: `${fontSizes.label}` }" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Experience
              </th>
              <th :style="{ fontSize: `${fontSizes.label}` }" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                AI Score
              </th>
              <th :style="{ fontSize: `${fontSizes.label}` }" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Status
              </th>
              <th :style="{ fontSize: `${fontSizes.label}` }" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Applied
              </th>
              <th :style="{ fontSize: `${fontSizes.label}` }" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-for="application in filteredApplications" :key="application.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-blue-600 dark:text-blue-400"></i>
                  </div>
                  <div class="ml-4">
                    <div :style="{ fontSize: `${fontSizes.subtitle}` }" class="text-sm font-medium text-gray-900 dark:text-white">
                      {{ application.firstName }} {{ application.lastName }}
                    </div>
                    <div :style="{ fontSize: `${fontSizes.xs}` }" class="text-sm text-gray-500 dark:text-gray-400">
                      {{ application.email }}
                    </div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div :style="{ fontSize: `${fontSizes.base}` }" class="text-sm text-gray-900 dark:text-white">
                  {{ getVacancyTitle(application.vacancyId) }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div :style="{ fontSize: `${fontSizes.base}` }" class="text-sm text-gray-900 dark:text-white">
                  {{ application.experience }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div v-if="application.aiScore" class="flex items-center">
                  <div :class="[
                    'w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold',
                    application.aiScore >= 80 ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' :
                    application.aiScore >= 60 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400' :
                    'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'
                  ]">
                    {{ application.aiScore }}
                  </div>
                </div>
                <div v-else :style="{ fontSize: `${fontSizes.xs}` }" class="text-gray-500 dark:text-gray-400">
                  Not scored
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <StatusBadge :status="application.status" />
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div :style="{ fontSize: `${fontSizes.xs}` }" class="text-sm text-gray-500 dark:text-gray-400">
                  {{ formatDate(application.appliedDate) }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex items-center space-x-2">
                  <HrButton
                    @click="handleViewResume(application)"
                    variant="ghost"
                    size="sm"
                  >
                    <i class="fas fa-file-alt"></i>
                  </HrButton>
                  <HrDropdown
                    :options="statusOptions.filter(s => s.value !== '')"
                    @update:modelValue="(value) => handleUpdateStatus(application.id, value)"
                    placeholder="Update Status"
                    size="sm"
                  />
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
.recruitment {
  padding: 0;
}
</style>
