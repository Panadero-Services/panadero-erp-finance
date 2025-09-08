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
const showAddModal = ref(false);
const showEditModal = ref(false);
const selectedVacancy = ref(null);
const searchTerm = ref('');
const filterStatus = ref('');
const filterDepartment = ref('');

// Form data
const vacancyForm = ref({
  title: '',
  description: '',
  department: '',
  positionType: '',
  employmentType: 'full_time',
  experienceLevel: 'mid',
  location: '',
  remoteAllowed: false,
  salaryMin: '',
  salaryMax: '',
  status: 'draft'
});

// Computed
const filteredVacancies = computed(() => {
  let filtered = store.vacancies;
  
  if (searchTerm.value) {
    const term = searchTerm.value.toLowerCase();
    filtered = filtered.filter(vacancy => 
      vacancy.title.toLowerCase().includes(term) ||
      vacancy.description.toLowerCase().includes(term) ||
      vacancy.positionType.toLowerCase().includes(term)
    );
  }
  
  if (filterStatus.value) {
    filtered = filtered.filter(vacancy => vacancy.status === filterStatus.value);
  }
  
  if (filterDepartment.value) {
    filtered = filtered.filter(vacancy => vacancy.department === filterDepartment.value);
  }
  
  return filtered;
});

const departmentOptions = computed(() => 
  store.departments.map(dept => ({ value: dept.name, label: dept.name }))
);

const statusOptions = [
  { value: '', label: 'All Status' },
  { value: 'draft', label: 'Draft' },
  { value: 'published', label: 'Published' },
  { value: 'closed', label: 'Closed' },
  { value: 'cancelled', label: 'Cancelled' }
];

const experienceLevelOptions = [
  { value: 'entry', label: 'Entry Level' },
  { value: 'mid', label: 'Mid Level' },
  { value: 'senior', label: 'Senior Level' },
  { value: 'executive', label: 'Executive' }
];

const employmentTypeOptions = [
  { value: 'full_time', label: 'Full Time' },
  { value: 'part_time', label: 'Part Time' },
  { value: 'contract', label: 'Contract' },
  { value: 'intern', label: 'Intern' },
  { value: 'consultant', label: 'Consultant' }
];

// Actions
const handleAddVacancy = () => {
  vacancyForm.value = {
    title: '',
    description: '',
    department: '',
    positionType: '',
    employmentType: 'full_time',
    experienceLevel: 'mid',
    location: '',
    remoteAllowed: false,
    salaryMin: '',
    salaryMax: '',
    status: 'draft'
  };
  showAddModal.value = true;
};

const handleEditVacancy = (vacancy) => {
  selectedVacancy.value = vacancy;
  vacancyForm.value = { ...vacancy };
  showEditModal.value = true;
};

const handleSaveVacancy = async () => {
  isLoading.value = true;
  try {
    if (selectedVacancy.value) {
      await store.updateVacancy(selectedVacancy.value.id, vacancyForm.value);
    } else {
      await store.addVacancy(vacancyForm.value);
    }
    
    showAddModal.value = false;
    showEditModal.value = false;
    selectedVacancy.value = null;
  } catch (error) {
    console.error('Error saving vacancy:', error);
  } finally {
    isLoading.value = false;
  }
};

const handlePublishVacancy = async (vacancyId) => {
  await store.updateVacancy(vacancyId, { status: 'published' });
};

const handleCloseVacancy = async (vacancyId) => {
  await store.updateVacancy(vacancyId, { status: 'closed' });
};

const formatSalary = (min, max) => {
  if (min && max) {
    return `$${min.toLocaleString()} - $${max.toLocaleString()}`;
  } else if (min) {
    return `$${min.toLocaleString()}+`;
  } else if (max) {
    return `Up to $${max.toLocaleString()}`;
  }
  return 'Not specified';
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString();
};

onMounted(() => {
  store.loadSettings();
});
</script>

