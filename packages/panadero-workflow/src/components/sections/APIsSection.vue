<script setup>
//import { useScaling } from '../../../../shared/composables/useScaling.js'

const props = defineProps({
  scaling: { type: Object, required: true },
  hasAccess: {
    type: Boolean,
    default: false
  }
})
</script>

<template>
  <div class="apis-section">
    <div v-if="!hasAccess" class="text-center py-16">
      <!-- Access Restricted Message -->
      <div class="mb-8">
        <i class="fas fa-lock text-6xl text-gray-400 dark:text-gray-500 mb-4"></i>
        <h2 :style="{ fontSize: scaling.font.heading }" class="font-bold text-gray-900 dark:text-white mb-4">
          Access Restricted
        </h2>
        <p :style="{ fontSize: scaling.font.subtitle }" class="text-gray-600 dark:text-gray-400 mb-8">
          API documentation and endpoints are available to authorized users only.
        </p>
      </div>

      <!-- Contact Information -->
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-8 max-w-md mx-auto">
        <h3 :style="{ fontSize: scaling.font.subtitle }" class="font-semibold text-gray-900 dark:text-white mb-4">
          Request Access
        </h3>
        <p :style="{ fontSize: scaling.font.body }" class="text-gray-600 dark:text-gray-400 mb-6">
          Contact your system administrator to request API access and documentation.
        </p>
        <div class="space-y-3 text-left">
          <div class="flex items-center space-x-3">
            <i class="fas fa-envelope text-blue-600 dark:text-blue-400"></i>
            <span :style="{ fontSize: scaling.font.body }" class="text-gray-700 dark:text-gray-300">
              support@panadero.com
            </span>
          </div>
          <div class="flex items-center space-x-3">
            <i class="fas fa-phone text-green-600 dark:text-green-400"></i>
            <span :style="{ fontSize: scaling.font.body }" class="text-gray-700 dark:text-gray-300">
              +31 (655) 328-495
            </span>
          </div>
          <div class="flex items-center space-x-3">
            <i class="fas fa-clock text-orange-600 dark:text-orange-400"></i>
            <span :style="{ fontSize: scaling.font.body }" class="text-gray-700 dark:text-gray-300">
              9 AM - 18 PM 
            </span>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="space-y-8">
      <!-- API Documentation Header -->
      <div class="text-center py-8">
        <h1 :style="{ fontSize: scaling.font.heading }" class="font-bold text-gray-900 dark:text-white mb-4">
          Finance API Documentation
        </h1>
        <p :style="{ fontSize: scaling.font.subtitle }" class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
          Comprehensive REST API documentation for the ERP Finance Module. 
          Access financial data, manage transactions, and integrate with external systems.
        </p>
      </div>

      <!-- API Endpoints Overview -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Accounts API -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
          <div class="flex items-center mb-4">
            <i class="fas fa-university text-blue-600 dark:text-blue-400 text-2xl mr-3"></i>
            <h3 :style="{ fontSize: scaling.font.subtitle }" class="font-semibold text-gray-900 dark:text-white">
              Accounts API
            </h3>
          </div>
          <p :style="{ fontSize: scaling.font.body }" class="text-gray-600 dark:text-gray-400 mb-4">
            Manage chart of accounts, account balances, and account hierarchies.
          </p>
          <div class="space-y-2">
            <div class="flex items-center text-sm">
              <span class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 px-2 py-1 rounded mr-2">GET</span>
              <code class="text-gray-700 dark:text-gray-300">/api/accounts</code>
            </div>
            <div class="flex items-center text-sm">
              <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-2 py-1 rounded mr-2">POST</span>
              <code class="text-gray-700 dark:text-gray-300">/api/accounts</code>
            </div>
            <div class="flex items-center text-sm">
              <span class="bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 px-2 py-1 rounded mr-2">PUT</span>
              <code class="text-gray-700 dark:text-gray-300">/api/accounts/{id}</code>
            </div>
          </div>
        </div>

        <!-- Transactions API -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
          <div class="flex items-center mb-4">
            <i class="fas fa-exchange-alt text-green-600 dark:text-green-400 text-2xl mr-3"></i>
            <h3 :style="{ fontSize: scaling.font.subtitle }" class="font-semibold text-gray-900 dark:text-white">
              Transactions API
            </h3>
          </div>
          <p :style="{ fontSize: scaling.font.body }" class="text-gray-600 dark:text-gray-400 mb-4">
            Create, read, update transactions and journal entries.
          </p>
          <div class="space-y-2">
            <div class="flex items-center text-sm">
              <span class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 px-2 py-1 rounded mr-2">GET</span>
              <code class="text-gray-700 dark:text-gray-300">/api/transactions</code>
            </div>
            <div class="flex items-center text-sm">
              <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-2 py-1 rounded mr-2">POST</span>
              <code class="text-gray-700 dark:text-gray-300">/api/transactions</code>
            </div>
            <div class="flex items-center text-sm">
              <span class="bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 px-2 py-1 rounded mr-2">DEL</span>
              <code class="text-gray-700 dark:text-gray-300">/api/transactions/{id}</code>
            </div>
          </div>
        </div>

        <!-- Reports API -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
          <div class="flex items-center mb-4">
            <i class="fas fa-chart-bar text-purple-600 dark:text-purple-400 text-2xl mr-3"></i>
            <h3 :style="{ fontSize: scaling.font.subtitle }" class="font-semibold text-gray-900 dark:text-white">
              Reports API
            </h3>
          </div>
          <p :style="{ fontSize: scaling.font.body }" class="text-gray-600 dark:text-gray-400 mb-4">
            Generate financial reports, statements, and analytics.
          </p>
          <div class="space-y-2">
            <div class="flex items-center text-sm">
              <span class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 px-2 py-1 rounded mr-2">GET</span>
              <code class="text-gray-700 dark:text-gray-300">/api/reports/balance-sheet</code>
            </div>
            <div class="flex items-center text-sm">
              <span class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 px-2 py-1 rounded mr-2">GET</span>
              <code class="text-gray-700 dark:text-gray-300">/api/reports/income-statement</code>
            </div>
            <div class="flex items-center text-sm">
              <span class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 px-2 py-1 rounded mr-2">GET</span>
              <code class="text-gray-700 dark:text-gray-300">/api/reports/cash-flow</code>
            </div>
          </div>
        </div>
      </div>

      <!-- Coming Soon Features -->
      <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 rounded-lg border border-blue-200 dark:border-gray-600 p-8">
        <div class="text-center">
          <i class="fas fa-rocket text-4xl text-blue-600 dark:text-blue-400 mb-4"></i>
          <h3 :style="{ fontSize: scaling.font.heading }" class="font-bold text-gray-900 dark:text-white mb-4">
            Coming Soon
          </h3>
          <p :style="{ fontSize: scaling.font.body }" class="text-gray-700 dark:text-gray-300 mb-6">
            We're working on exciting new API features and integrations.
          </p>
          
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 max-w-2xl mx-auto">
            <div class="text-center">
              <i class="fas fa-webhook text-2xl text-blue-600 dark:text-blue-400 mb-2"></i>
              <h4 :style="{ fontSize: scaling.font.subtitle }" class="font-semibold text-gray-900 dark:text-white">Webhooks</h4>
              <p :style="{ fontSize: scaling.font.body }" class="text-gray-600 dark:text-gray-400">Real-time notifications</p>
            </div>
            <div class="text-center">
              <i class="fas fa-key text-2xl text-green-600 dark:text-green-400 mb-2"></i>
              <h4 :style="{ fontSize: scaling.font.subtitle }" class="font-semibold text-gray-900 dark:text-white">OAuth 2.0</h4>
              <p :style="{ fontSize: scaling.font.body }" class="text-gray-600 dark:text-gray-400">Secure authentication</p>
            </div>
            <div class="text-center">
              <i class="fas fa-code text-2xl text-purple-600 dark:text-purple-400 mb-2"></i>
              <h4 :style="{ fontSize: scaling.font.subtitle }" class="font-semibold text-gray-900 dark:text-white">GraphQL</h4>
              <p :style="{ fontSize: scaling.font.body }" class="text-gray-600 dark:text-gray-400">Flexible queries</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* APIs section specific styles if needed */
code {
  font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
}
</style>
