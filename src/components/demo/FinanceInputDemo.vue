<script setup>
import { ref } from 'vue'
import FinanceInput from '../ui/FinanceInput.vue'
import FinanceButton from '../ui/FinanceButton.vue'

// Demo form data
const basicInput = ref('')
const emailInput = ref('')
const passwordInput = ref('')
const numberInput = ref('')
const searchInput = ref('')

// Form data
const firstName = ref('')
const lastName = ref('')
const companyEmail = ref('')
const phoneNumber = ref('')
const website = ref('')

// Validation states
const emailError = ref('')
const passwordError = ref('')

// Loading state
const isLoading = ref(false)

// Methods
const validateEmail = () => {
  if (companyEmail.value && !companyEmail.value.includes('@')) {
    emailError.value = 'Please enter a valid email address'
  } else {
    emailError.value = ''
  }
}

const validatePassword = () => {
  if (passwordInput.value && passwordInput.value.length < 6) {
    passwordError.value = 'Password must be at least 6 characters'
  } else {
    passwordError.value = ''
  }
}

const submitForm = async () => {
  isLoading.value = true
  // Simulate API call
  setTimeout(() => {
    isLoading.value = false
    console.log('Form submitted:', {
      firstName: firstName.value,
      lastName: lastName.value,
      email: companyEmail.value,
      phone: phoneNumber.value,
      website: website.value
    })
  }, 2000)
}

const clearForm = () => {
  firstName.value = ''
  lastName.value = ''
  companyEmail.value = ''
  phoneNumber.value = ''
  website.value = ''
  emailError.value = ''
  passwordError.value = ''
}
</script>

