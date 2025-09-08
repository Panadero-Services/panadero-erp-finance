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
const selectedDepartment = ref(null);
const searchTerm = ref('');
const filterStatus = ref('');

// Form data
const departmentForm = ref({
  name: '',
  description: '',
  code: '',
  budget: '',
  location: '',
  status: 'active'
});

// Computed
const filteredDepartments = computed(() => {
  let filtered = store.departments;
  
  if (searchTerm.value) {
    const term = searchTerm.value.toLowerCase();
    filtered = filtered.filter(dept => 
      dept.name.toLowerCase().includes(term) ||
      dept.description.toLowerCase().includes(term) ||
      dept.code.toLowerCase().includes(term)
    );
  }
  
  if (filterStatus.value) {
    filtered = filtered.filter(dept => dept.status === filterStatus.value);
  }
  
  return filtered;
});

const statusOptions = [
  { value: '', label: 'All Status' },
  { value: 'active', label: 'Active' },
  { value: 'inactive', label: 'Inactive' },
  { value: 'archived', label: 'Archived' }
];

// Actions
const handleAddDepartment = () => {
  departmentForm.value = {
    name: '',
    description: '',
    code: '',
    budget: '',
    location: '',
    status: 'active'
  };
  showAddModal.value = true;
};

const handleEditDepartment = (department) => {
  selectedDepartment.value = department;
  departmentForm.value = { ...department };
  showEditModal.value = true;
};

const handleSaveDepartment = async () => {
  isLoading.value = true;
  try {
    if (selectedDepartment.value) {
      await store.updateDepartment(selectedDepartment.value.id, departmentForm.value);
    } else {
      await store.addDepartment(departmentForm.value);
    }
    
    showAddModal.value = false;
    showEditModal.value = false;
    selectedDepartment.value = null;
  } catch (error) {
    console.error('Error saving department:', error);
  } finally {
    isLoading.value = false;
  }
};

const handleDeleteDepartment = async (departmentId) => {
  if (confirm('Are you sure you want to delete this department?')) {
    // Implementation for deleting department
    console.log('Delete department:', departmentId);
  }
};

const formatBudget = (budget) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(budget);
};

onMounted(() => {
  store.loadSettings();
});
</script>

<template>
  <div class="department-management">
    <!-- Header -->
    <div class="mb-8">
      <div class="flex items-center justify-between">
        <div>
          <h1 :style="{ fontSize: `${fontSizes.heading}` }" class="font-bold text-gray-900 dark:text-white mb-2">
            Department Management
          </h1>
          <p :style="{ fontSize: `${fontSizes.base}px` }" class="text-gray-600 dark:text-gray-400">
            Organize and manage company departments and organizational structure
          </p>
        </div>
        <div class="flex items-center space-x-3">
          <HrButton
            @click="handleAddDepartment"
            variant="primary"
            size="md"
          >
            <i class="fas fa-plus mr-2"></i>
            Add Department
          </HrButton>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <HrInput
          v-model="searchTerm"
          placeholder="Search departments..."
          type="text"
        />
        <HrDropdown
          v-model="filterStatus"
          :options="statusOptions"
          placeholder="Filter by status"
        />
        <HrButton
          @click="searchTerm = ''; filterStatus = ''"
          variant="ghost"
          size="md"
        >
          <i class="fas fa-eraser mr-2"></i>
          Clear Filters
        </HrButton>
      </div>
    </div>

    <!-- Departments Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="department in filteredDepartments"
        :key="department.id"
        class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-shadow duration-200"
      >
        <div class="flex items-start justify-between mb-4">
          <div class="flex-1">
            <h3 :style="{ fontSize: `${fontSizes.h3}` }" class="font-semibold text-gray-900 dark:text-white mb-2">
              {{ department.name }}
            </h3>
            <p :style="{ fontSize: `${fontSizes.label}` }" class="text-gray-600 dark:text-gray-400 mb-2">
              {{ department.code }}
            </p>
            <p :style="{ fontSize: `${fontSizes.xs}` }" class="text-gray-500 dark:text-gray-400">
              {{ department.location }}
            </p>
          </div>
          <StatusBadge :status="department.status" />
        </div>
        
        <p :style="{ fontSize: `${fontSizes.xs}` }" class="text-gray-600 dark:text-gray-400 mb-4 line-clamp-3">
          {{ department.description }}
        </p>
        
        <div class="space-y-3 mb-4">
          <div class="flex items-center justify-between">
            <span :style="{ fontSize: `${fontSizes.xs}` }" class="text-gray-500 dark:text-gray-400">
              Employees
            </span>
            <span :style="{ fontSize: `${fontSizes.label}` }" class="font-semibold text-gray-900 dark:text-white">
              {{ department.employeeCount }}
            </span>
          </div>
          <div class="flex items-center justify-between">
            <span :style="{ fontSize: `${fontSizes.xs}` }" class="text-gray-500 dark:text-gray-400">
              Budget
            </span>
            <span :style="{ fontSize: `${fontSizes.label}` }" class="font-semibold text-gray-900 dark:text-white">
              {{ formatBudget(department.budget) }}
            </span>
          </div>
        </div>
        
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-2">
            <HrButton
              @click="handleEditDepartment(department)"
              variant="ghost"
              size="sm"
            >
              <i class="fas fa-edit"></i>
            </HrButton>
            <HrButton
              @click="handleDeleteDepartment(department.id)"
              variant="danger"
              size="sm"
            >
              <i class="fas fa-trash"></i>
            </HrButton>
          </div>
          <HrButton
            variant="ghost"
            size="sm"
          >
            View Details
            <i class="fas fa-arrow-right ml-1"></i>
          </HrButton>
        </div>
      </div>
    </div>

    <!-- Add/Edit Department Modal -->
    <div v-if="showAddModal || showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <h2 :style="{ fontSize: `${fontSizes.h2}` }" class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
          {{ selectedDepartment ? 'Edit Department' : 'Add New Department' }}
        </h2>
        
        <form @submit.prevent="handleSaveDepartment" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <HrInput
              v-model="departmentForm.name"
              label="Department Name"
              placeholder="Enter department name"
              required
            />
            <HrInput
              v-model="departmentForm.code"
              label="Department Code"
              placeholder="Enter department code"
              required
            />
          </div>
          
          <HrInput
            v-model="departmentForm.description"
            label="Description"
            placeholder="Enter department description"
            type="textarea"
            rows="3"
            required
          />
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <HrInput
              v-model="departmentForm.budget"
              label="Budget"
              type="number"
              placeholder="Enter budget amount"
            />
            <HrInput
              v-model="departmentForm.location"
              label="Location"
              placeholder="Enter department location"
            />
          </div>
          
          <div class="flex justify-end space-x-3 pt-4">
            <HrButton
              @click="showAddModal = false; showEditModal = false; selectedDepartment = null"
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
              {{ selectedDepartment ? 'Update Department' : 'Add Department' }}
            </HrButton>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
.department-management {
  padding: 0;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