<template>
  <div class="vacancy-management">
    <!-- Header -->
    <div class="mb-8">
      <div class="flex items-center justify-between">
        <div>
          <h1 :style="{ fontSize: `${fontSizes.heading}` }" class="font-bold text-gray-900 dark:text-white mb-2">
            Vacancy Management
          </h1>
          <p :style="{ fontSize: `${fontSizes.base}px` }" class="text-gray-600 dark:text-gray-400">
            Create and manage job vacancies and recruitment opportunities
          </p>
        </div>
        <div class="flex items-center space-x-3">
          <HrButton
            @click="handleAddVacancy"
            variant="primary"
            size="md"
          >
            <i class="fas fa-plus mr-2"></i>
            Create Vacancy
          </HrButton>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <HrInput
          v-model="searchTerm"
          placeholder="Search vacancies..."
          type="text"
        />
        <HrDropdown
          v-model="filterStatus"
          :options="statusOptions"
          placeholder="Filter by status"
        />
        <HrDropdown
          v-model="filterDepartment"
          :options="[{ value: '', label: 'All Departments' }, ...departmentOptions]"
          placeholder="Filter by department"
        />
        <HrButton
          @click="searchTerm = ''; filterStatus = ''; filterDepartment = ''"
          variant="ghost"
          size="md"
        >
          <i class="fas fa-eraser mr-2"></i>
          Clear Filters
        </HrButton>
      </div>
    </div>

    <!-- Vacancies Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="vacancy in filteredVacancies"
        :key="vacancy.id"
        class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-shadow duration-200"
      >
        <div class="flex items-start justify-between mb-4">
          <div class="flex-1">
            <h3 :style="{ fontSize: `${fontSizes.h3}` }" class="font-semibold text-gray-900 dark:text-white mb-2">
              {{ vacancy.title }}
            </h3>
            <p :style="{ fontSize: `${fontSizes.label}` }" class="text-gray-600 dark:text-gray-400 mb-2">
              {{ vacancy.positionType }}
            </p>
            <p :style="{ fontSize: `${fontSizes.xs}` }" class="text-gray-500 dark:text-gray-400">
              {{ vacancy.department }}
            </p>
          </div>
          <StatusBadge :status="vacancy.status" />
        </div>
        
        <div class="space-y-3 mb-4">
          <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
            <i class="fas fa-map-marker-alt mr-2"></i>
            <span :style="{ fontSize: `${fontSizes.xs}` }">
              {{ vacancy.location }} {{ vacancy.remoteAllowed ? '(Remote OK)' : '' }}
            </span>
          </div>
          <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
            <i class="fas fa-briefcase mr-2"></i>
            <span :style="{ fontSize: `${fontSizes.xs}` }">
              {{ vacancy.employmentType.replace('_', ' ').toUpperCase() }} • {{ vacancy.experienceLevel.toUpperCase() }}
            </span>
          </div>
          <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
            <i class="fas fa-dollar-sign mr-2"></i>
            <span :style="{ fontSize: `${fontSizes.xs}` }">
              {{ formatSalary(vacancy.salaryMin, vacancy.salaryMax) }}
            </span>
          </div>
          <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
            <i class="fas fa-users mr-2"></i>
            <span :style="{ fontSize: `${fontSizes.xs}` }">
              {{ vacancy.applicationCount }} applications
            </span>
          </div>
        </div>
        
        <p :style="{ fontSize: `${fontSizes.xs}` }" class="text-gray-600 dark:text-gray-400 mb-4 line-clamp-3">
          {{ vacancy.description }}
        </p>
        
        <div class="flex items-center justify-between">
          <span :style="{ fontSize: `${fontSizes.xxs}` }" class="text-gray-500 dark:text-gray-400">
            Created {{ formatDate(vacancy.createdDate) }}
          </span>
          <div class="flex items-center space-x-2">
            <HrButton
              @click="handleEditVacancy(vacancy)"
              variant="ghost"
              size="sm"
            >
              <i class="fas fa-edit"></i>
            </HrButton>
            <HrButton
              v-if="vacancy.status === 'draft'"
              @click="handlePublishVacancy(vacancy.id)"
              variant="success"
              size="sm"
            >
              <i class="fas fa-eye"></i>
            </HrButton>
            <HrButton
              v-if="vacancy.status === 'published'"
              @click="handleCloseVacancy(vacancy.id)"
              variant="warning"
              size="sm"
            >
              <i class="fas fa-times"></i>
            </HrButton>
          </div>
        </div>
      </div>
    </div>

    <!-- Add/Edit Vacancy Modal -->
    <div v-if="showAddModal || showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-4xl max-h-[90vh] overflow-y-auto">
        <h2 :style="{ fontSize: `${fontSizes.h2}` }" class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
          {{ selectedVacancy ? 'Edit Vacancy' : 'Create New Vacancy' }}
        </h2>
        
        <form @submit.prevent="handleSaveVacancy" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <HrInput
              v-model="vacancyForm.title"
              label="Job Title"
              placeholder="Enter job title"
              required
            />
            <HrInput
              v-model="vacancyForm.positionType"
              label="Position Type"
              placeholder="Enter position type"
              required
            />
          </div>
          
          <HrInput
            v-model="vacancyForm.description"
            label="Job Description"
            placeholder="Enter detailed job description"
            type="textarea"
            rows="4"
            required
          />
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <HrDropdown
              v-model="vacancyForm.department"
              :options="departmentOptions"
              label="Department"
              placeholder="Select department"
              required
            />
            <HrDropdown
              v-model="vacancyForm.employmentType"
              :options="employmentTypeOptions"
              label="Employment Type"
              placeholder="Select employment type"
              required
            />
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <HrDropdown
              v-model="vacancyForm.experienceLevel"
              :options="experienceLevelOptions"
              label="Experience Level"
              placeholder="Select experience level"
              required
            />
            <HrInput
              v-model="vacancyForm.location"
              label="Location"
              placeholder="Enter job location"
            />
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <HrInput
              v-model="vacancyForm.salaryMin"
              label="Minimum Salary"
              type="number"
              placeholder="Enter minimum salary"
            />
            <HrInput
              v-model="vacancyForm.salaryMax"
              label="Maximum Salary"
              type="number"
              placeholder="Enter maximum salary"
            />
          </div>
          
          <div class="flex items-center">
            <input
              v-model="vacancyForm.remoteAllowed"
              type="checkbox"
              class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
            />
            <label :style="{ fontSize: `${fontSizes.label}` }" class="ml-2 block text-sm text-gray-900 dark:text-white">
              Remote work allowed
            </label>
          </div>
          
          <div class="flex justify-end space-x-3 pt-4">
            <HrButton
              @click="showAddModal = false; showEditModal = false; selectedVacancy = null"
              variant="ghost"
              type="button"
            >
              Cancel
            </HrButton>
            <HrButton
              type="submit"
              variant="primary"
              :loading="isLoading"
            >
              {{ selectedVacancy ? 'Update Vacancy' : 'Create Vacancy' }}
            </HrButton>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
.vacancy-management {
  padding: 0;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
