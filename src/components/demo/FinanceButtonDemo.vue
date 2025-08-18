<script setup>
import { ref } from 'vue';
import FinanceButton from '../../components/ui/FinanceButton.vue';

// Reactive data
const counter = ref(0);
const isLoading = ref(false);

// Methods
const increment = () => {
  counter.value++;
};

const decrement = () => {
  counter.value--;
};

const reset = () => {
  counter.value = 0;
};

const simulateLoading = async () => {
  isLoading.value = true;
  await new Promise(resolve => setTimeout(resolve, 2000));
  isLoading.value = false;
};
</script>

<template>
  <div>
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
      FinanceButton Component Demo
    </h2>
    
    <!-- Three Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      
      <!-- Column 1: Variants & Basic Features -->
      <div class="space-y-6">
        <!-- Solid Variants Section -->
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">
            Solid Variants
          </h3>
          <div class="grid grid-cols-2 gap-2 mb-3">
            <FinanceButton variant="primary" size="small">Primary</FinanceButton>
            <FinanceButton variant="secondary" size="small">Secondary</FinanceButton>
            <FinanceButton variant="success" size="small">Success</FinanceButton>
            <FinanceButton variant="danger" size="small">Danger</FinanceButton>
            <FinanceButton variant="warning" size="small">Warning</FinanceButton>
            <FinanceButton variant="info" size="small">Info</FinanceButton>
          </div>
          <!-- Usage Example -->
          <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded text-xs">
            <div class="font-mono text-blue-600 dark:text-blue-400">
              &lt;FinanceButton variant="primary"&gt;Save&lt;/FinanceButton&gt;<br>
              &lt;FinanceButton variant="danger"&gt;Delete&lt;/FinanceButton&gt;
            </div>
          </div>
        </div>

        <!-- Outline Variants Section -->
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">
            Outline Variants
          </h3>
          <div class="grid grid-cols-2 gap-2 mb-3">
            <FinanceButton variant="outline-primary" size="small">Primary</FinanceButton>
            <FinanceButton variant="outline-secondary" size="small">Secondary</FinanceButton>
            <FinanceButton variant="outline-success" size="small">Success</FinanceButton>
            <FinanceButton variant="outline-danger" size="small">Danger</FinanceButton>
            <FinanceButton variant="outline-warning" size="small">Warning</FinanceButton>
            <FinanceButton variant="outline-info" size="small">Info</FinanceButton>
          </div>
          <!-- Usage Example -->
          <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded text-xs">
            <div class="font-mono text-blue-600 dark:text-blue-400">
              &lt;FinanceButton variant="outline-primary"&gt;Continue&lt;/FinanceButton&gt;<br>
              &lt;FinanceButton variant="outline-danger"&gt;Cancel&lt;/FinanceButton&gt;
            </div>
          </div>
        </div>

        <!-- Ghost & Generic -->
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">
            Special Variants
          </h3>
          <div class="grid grid-cols-2 gap-2 mb-3">
            <FinanceButton variant="ghost" size="small">Ghost</FinanceButton>
            <FinanceButton variant="outline" size="small">Outline</FinanceButton>
          </div>
          <!-- Usage Example -->
          <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded text-xs">
            <div class="font-mono text-blue-600 dark:text-blue-400">
              &lt;FinanceButton variant="ghost"&gt;Subtle Action&lt;/FinanceButton&gt;<br>
              &lt;FinanceButton variant="outline"&gt;Generic Border&lt;/FinanceButton&gt;
            </div>
          </div>
        </div>
      </div>

      <!-- Column 2: Sizes, States & Icons -->
      <div class="space-y-6">
        <!-- Button Sizes Section -->
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">
            Button Sizes
          </h3>
          <div class="space-y-3 mb-3">
            <div class="flex items-end gap-2">
              <FinanceButton size="small" variant="primary">Small</FinanceButton>
              <FinanceButton size="normal" variant="primary">Normal</FinanceButton>
              <FinanceButton size="large" variant="primary">Large</FinanceButton>
            </div>
            <div class="flex items-end gap-2">
              <FinanceButton size="small" variant="outline-success">Small Outline</FinanceButton>
              <FinanceButton size="normal" variant="outline-success">Normal Outline</FinanceButton>
              <FinanceButton size="large" variant="outline-success">Large Outline</FinanceButton>
            </div>
          </div>
          <!-- Usage Example -->
          <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded text-xs">
            <div class="font-mono text-blue-600 dark:text-blue-400">
              &lt;FinanceButton size="small"&gt;Small&lt;/FinanceButton&gt;<br>
              &lt;FinanceButton size="normal"&gt;Normal&lt;/FinanceButton&gt;<br>
              &lt;FinanceButton size="large"&gt;Large&lt;/FinanceButton&gt;
            </div>
          </div>
        </div>
        
        <!-- States Section -->
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">
            Button States
          </h3>
          <div class="space-y-3 mb-3">
            <div class="flex gap-2">
              <FinanceButton variant="primary">Normal</FinanceButton>
              <FinanceButton variant="primary" disabled>Disabled</FinanceButton>
              <FinanceButton variant="primary" loading>Loading</FinanceButton>
            </div>
            <div class="flex gap-2">
              <FinanceButton variant="outline-danger">Outline Normal</FinanceButton>
              <FinanceButton variant="outline-danger" disabled>Outline Disabled</FinanceButton>
            </div>
          </div>
          <!-- Usage Example -->
          <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded text-xs">
            <div class="font-mono text-blue-600 dark:text-blue-400">
              &lt;FinanceButton disabled&gt;Disabled&lt;/FinanceButton&gt;<br>
              &lt;FinanceButton :loading="isLoading"&gt;Save&lt;/FinanceButton&gt;
            </div>
          </div>
        </div>
        
        <!-- With Icons Section -->
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">
            With Icons
          </h3>
          <div class="space-y-2 mb-3">
            <div class="flex gap-2">
              <FinanceButton variant="success" icon-left="fas fa-plus">Create</FinanceButton>
              <FinanceButton variant="info" icon-right="fas fa-download">Export</FinanceButton>
            </div>
            <div class="flex gap-2">
              <FinanceButton variant="warning" icon-left="fas fa-exclamation-triangle">Warning</FinanceButton>
              <FinanceButton variant="outline-primary" icon-left="fas fa-save">Save</FinanceButton>
            </div>
            <FinanceButton variant="outline-danger" icon-left="fas fa-trash" full-width>Delete (Full Width)</FinanceButton>
          </div>
          <!-- Usage Example -->
          <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded text-xs">
            <div class="font-mono text-blue-600 dark:text-blue-400">
              &lt;FinanceButton icon-left="fas fa-plus"&gt;Create&lt;/FinanceButton&gt;<br>
              &lt;FinanceButton icon-right="fas fa-download"&gt;Export&lt;/FinanceButton&gt;<br>
              &lt;FinanceButton icon-left="fas fa-save" icon-right="fas fa-arrow-right"&gt;Both&lt;/FinanceButton&gt;
            </div>
          </div>
        </div>
      </div>

      <!-- Column 3: Interactive Examples & Usage -->
      <div class="space-y-6">
        <!-- Interactive Counter -->
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">
            Interactive Counter
          </h3>
          <div class="text-center mb-4">
            <span class="text-2xl font-bold text-gray-700 dark:text-gray-300">{{ counter }}</span>
          </div>
          <div class="space-y-2 mb-3">
            <div class="flex gap-2">
              <FinanceButton variant="outline-success" @click="increment" icon-left="fas fa-plus">+</FinanceButton>
              <FinanceButton variant="outline-danger" @click="decrement" icon-left="fas fa-minus">-</FinanceButton>
              <FinanceButton variant="outline-secondary" @click="reset" icon-left="fas fa-redo">Reset</FinanceButton>
            </div>
          </div>
          <!-- Usage Example -->
          <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded text-xs">
            <div class="font-mono text-blue-600 dark:text-blue-400">
              &lt;FinanceButton @click="increment"&gt;+&lt;/FinanceButton&gt;<br>
              &lt;FinanceButton @click="handleClick"&gt;Click Me&lt;/FinanceButton&gt;
            </div>
          </div>
        </div>
        
        <!-- Loading Simulation -->
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">
            Loading States
          </h3>
          <div class="space-y-2 mb-3">
            <FinanceButton 
              variant="primary" 
              :loading="isLoading" 
              @click="simulateLoading"
              full-width
            >
              {{ isLoading ? 'Processing...' : 'Simulate Loading' }}
            </FinanceButton>
            <FinanceButton 
              variant="outline-primary" 
              :loading="isLoading" 
              @click="simulateLoading"
              full-width
            >
              {{ isLoading ? 'Loading...' : 'Outline Loading' }}
            </FinanceButton>
          </div>
          <!-- Usage Example -->
          <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded text-xs">
            <div class="font-mono text-blue-600 dark:text-blue-400">
              &lt;FinanceButton :loading="isLoading" @click="save"&gt;<br>
              &nbsp;&nbsp;{{ isLoading ? 'Saving...' : 'Save' }}<br>
              &lt;/FinanceButton&gt;
            </div>
          </div>
        </div>

        <!-- Auto-scaling Feature -->
        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
          <h3 class="text-lg font-semibold text-blue-700 dark:text-blue-300 mb-3">
            ✨ Auto-scaling
          </h3>
          <p class="text-sm text-blue-600 dark:text-blue-400 mb-4">
            All buttons automatically scale with the store's fontSize setting!
          </p>
          <div class="flex gap-2 mb-3">
            <FinanceButton variant="primary" size="small">Auto-scaled</FinanceButton>
            <FinanceButton variant="success" icon-left="fas fa-check" size="normal">With Icon</FinanceButton>
          </div>
          <!-- Usage Example -->
          <div class="bg-white dark:bg-gray-600 p-2 rounded text-xs">
            <div class="font-mono text-blue-600 dark:text-blue-400">
              <span class="text-green-600 dark:text-green-400">// No manual styling needed!</span><br>
              &lt;FinanceButton&gt;Auto-scales&lt;/FinanceButton&gt;
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Full Width Demonstration -->
    <div class="mt-8 p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
      <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Full Width Examples</h3>
      <div class="space-y-2 mb-3">
        <FinanceButton variant="primary" full-width>Full Width Primary Button</FinanceButton>
        <FinanceButton variant="outline-success" full-width icon-left="fas fa-check">Full Width Outline with Icon</FinanceButton>
      </div>
      <!-- Usage Example -->
      <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded text-xs">
        <div class="font-mono text-blue-600 dark:text-blue-400">
          &lt;FinanceButton full-width&gt;Full Width&lt;/FinanceButton&gt;<br>
          &lt;FinanceButton full-width type="submit"&gt;Submit Form&lt;/FinanceButton&gt;
        </div>
      </div>
    </div>

    <!-- Comprehensive Usage Examples -->
    <div class="mt-8 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
      <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Usage Examples</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        
        <!-- Basic usage -->
        <div class="bg-white dark:bg-gray-600 p-3 rounded border">
          <div class="text-xs text-gray-500 dark:text-gray-400 mb-2">Basic usage (auto-scaling):</div>
          <div class="font-mono text-xs text-blue-600 dark:text-blue-400">
            &lt;FinanceButton variant="primary"&gt;<br>
            &nbsp;&nbsp;Save<br>
            &lt;/FinanceButton&gt;
          </div>
          <div class="text-xs text-green-600 dark:text-green-400 mt-1">✓ Automatically inherits store.fontSizes.base and scaling styles</div>
        </div>
        
        <!-- With size and loading -->
        <div class="bg-white dark:bg-gray-600 p-3 rounded border">
          <div class="text-xs text-gray-500 dark:text-gray-400 mb-2">With size and loading:</div>
          <div class="font-mono text-xs text-blue-600 dark:text-blue-400">
            &lt;FinanceButton<br>
            &nbsp;&nbsp;variant="success"<br>
            &nbsp;&nbsp;size="large"<br>
            &nbsp;&nbsp;:loading="isLoading"<br>
            &nbsp;&nbsp;@click="handleSave"<br>
            &gt;<br>
            &nbsp;&nbsp;Save Changes<br>
            &lt;/FinanceButton&gt;
          </div>
        </div>
        
        <!-- With icons -->
        <div class="bg-white dark:bg-gray-600 p-3 rounded border">
          <div class="text-xs text-gray-500 dark:text-gray-400 mb-2">With icons:</div>
          <div class="font-mono text-xs text-blue-600 dark:text-blue-400">
            &lt;FinanceButton<br>
            &nbsp;&nbsp;variant="danger"<br>
            &nbsp;&nbsp;icon-left="fas fa-trash"<br>
            &nbsp;&nbsp;@click="handleDelete"<br>
            &gt;<br>
            &nbsp;&nbsp;Delete<br>
            &lt;/FinanceButton&gt;
          </div>
        </div>
        
        <!-- Full width submit button -->
        <div class="bg-white dark:bg-gray-600 p-3 rounded border">
          <div class="text-xs text-gray-500 dark:text-gray-400 mb-2">Full width submit button:</div>
          <div class="font-mono text-xs text-blue-600 dark:text-blue-400">
            &lt;FinanceButton<br>
            &nbsp;&nbsp;type="submit"<br>
            &nbsp;&nbsp;variant="primary"<br>
            &nbsp;&nbsp;full-width<br>
            &nbsp;&nbsp;:disabled="!isValid"<br>
            &gt;<br>
            &nbsp;&nbsp;Submit Form<br>
            &lt;/FinanceButton&gt;
          </div>
        </div>

        <!-- Outline transparent variant -->
        <div class="bg-white dark:bg-gray-600 p-3 rounded border">
          <div class="text-xs text-gray-500 dark:text-gray-400 mb-2">Outline variant (transparent with colored border):</div>
          <div class="font-mono text-xs text-blue-600 dark:text-blue-400">
            &lt;FinanceButton<br>
            &nbsp;&nbsp;variant="outline-primary"<br>
            &nbsp;&nbsp;icon-right="fas fa-arrow-right"<br>
            &gt;<br>
            &nbsp;&nbsp;Continue<br>
            &lt;/FinanceButton&gt;
          </div>
        </div>

        <!-- Multiple icons -->
        <div class="bg-white dark:bg-gray-600 p-3 rounded border">
          <div class="text-xs text-gray-500 dark:text-gray-400 mb-2">With left and right icons:</div>
          <div class="font-mono text-xs text-blue-600 dark:text-blue-400">
            &lt;FinanceButton<br>
            &nbsp;&nbsp;variant="warning"<br>
            &nbsp;&nbsp;icon-left="fas fa-exclamation-triangle"<br>
            &nbsp;&nbsp;icon-right="fas fa-chart-bar"<br>
            &gt;<br>
            &nbsp;&nbsp;Generate Report<br>
            &lt;/FinanceButton&gt;
          </div>
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
