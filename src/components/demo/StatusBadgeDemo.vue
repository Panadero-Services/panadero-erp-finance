<script setup>
import StatusBadge from '../ui/StatusBadge.vue';
</script>

<template>
  <div class="status-badge-demo dark:bg-gray-900 p-6">
    <h2 class="text-2xl font-bold mb-6 dark:text-white">Status Badge Component Demo</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Auto-detected Statuses -->
      <div class="space-y-4">
        <h3 class="text-lg font-semibold dark:text-gray-300">Auto-detected Statuses</h3>
        
        <div class="space-y-2">
          <div class="flex items-center gap-2">
            <span class="text-sm text-gray-600 dark:text-gray-400 w-24">Draft:</span>
            <StatusBadge status="draft" />
          </div>
          <div class="flex items-center gap-2">
            <span class="text-sm text-gray-600 dark:text-gray-400 w-24">Pending:</span>
            <StatusBadge status="pending" />
          </div>
          <div class="flex items-center gap-2">
            <span class="text-sm text-gray-600 dark:text-gray-400 w-24">Approved:</span>
            <StatusBadge status="approved" />
          </div>
          <div class="flex items-center gap-2">
            <span class="text-sm text-gray-600 dark:text-gray-400 w-24">Paid:</span>
            <StatusBadge status="paid" />
          </div>
          <div class="flex items-center gap-2">
            <span class="text-sm text-gray-600 dark:text-gray-400 w-24">Overdue:</span>
            <StatusBadge status="overdue" />
          </div>
          <div class="flex items-center gap-2">
            <span class="text-sm text-gray-600 dark:text-gray-400 w-24">Inflow:</span>
            <StatusBadge status="inflow" />
          </div>
          <div class="flex items-center gap-2">
            <span class="text-sm text-gray-600 dark:text-gray-400 w-24">Outflow:</span>
            <StatusBadge status="outflow" />
          </div>
        </div>
      </div>

      <!-- Manual Variants -->
      <div class="space-y-4">
        <h3 class="text-lg font-semibold dark:text-gray-300">Manual Variants</h3>
        
        <div class="space-y-2">
          <div class="flex items-center gap-2">
            <span class="text-sm text-gray-600 dark:text-gray-400 w-24">Success:</span>
            <StatusBadge status="Success" variant="success" />
          </div>
          <div class="flex items-center gap-2">
            <span class="text-sm text-gray-600 dark:text-gray-400 w-24">Warning:</span>
            <StatusBadge status="Warning" variant="warning" />
          </div>
          <div class="flex items-center gap-2">
            <span class="text-sm text-gray-600 dark:text-gray-400 w-24">Danger:</span>
            <StatusBadge status="Danger" variant="danger" />
          </div>
          <div class="flex items-center gap-2">
            <span class="text-sm text-gray-600 dark:text-gray-400 w-24">Info:</span>
            <StatusBadge status="Info" variant="info" />
          </div>
        </div>
      </div>
    </div>

    <!-- Usage Examples -->
    <div class="mt-8">
      <h3 class="text-lg font-semibold mb-4 dark:text-gray-300">Usage Examples</h3>
      
      <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded-lg">
        <h4 class="font-medium mb-2 dark:text-gray-200">Basic Usage:</h4>
        <code class="text-sm text-gray-700 dark:text-gray-300">
          &lt;StatusBadge :status="payable.status" /&gt;
        </code>
        
        <h4 class="font-medium mb-2 mt-4 dark:text-gray-200">With Variant:</h4>
        <code class="text-sm text-gray-700 dark:text-gray-300">
          &lt;StatusBadge :status="'Success'" variant="success" /&gt;
        </code>
        
        <h4 class="font-medium mb-2 mt-4 dark:text-gray-200">Auto-detection:</h4>
        <code class="text-sm text-gray-700 dark:text-gray-300">
          &lt;StatusBadge :status="transaction.type" /&gt; // Automatically detects 'inflow'/'outflow'
        </code>
      </div>
    </div>
  </div>
</template>
