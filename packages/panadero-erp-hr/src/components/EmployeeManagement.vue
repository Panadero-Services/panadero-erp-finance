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
const selectedEmployee = ref(null);
const searchTerm = ref('');
const filterDepartment = ref('');
const filterStatus = ref('');

// Form data
const employeeForm = ref({
  firstName: '',
  lastName: '',
  email: '',
  phone: '',
  department: '',
  position: '',
  employmentType: 'full_time',
  salary: '',
  skills: []
});

// Computed
const filteredEmployees = computed(() => {
  let filtered = store.employees;
  
  if (searchTerm.value) {
    const term = searchTerm.value.toLowerCase();
    filtered = filtered.filter(emp => 
      emp.firstName.toLowerCase().includes(term) ||
      emp.lastName.toLowerCase().includes(term) ||
      emp.email.toLowerCase().includes(term) ||
      emp.position.toLowerCase().includes(term)
    );
  }
  
  if (filterDepartment.value) {
    filtered = filtered.filter(emp => emp.department === filterDepartment.value);
  }
  
  if (filterStatus.value) {
    filtered = filtered.filter(emp => emp.status === filterStatus.value);
  }
  
  return filtered;
});

const departmentOptions = computed(() => 
  store.departments.map(dept => ({ value: dept.name, label: dept.name }))
);

const statusOptions = [
  { value: 'active', label: 'Active' },
  { value: 'inactive', label: 'Inactive' },
  { value: 'terminated', label: 'Terminated' },
  { value: 'on_leave', label: 'On Leave' }
];

const employmentTypeOptions = [
  { value: 'full_time', label: 'Full Time' },
  { value: 'part_time', label: 'Part Time' },
  { value: 'contract', label: 'Contract' },
  { value: 'intern', label: 'Intern' },
  { value: 'consultant', label: 'Consultant' }
];

// Actions
const handleAddEmployee = () => {
  employeeForm.value = {
    firstName: '',
    lastName: '',
    email: '',
    phone: '',
    department: '',
    position: '',
    employmentType: 'full_time',
    salary: '',
    skills: []
  };
  showAddModal.value = true;
};

const handleEditEmployee = (employee) => {
  selectedEmployee.value = employee;
  employeeForm.value = { ...employee };
  showEditModal.value = true;
};

const handleSaveEmployee = async () => {
  isLoading.value = true;
  try {
    if (selectedEmployee.value) {
      await store.updateEmployee(selectedEmployee.value.id, employeeForm.value);
    } else {
      await store.addEmployee(employeeForm.value);
    }
    
    showAddModal.value = false;
    showEditModal.value = false;
    selectedEmployee.value = null;
  } catch (error) {
    console.error('Error saving employee:', error);
  } finally {
    isLoading.value = false;
  }
};

const handleDeleteEmployee = async (employeeId) => {
  if (confirm('Are you sure you want to delete this employee?')) {
    // Implementation for deleting employee
    console.log('Delete employee:', employeeId);
  }
};

const formatSalary = (salary) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(salary);
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString();
};

onMounted(() => {
  store.loadSettings();
});
</script>

