<template>
  <div>
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
      FinanceToggle Component Demo
    </h2>
    
    <!-- Three Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      
      <!-- Column 1: Sizes & Colors -->
      <div class="space-y-6">
        <!-- Size Variants Section -->
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">
            Size Variants
          </h3>
          <div class="space-y-3 mb-3">
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-600 dark:text-gray-400">Small:</span>
              <FinanceToggle v-model="toggle1" label="Small Toggle" size="small" variant="blue" />
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-600 dark:text-gray-400">Normal:</span>
              <FinanceToggle v-model="toggle2" label="Normal Toggle" size="normal" variant="green" />
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-600 dark:text-gray-400">Large:</span>
              <FinanceToggle v-model="toggle3" label="Large Toggle" size="large" variant="purple" />
            </div>
          </div>
          <!-- Usage Example -->
          <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded text-xs">
            <div class="font-mono text-blue-600 dark:text-blue-400">
              &lt;FinanceToggle size="small" /&gt;<br>
              &lt;FinanceToggle size="normal" /&gt;<br>
              &lt;FinanceToggle size="large" /&gt;
            </div>
          </div>
        </div>
        
        <!-- Color Variants Section -->
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">
            Color Variants
          </h3>
          <div class="space-y-2 mb-3">
            <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded">
              <span class="text-sm text-gray-600 dark:text-gray-400">Blue:</span>
              <FinanceToggle v-model="toggle4" label="Blue Toggle" variant="blue" />
            </div>
            <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded">
              <span class="text-sm text-gray-600 dark:text-gray-400">Green:</span>
              <FinanceToggle v-model="toggle5" label="Green Toggle" variant="green" />
            </div>
            <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded">
              <span class="text-sm text-gray-600 dark:text-gray-400">Red:</span>
              <FinanceToggle v-model="toggle6" label="Red Toggle" variant="red" />
            </div>
            <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded">
              <span class="text-sm text-gray-600 dark:text-gray-400">Purple:</span>
              <FinanceToggle v-model="toggle7" label="Purple Toggle" variant="purple" />
            </div>
          </div>
          <!-- Usage Example -->
          <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded text-xs">
            <div class="font-mono text-blue-600 dark:text-blue-400">
              &lt;FinanceToggle variant="blue" /&gt;<br>
              &lt;FinanceToggle variant="green" /&gt;<br>
              &lt;FinanceToggle variant="red" /&gt;<br>
              &lt;FinanceToggle variant="purple" /&gt;
            </div>
          </div>
        </div>
      </div>

      <!-- Column 2: Interactive Examples -->
      <div class="space-y-6">
        <!-- Settings Demo Section -->
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">
            Settings Demo
          </h3>
          <div class="space-y-3 mb-3">
            <div class="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
              <span class="text-sm text-gray-600 dark:text-gray-400">Dark Mode:</span>
              <FinanceToggle v-model="darkMode" label="Dark Mode" variant="blue" />
            </div>
            <div class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
              <span class="text-sm text-gray-600 dark:text-gray-400">Notifications:</span>
              <FinanceToggle v-model="notifications" label="Notifications" variant="green" />
            </div>
            <div class="flex items-center justify-between p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
              <span class="text-sm text-gray-600 dark:text-gray-400">Auto-save:</span>
              <FinanceToggle v-model="autoSave" label="Auto-save" variant="red" />
            </div>
          </div>
          <!-- Usage Example -->
          <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded text-xs">
            <div class="font-mono text-blue-600 dark:text-blue-400">
              &lt;FinanceToggle v-model="darkMode" label="Dark Mode" /&gt;<br>
              &lt;FinanceToggle v-model="notifications" variant="green" /&gt;
            </div>
          </div>
        </div>
        
        <!-- Form Integration Section -->
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">
            Form Integration
          </h3>
          <div class="space-y-3 mb-3">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Account Settings
              </label>
              <div class="space-y-2">
                <div class="flex items-center justify-between">
                  <span class="text-sm text-gray-600 dark:text-gray-400">Email alerts</span>
                  <FinanceToggle v-model="emailAlerts" variant="blue" size="small" />
                </div>
                <div class="flex items-center justify-between">
                  <span class="text-sm text-gray-600 dark:text-gray-400">SMS alerts</span>
                  <FinanceToggle v-model="smsAlerts" variant="green" size="small" />
                </div>
                <div class="flex items-center justify-between">
                  <span class="text-sm text-gray-600 dark:text-gray-400">Marketing emails</span>
                  <FinanceToggle v-model="marketing" variant="purple" size="small" />
                </div>
              </div>
            </div>
          </div>
          <!-- Usage Example -->
          <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded text-xs">
            <div class="font-mono text-blue-600 dark:text-blue-400">
              &lt;FinanceToggle v-model="emailAlerts" size="small" /&gt;<br>
              &lt;label&gt;Enable notifications&lt;/label&gt;
            </div>
          </div>
        </div>
      </div>

      <!-- Column 3: Status & Advanced -->
      <div class="space-y-6">
        <!-- Current States Section -->
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">
            Current States
          </h3>
          <div class="space-y-2 mb-3">
            <div class="flex items-center gap-2">
              <span class="w-3 h-3 rounded-full" :class="darkMode ? 'bg-blue-500' : 'bg-gray-300'"></span>
              <span class="text-sm text-gray-600 dark:text-gray-400">Dark Mode: {{ darkMode ? 'ON' : 'OFF' }}</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="w-3 h-3 rounded-full" :class="notifications ? 'bg-green-500' : 'bg-gray-300'"></span>
              <span class="text-sm text-gray-600 dark:text-gray-400">Notifications: {{ notifications ? 'ON' : 'OFF' }}</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="w-3 h-3 rounded-full" :class="autoSave ? 'bg-red-500' : 'bg-gray-300'"></span>
              <span class="text-sm text-gray-600 dark:text-gray-400">Auto-save: {{ autoSave ? 'ON' : 'OFF' }}</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="w-3 h-3 rounded-full" :class="emailAlerts ? 'bg-blue-500' : 'bg-gray-300'"></span>
              <span class="text-sm text-gray-600 dark:text-gray-400">Email: {{ emailAlerts ? 'ON' : 'OFF' }}</span>
            </div>
          </div>
          <!-- Usage Example -->
          <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded text-xs">
            <div class="font-mono text-blue-600 dark:text-blue-400">
              {{ darkMode ? 'ON' : 'OFF' }}<br>
              {{ notifications ? 'Enabled' : 'Disabled' }}
            </div>
          </div>
        </div>
        
        <!-- Custom Labels Section -->
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">
            Custom Labels
          </h3>
          <div class="space-y-3 mb-3">
            <FinanceToggle 
              v-model="customToggle1" 
              label="Feature Toggle with Custom Label" 
              variant="green" 
              size="normal"
            />
            <FinanceToggle 
              v-model="customToggle2" 
              label="Another Custom Toggle" 
              variant="purple" 
              size="small"
            />
            <FinanceToggle 
              v-model="customToggle3" 
              label="Large Custom Toggle with Long Text" 
              variant="blue" 
              size="large"
            />
          </div>
          <!-- Usage Example -->
          <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded text-xs">
            <div class="font-mono text-blue-600 dark:text-blue-400">
              &lt;FinanceToggle<br>
              &nbsp;&nbsp;v-model="enabled"<br>
              &nbsp;&nbsp;label="Custom Label"<br>
              &nbsp;&nbsp;variant="green"<br>
              /&gt;
            </div>
          </div>
        </div>
        
        <!-- Auto-scaling Feature -->
        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
          <h3 class="text-lg font-semibold text-blue-700 dark:text-blue-300 mb-3">
            ✨ Auto-scaling
          </h3>
          <p class="text-sm text-blue-600 dark:text-blue-400 mb-4">
            All toggles automatically scale with the store's fontSize setting!
          </p>
          <div class="mb-3">
            <FinanceToggle v-model="autoScaleDemo" label="Auto-scaled toggle" variant="blue" />
          </div>
          <!-- Usage Example -->
          <div class="bg-white dark:bg-gray-600 p-2 rounded text-xs">
            <div class="font-mono text-blue-600 dark:text-blue-400">
              <span class="text-green-600 dark:text-green-400">// No manual styling needed!</span><br>
              &lt;FinanceToggle&gt;Auto-scales&lt;/FinanceToggle&gt;
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import FinanceToggle from '../ui/FinanceToggle.vue';

// Demo toggle states
const toggle1 = ref(false);
const toggle2 = ref(true);
const toggle3 = ref(false);
const toggle4 = ref(true);
const toggle5 = ref(false);
const toggle6 = ref(true);
const toggle7 = ref(false);

// Interactive demo states
const darkMode = ref(false);
const notifications = ref(true);
const autoSave = ref(false);

// Form integration states
const emailAlerts = ref(true);
const smsAlerts = ref(false);
const marketing = ref(false);

// Custom toggle states
const customToggle1 = ref(false);
const customToggle2 = ref(true);
const customToggle3 = ref(false);

// Auto-scaling demo
const autoScaleDemo = ref(true);
</script>