<template>
  <div>
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
      FinanceInput Component Demo
    </h2>
    
    <!-- Three Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      
      <!-- Column 1: Basic Inputs & Variants -->
      <div class="space-y-6">
        <!-- Basic Input Types Section -->
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">
            Input Types
          </h3>
          <div class="space-y-3 mb-3">
            <FinanceInput 
              v-model="basicInput" 
              type="text" 
              placeholder="Text input"
              variant="outline"
              size="normal"
            />
            <FinanceInput 
              v-model="emailInput" 
              type="email" 
              placeholder="Email input"
              variant="outline"
              size="normal"
            />
            <FinanceInput 
              v-model="passwordInput" 
              type="password" 
              placeholder="Password input"
              variant="outline"
              size="normal"
            />
            <FinanceInput 
              v-model="numberInput" 
              type="number" 
              placeholder="Number input"
              variant="outline"
              size="normal"
            />
            <FinanceInput 
              v-model="searchInput" 
              type="search" 
              placeholder="Search input"
              variant="outline"
              size="normal"
            />
          </div>
          <!-- Usage Example -->
          <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded text-xs">
            <div class="font-mono text-blue-600 dark:text-blue-400">
              &lt;FinanceInput v-model="value" type="text" /&gt;<br>
              &lt;FinanceInput v-model="email" type="email" /&gt;<br>
              &lt;FinanceInput v-model="password" type="password" /&gt;
            </div>
          </div>
        </div>
        
        <!-- Style Variants Section -->
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">
            Style Variants
          </h3>
          <div class="space-y-3 mb-3">
            <FinanceInput 
              placeholder="Outline variant" 
              variant="outline"
              size="normal"
            />
            <FinanceInput 
              placeholder="Filled variant" 
              variant="filled"
              size="normal"
            />
            <FinanceInput 
              placeholder="Ghost variant" 
              variant="ghost"
              size="normal"
            />
          </div>
          <!-- Usage Example -->
          <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded text-xs">
            <div class="font-mono text-blue-600 dark:text-blue-400">
              &lt;FinanceInput variant="outline" /&gt;<br>
              &lt;FinanceInput variant="filled" /&gt;<br>
              &lt;FinanceInput variant="ghost" /&gt;
            </div>
          </div>
        </div>
      </div>

      <!-- Column 2: Sizes & Icons -->
      <div class="space-y-6">
        <!-- Size Variants Section -->
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">
            Size Variants
          </h3>
          <div class="space-y-3 mb-3">
            <div class="flex items-end gap-2">
              <FinanceInput 
                placeholder="Small" 
                size="small"
                variant="outline"
              />
              <FinanceInput 
                placeholder="Normal" 
                size="normal"
                variant="outline"
              />
            </div>
            <FinanceInput 
              placeholder="Large size input" 
              size="large"
              variant="outline"
            />
          </div>
          <!-- Usage Example -->
          <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded text-xs">
            <div class="font-mono text-blue-600 dark:text-blue-400">
              &lt;FinanceInput size="small" /&gt;<br>
              &lt;FinanceInput size="normal" /&gt;<br>
              &lt;FinanceInput size="large" /&gt;
            </div>
          </div>
        </div>
        
        <!-- With Icons Section -->
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">
            With Icons
          </h3>
          <div class="space-y-3 mb-3">
            <FinanceInput 
              placeholder="Search..." 
              icon-left="fas fa-search"
              variant="outline"
              size="normal"
            />
            <FinanceInput 
              placeholder="Email address" 
              icon-left="fas fa-envelope"
              variant="outline"
              size="normal"
            />
            <FinanceInput 
              placeholder="Phone number" 
              icon-left="fas fa-phone"
              variant="outline"
              size="normal"
            />
            <FinanceInput 
              placeholder="Website URL" 
              icon-left="fas fa-globe"
              icon-right="fas fa-external-link-alt"
              variant="outline"
              size="normal"
            />
          </div>
          <!-- Usage Example -->
          <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded text-xs">
            <div class="font-mono text-blue-600 dark:text-blue-400">
              &lt;FinanceInput icon-left="fas fa-search" /&gt;<br>
              &lt;FinanceInput icon-right="fas fa-eye" /&gt;<br>
              &lt;FinanceInput icon-left="fas fa-user" icon-right="fas fa-check" /&gt;
            </div>
          </div>
        </div>
        
        <!-- States Section -->
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">
            States & Validation
          </h3>
          <div class="space-y-3 mb-3">
            <FinanceInput 
              placeholder="Normal state" 
              variant="outline"
              size="normal"
            />
            <FinanceInput 
              placeholder="Disabled state" 
              variant="outline"
              size="normal"
              disabled
            />
            <FinanceInput 
              v-model="companyEmail"
              placeholder="Email with validation" 
              type="email"
              variant="outline"
              size="normal"
              :error="emailError"
              @blur="validateEmail"
            />
            <FinanceInput 
              placeholder="Required field" 
              variant="outline"
              size="normal"
              required
              label="Required Field"
              help-text="This field is required"
            />
          </div>
          <!-- Usage Example -->
          <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded text-xs">
            <div class="font-mono text-blue-600 dark:text-blue-400">
              &lt;FinanceInput disabled /&gt;<br>
              &lt;FinanceInput required /&gt;<br>
              &lt;FinanceInput :error="errorMessage" /&gt;
            </div>
          </div>
        </div>
      </div>

      <!-- Column 3: Forms & Integration -->
      <div class="space-y-6">
        <!-- Form Integration Section -->
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">
            Form Integration
          </h3>
          <div class="space-y-4 mb-3">
            <FinanceInput 
              v-model="firstName"
              label="First Name"
              placeholder="Enter first name" 
              variant="outline"
              size="normal"
              required
            />
            <FinanceInput 
              v-model="lastName"
              label="Last Name"
              placeholder="Enter last name" 
              variant="outline"
              size="normal"
              required
            />
            <FinanceInput 
              v-model="companyEmail"
              label="Email"
              type="email"
              placeholder="Enter email address" 
              variant="outline"
              size="normal"
              required
              icon-left="fas fa-envelope"
              :error="emailError"
              @blur="validateEmail"
            />
            <FinanceInput 
              v-model="phoneNumber"
              label="Phone"
              type="tel"
              placeholder="Enter phone number" 
              variant="outline"
              size="normal"
              icon-left="fas fa-phone"
              help-text="Include country code"
            />
            <div class="flex gap-2 pt-2">
              <FinanceButton 
                variant="primary" 
                size="normal"
                :loading="isLoading"
                @click="submitForm"
              >
                Submit
              </FinanceButton>
              <FinanceButton 
                variant="outline-secondary" 
                size="normal"
                @click="clearForm"
              >
                Clear
              </FinanceButton>
            </div>
          </div>
          <!-- Usage Example -->
          <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded text-xs">
            <div class="font-mono text-blue-600 dark:text-blue-400">
              &lt;FinanceInput v-model="firstName" label="Name" required /&gt;<br>
              &lt;FinanceInput @blur="validate" :error="error" /&gt;
            </div>
          </div>
        </div>
        
        <!-- Full Width Section -->
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">
            Full Width
          </h3>
          <div class="space-y-3 mb-3">
            <FinanceInput 
              placeholder="Full width input" 
              variant="outline"
              size="normal"
              full-width
            />
            <FinanceInput 
              placeholder="Full width with icon" 
              variant="filled"
              size="normal"
              full-width
              icon-left="fas fa-search"
            />
          </div>
          <!-- Usage Example -->
          <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded text-xs">
            <div class="font-mono text-blue-600 dark:text-blue-400">
              &lt;FinanceInput full-width /&gt;<br>
              &lt;FinanceInput full-width variant="filled" /&gt;
            </div>
          </div>
        </div>
        
        <!-- Auto-scaling Feature -->
        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
          <h3 class="text-lg font-semibold text-blue-700 dark:text-blue-300 mb-3">
            ✨ Auto-scaling
          </h3>
          <p class="text-sm text-blue-600 dark:text-blue-400 mb-4">
            All inputs automatically scale with the store's fontSize setting and match FinanceDropdown dimensions!
          </p>
          <div class="mb-3">
            <FinanceInput 
              placeholder="Auto-scaled input" 
              variant="outline"
              size="normal"
            />
          </div>
          <!-- Usage Example -->
          <div class="bg-white dark:bg-gray-600 p-2 rounded text-xs">
            <div class="font-mono text-blue-600 dark:text-blue-400">
              <span class="text-green-600 dark:text-green-400">// Perfect height matching with FinanceDropdown!</span><br>
              &lt;FinanceInput&gt;Auto-scales&lt;/FinanceInput&gt;
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Height Matching Demonstration -->
    <div class="mt-8 p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
      <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Perfect Height Matching with FinanceDropdown</h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-3">
        <!-- Small size -->
        <div>
          <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Small Size (32px)</h4>
          <div class="flex gap-2">
            <FinanceInput placeholder="Input" size="small" variant="outline" />
            <FinanceButton size="small" variant="primary">Button</FinanceButton>
          </div>
        </div>
        
        <!-- Normal size -->
        <div>
          <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Normal Size (40px)</h4>
          <div class="flex gap-2">
            <FinanceInput placeholder="Input" size="normal" variant="outline" />
            <FinanceButton size="normal" variant="primary">Button</FinanceButton>
          </div>
        </div>
        
        <!-- Large size -->
        <div>
          <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Large Size (48px)</h4>
          <div class="flex gap-2">
            <FinanceInput placeholder="Input" size="large" variant="outline" />
            <FinanceButton size="large" variant="primary">Button</FinanceButton>
          </div>
        </div>
      </div>
      <!-- Usage Example -->
      <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded text-xs">
        <div class="font-mono text-blue-600 dark:text-blue-400">
          &lt;FinanceInput size="normal" variant="outline" /&gt;<br>
          &lt;FinanceButton size="normal"&gt;Perfect alignment&lt;/FinanceButton&gt;
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
</style>
