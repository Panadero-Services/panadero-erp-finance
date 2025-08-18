<script setup>
import { ref } from 'vue';
import FinanceDropdown from '../ui/FinanceDropdown.vue';
import FinanceButton from '../ui/FinanceButton.vue';

// Reactive data
const selectedValue = ref('');
const selectedCategory = ref('');
const selectedStatus = ref('');
const selectedCountry = ref('');
const selectedSize = ref('normal');

// Sample data
const categories = [
  'Revenue',
  'Expenses',
  'Assets',
  'Liabilities',
  'Equity'
];

const statuses = [
  { label: 'Active', value: 'active' },
  { label: 'Inactive', value: 'inactive' },
  { label: 'Pending', value: 'pending' },
  { label: 'Completed', value: 'completed' },
  { label: 'Cancelled', value: 'cancelled' }
];

const countries = [
  { label: 'United States', value: 'US' },
  { label: 'Canada', value: 'CA' },
  { label: 'United Kingdom', value: 'UK' },
  { label: 'Germany', value: 'DE' },
  { label: 'France', value: 'FR' },
  { label: 'Japan', value: 'JP' },
  { label: 'Australia', value: 'AU' }
];

const sizes = [
  { label: 'Small', value: 'small' },
  { label: 'Normal', value: 'normal' },
  { label: 'Large', value: 'large' }
];

// Methods
const handleChange = (value, option) => {
  console.log('Dropdown changed:', { value, option });
};

const handleSubmit = () => {
  console.log('Form submitted with values:', {
    category: selectedCategory.value,
    status: selectedStatus.value,
    country: selectedCountry.value
  });
};
</script>