<template>
  <div class="employee-management">
    <!-- Header -->
    <div class="mb-8">
      <div class="flex items-center justify-between">
        <div>
          <h1 :style="{ fontSize: `${fontSizes.heading}` }" class="font-bold text-gray-900 dark:text-white mb-2">
            Employee Management
          </h1>
          <p :style="{ fontSize: `${fontSizes.base}px` }" class="text-gray-600 dark:text-gray-400">
            Manage employee information, roles, and organizational structure
          </p>
        </div>
        <div class="flex items-center space-x-3">
          <HrButton
            @click="handleAddEmployee"
            variant="primary"
            size="md"
          >
            <i class="fas fa-user-plus mr-2"></i>
            Add Employee
          </HrButton>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <HrInput
          v-model="searchTerm"
          placeholder="Search employees..."
          type="text"
        />
        <HrDropdown
          v-model="filterDepartment"
          :options="[{ value: '', label: 'All Departments' }, ...departmentOptions]"
          placeholder="Filter by department"
        />
        <HrDropdown
          v-model="filterStatus"
          :options="[{ value: '', label: 'All Status' }, ...statusOptions]"
          placeholder="Filter by status"
        />
        <HrButton
          @click="searchTerm = ''; filterDepartment = ''; filterStatus = ''"
          variant="ghost"
          size="md"
        >
          <i class="fas fa-eraser mr-2"></i>
          Clear Filters
        </HrButton>
      </div>
    </div>

    <!-- Employees Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
              <th :style="{ fontSize: `${fontSizes.label}` }" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Employee
              </th>
              <th :style="{ fontSize: `${fontSizes.label}` }" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Position
              </th>
              <th :style="{ fontSize: `${fontSizes.label}` }" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Department
              </th>
              <th :style="{ fontSize: `${fontSizes.label}` }" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Salary
              </th>
              <th :style="{ fontSize: `${fontSizes.label}` }" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Status
              </th>
              <th :style="{ fontSize: `${fontSizes.label}` }" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-for="employee in filteredEmployees" :key="employee.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="w-10 h-10 bg-green-100 dark:bg-green-900/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-green-600 dark:text-green-400"></i>
                  </div>
                  <div class="ml-4">
                    <div :style="{ fontSize: `${fontSizes.subtitle}` }" class="text-sm font-medium text-gray-900 dark:text-white">
                      {{ employee.firstName }} {{ employee.lastName }}
                    </div>
                    <div :style="{ fontSize: `${fontSizes.xs}` }" class="text-sm text-gray-500 dark:text-gray-400">
                      {{ employee.email }}
                    </div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div :style="{ fontSize: `${fontSizes.base}` }" class="text-sm text-gray-900 dark:text-white">
                  {{ employee.position }}
                </div>
                <div :style="{ fontSize: `${fontSizes.xs}` }" class="text-sm text-gray-500 dark:text-gray-400">
                  {{ employee.employmentType.replace('_', ' ').toUpperCase() }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div :style="{ fontSize: `${fontSizes.base}` }" class="text-sm text-gray-900 dark:text-white">
                  {{ employee.department }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div :style="{ fontSize: `${fontSizes.base}` }" class="text-sm text-gray-900 dark:text-white">
                  {{ formatSalary(employee.salary) }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <StatusBadge :status="employee.status" />
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex items-center space-x-2">
                  <HrButton
                    @click="handleEditEmployee(employee)"
                    variant="ghost"
                    size="sm"
                  >
                    <i class="fas fa-edit"></i>
                  </HrButton>
                  <HrButton
                    @click="handleDeleteEmployee(employee.id)"
                    variant="danger"
                    size="sm"
                  >
                    <i class="fas fa-trash"></i>
                  </HrButton>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Add/Edit Employee Modal -->
    <div v-if="showAddModal || showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <h2 :style="{ fontSize: `${fontSizes.h2}` }" class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
          {{ selectedEmployee ? 'Edit Employee' : 'Add New Employee' }}
        </h2>
        
        <form @submit.prevent="handleSaveEmployee" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <HrInput
              v-model="employeeForm.firstName"
              label="First Name"
              placeholder="Enter first name"
              required
            />
            <HrInput
              v-model="employeeForm.lastName"
              label="Last Name"
              placeholder="Enter last name"
              required
            />
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <HrInput
              v-model="employeeForm.email"
              label="Email"
              type="email"
              placeholder="Enter email address"
              required
            />
            <HrInput
              v-model="employeeForm.phone"
              label="Phone"
              placeholder="Enter phone number"
            />
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <HrDropdown
              v-model="employeeForm.department"
              :options="departmentOptions"
              label="Department"
              placeholder="Select department"
              required
            />
            <HrInput
              v-model="employeeForm.position"
              label="Position"
              placeholder="Enter job title"
              required
            />
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <HrDropdown
              v-model="employeeForm.employmentType"
              :options="employmentTypeOptions"
              label="Employment Type"
              placeholder="Select employment type"
              required
            />
            <HrInput
              v-model="employeeForm.salary"
              label="Salary"
              type="number"
              placeholder="Enter salary"
            />
          </div>
          
          <div class="flex justify-end space-x-3 pt-4">
            <HrButton
              @click="showAddModal = false; showEditModal = false; selectedEmployee = null"
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
              {{ selectedEmployee ? 'Update Employee' : 'Add Employee' }}
            </HrButton>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
.employee-management {
  padding: 0;
}
</style>