<template>
  <div>
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
      FinanceDropdown Component Demo
    </h2>
    
    <!-- Three Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      
      <!-- Column 1: Basic Usage & Variants -->
      <div class="space-y-6">
        <!-- Basic Usage Section -->
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">
            Basic Usage
          </h3>
          <div class="space-y-3 mb-3">
            <FinanceDropdown 
              v-model="selectedCategory"
              :options="categories"
              placeholder="Select Category"
              @change="handleChange"
            />
            <FinanceDropdown 
              v-model="selectedStatus"
              :options="statuses"
              option-label="label"
              option-value="value"
              placeholder="Select Status"
              @change="handleChange"
            />
          </div>
          <div class="mb-3 text-xs text-gray-600 dark:text-gray-400">
            Selected: {{ selectedCategory || 'None' }} | {{ selectedStatus || 'None' }}
          </div>
          <!-- Usage Example -->
          <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded text-xs">
            <div class="font-mono text-blue-600 dark:text-blue-400">
              &lt;FinanceDropdown v-model="selected" :options="options" /&gt;<br>
              &lt;FinanceDropdown :options="statuses" option-label="label" /&gt;
            </div>
          </div>
        </div>
        
        <!-- Style Variants Section -->
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">
            Style Variants
          </h3>
          <div class="space-y-3 mb-3">
            <FinanceDropdown 
              :options="countries"
              option-label="label"
              option-value="value"
              variant="outline"
              placeholder="Outline Style"
            />
            <FinanceDropdown 
              :options="countries"
              option-label="label"
              option-value="value"
              variant="filled"
              placeholder="Filled Style"
            />
            <FinanceDropdown 
              :options="countries"
              option-label="label"
              option-value="value"
              variant="ghost"
              placeholder="Ghost Style"
            />
          </div>
          <!-- Usage Example -->
          <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded text-xs">
            <div class="font-mono text-blue-600 dark:text-blue-400">
              &lt;FinanceDropdown variant="outline" /&gt;<br>
              &lt;FinanceDropdown variant="filled" /&gt;<br>
              &lt;FinanceDropdown variant="ghost" /&gt;
            </div>
          </div>
        </div>
      </div>
    
      <!-- Column 2: Sizes & States -->
      <div class="space-y-6">
        <!-- Size Variants Section -->
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">
            Size Variants
          </h3>
          <div class="space-y-3 mb-3">
            <div class="flex items-end gap-2">
              <FinanceDropdown 
                :options="categories"
                size="small"
                placeholder="Small"
                variant="outline"
              />
              <FinanceDropdown 
                :options="categories"
                size="normal"
                placeholder="Normal"
                variant="outline"
              />
            </div>
            <FinanceDropdown 
              :options="categories"
              size="large"
              placeholder="Large Size"
              variant="outline"
            />
          </div>
          <!-- Usage Example -->
          <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded text-xs">
            <div class="font-mono text-blue-600 dark:text-blue-400">
              &lt;FinanceDropdown size="small" /&gt;<br>
              &lt;FinanceDropdown size="normal" /&gt;<br>
              &lt;FinanceDropdown size="large" /&gt;
            </div>
          </div>
        </div>
        
        <!-- States Section -->
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">
            States
          </h3>
          <div class="space-y-3 mb-3">
            <FinanceDropdown 
              :options="categories"
              placeholder="Normal State"
              variant="outline"
            />
            <FinanceDropdown 
              :options="categories"
              placeholder="Disabled State"
              variant="outline"
              disabled
            />
          </div>
          <!-- Usage Example -->
          <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded text-xs">
            <div class="font-mono text-blue-600 dark:text-blue-400">
              &lt;FinanceDropdown disabled /&gt;<br>
              &lt;FinanceDropdown :disabled="isDisabled" /&gt;
            </div>
          </div>
        </div>
        
        <!-- With Buttons Section -->
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">
            With Buttons
          </h3>
          <div class="space-y-3 mb-3">
            <div class="flex items-center gap-2">
              <FinanceDropdown 
                :options="categories"
                size="small"
                placeholder="Category"
                variant="outline"
              />
              <FinanceButton size="small" variant="primary">Filter</FinanceButton>
            </div>
            <div class="flex items-center gap-2">
              <FinanceDropdown 
                v-model="selectedCountry"
                :options="countries"
                option-label="label"
                option-value="value"
                size="normal"
                placeholder="Country"
                variant="outline"
              />
              <FinanceButton size="normal" variant="success" icon-left="fas fa-save">Save</FinanceButton>
            </div>
          </div>
          <!-- Usage Example -->
          <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded text-xs">
            <div class="font-mono text-blue-600 dark:text-blue-400">
              &lt;FinanceDropdown size="normal" /&gt;<br>
              &lt;FinanceButton size="normal"&gt;Filter&lt;/FinanceButton&gt;
            </div>
          </div>
        </div>
      </div>
    
      <!-- Column 3: Full Width & Interactive -->
      <div class="space-y-6">
        <!-- Full Width Section -->
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">
            Full Width
          </h3>
          <div class="space-y-3 mb-3">
            <FinanceDropdown 
              :options="countries"
              option-label="label"
              option-value="value"
              placeholder="Full Width Dropdown"
              variant="outline"
              full-width
            />
            <FinanceButton variant="primary" full-width>Primary Full Width</FinanceButton>
          </div>
          <!-- Usage Example -->
          <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded text-xs">
            <div class="font-mono text-blue-600 dark:text-blue-400">
              &lt;FinanceDropdown full-width /&gt;<br>
              &lt;FinanceDropdown full-width variant="outline" /&gt;
            </div>
          </div>
        </div>
        
        <!-- Interactive Form Section -->
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">
            Interactive Form
          </h3>
          <div class="space-y-3 mb-3">
            <div>
              <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
              <FinanceDropdown 
                v-model="selectedCategory"
                :options="categories"
                placeholder="Select Category"
                variant="outline"
                full-width
              />
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
              <FinanceDropdown 
                v-model="selectedStatus"
                :options="statuses"
                option-label="label"
                option-value="value"
                placeholder="Select Status"
                variant="outline"
                full-width
              />
            </div>
            <div class="flex gap-2">
              <FinanceButton variant="primary" @click="handleSubmit">Submit</FinanceButton>
              <FinanceButton variant="outline-secondary">Reset</FinanceButton>
            </div>
          </div>
          <!-- Usage Example -->
          <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded text-xs">
            <div class="font-mono text-blue-600 dark:text-blue-400">
              &lt;FinanceDropdown v-model="selected" @change="handleChange" /&gt;<br>
              &lt;FinanceButton @click="handleSubmit"&gt;Submit&lt;/FinanceButton&gt;
            </div>
          </div>
        </div>
        
        <!-- Auto-scaling Feature -->
        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
          <h3 class="text-lg font-semibold text-blue-700 dark:text-blue-300 mb-3">
            ✨ Auto-scaling
          </h3>
          <p class="text-sm text-blue-600 dark:text-blue-400 mb-4">
            All dropdowns automatically scale with the store's fontSize setting!
          </p>
          <div class="mb-3">
            <FinanceDropdown 
              :options="categories"
              placeholder="Auto-scaled dropdown"
              variant="outline"
            />
          </div>
          <!-- Usage Example -->
          <div class="bg-white dark:bg-gray-600 p-2 rounded text-xs">
            <div class="font-mono text-blue-600 dark:text-blue-400">
              <span class="text-green-600 dark:text-green-400">// No manual styling needed!</span><br>
              &lt;FinanceDropdown&gt;Auto-scales&lt;/FinanceDropdown&gt;
            </div>
          </div>
        </div>
      </div>
    </div>
    
    
    <!-- Additional Examples Section -->
    <div class="mt-8 p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
      <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Advanced Examples</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
        <!-- Height Alignment Demo -->
        <div>
          <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Height Alignment (Large)</h4>
          <div class="flex items-center gap-2">
            <FinanceDropdown 
              :options="statuses"
              option-label="label"
              option-value="value"
              size="large"
              placeholder="Status Filter"
              variant="filled"
            />
            <FinanceButton size="large" variant="primary">Submit</FinanceButton>
          </div>
        </div>
        
        <!-- Multiple Selection Demo -->
        <div>
          <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Form Layout</h4>
          <div class="space-y-2">
            <FinanceDropdown 
              :options="categories"
              placeholder="Select Category"
              variant="outline"
              full-width
            />
            <div class="flex gap-2">
              <FinanceButton variant="success" size="small">Apply</FinanceButton>
              <FinanceButton variant="outline-secondary" size="small">Clear</FinanceButton>
            </div>
          </div>
        </div>
      </div>
      <!-- Usage Example -->
      <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded text-xs">
        <div class="font-mono text-blue-600 dark:text-blue-400">
          &lt;FinanceDropdown size="large" variant="filled" /&gt;<br>
          &lt;FinanceButton size="large"&gt;Perfect alignment&lt;/FinanceButton&gt;
        </div>
      </div>
    </div>
    
  </div>
</template>

<style scoped>
/* Demo-specific styles */
.font-mono {
  font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
}

/* Code example styling */
.font-mono .text-blue-600 {
  color: #2563eb;
}

/* Dark mode adjustments */
.dark .font-mono .text-blue-600 {
  color: #60a5fa;
}
</style>
