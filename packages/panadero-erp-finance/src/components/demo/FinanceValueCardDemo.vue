<script setup>
import { ref } from 'vue';
import { useFinanceStore } from '../../stores/financeStore';
import FinanceValueCard from '../ui/FinanceValueCard.vue';

const store = useFinanceStore();

// Demo data
const kpiData = ref({
  currentPeriod: 'Q4 2025',
  journalEntries: 145,
  receivablesOutstanding: 25000,
  payablesOutstanding: 18000,
  netCash: 7000
});

const demoValues = ref({
  lowValue: 500,
  mediumValue: 15000,
  highValue: 35000,
  negativeValue: -2500,
  percentage: 75.5,
  count: 42
});
</script>

<template>
  <div class="finance-value-card-demo space-y-8">
    <!-- Header -->
    <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
      <h2 :style="{ fontSize: `${store.fontSizes.base + 4}px` }" 
          class="font-bold text-gray-900 dark:text-white">
        FinanceValueCard Component Demo
      </h2>
      <p :style="{ fontSize: `${store.fontSizes.base}px` }" 
         class="text-gray-600 dark:text-gray-400 mt-2">
        Reusable value cards with auto-scaling, theming, and auto-coloring based on thresholds
      </p>
    </div>

    <!-- 2-Row Layout Examples -->
    <div>
      <h3 :style="{ fontSize: `${store.fontSizes.base + 2}px` }" 
          class="font-semibold text-gray-900 dark:text-white mb-4">
        2-Row Layout Examples
      </h3>
      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <FinanceValueCard 
          title="Total Receivables"
          :value="25000"
          rows="2-row"
          format="currency"
          color="positive"
          icon="fas fa-arrow-circle-up"
        />
        <FinanceValueCard 
          title="Outstanding Payables"
          :value="18000"
          rows="2-row"
          format="currency"
          color="negative"
          trend="down"
        />
        <FinanceValueCard 
          title="Collection Rate"
          :value="85.5"
          rows="2-row"
          format="percentage"
          color="warning"
          trend="up"
        />
        <FinanceValueCard 
          title="Active Accounts"
          :value="142"
          rows="2-row"
          format="number"
          color="info"
          icon="fas fa-list"
        />
      </div>
    </div>

    <!-- 3-Row Layout Examples -->
    <div>
      <h3 :style="{ fontSize: `${store.fontSizes.base + 2}px` }" 
          class="font-semibold text-gray-900 dark:text-white mb-4">
        3-Row Layout Examples (KPI Style)
      </h3>
      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <FinanceValueCard 
          title="Current Period"
          :value="kpiData.currentPeriod"
          :subtitle="`Journal Entries: ${kpiData.journalEntries}`"
          rows="3-row"
          color="info"
          icon="fas fa-calendar"
        />
        <FinanceValueCard 
          title="Receivables Outstanding"
          :value="kpiData.receivablesOutstanding"
          subtitle="Top overdue AR below"
          rows="3-row"
          format="currency"
          color="positive"
          trend="up"
        />
        <FinanceValueCard 
          title="Payables Outstanding"
          :value="kpiData.payablesOutstanding"
          subtitle="Top overdue AP below"
          rows="3-row"
          format="currency"
          color="negative"
          trend="down"
        />
        <FinanceValueCard 
          title="Net Cash (Period)"
          :value="kpiData.netCash"
          subtitle="Inflow − Outflow"
          rows="3-row"
          format="currency"
          color="info"
          icon="fas fa-chart-line"
        />
      </div>
    </div>

    <!-- Auto-Coloring Examples -->
    <div>
      <h3 :style="{ fontSize: `${store.fontSizes.base + 2}px` }" 
          class="font-semibold text-gray-900 dark:text-white mb-4">
        Auto-Coloring Based on Thresholds
      </h3>
      <p :style="{ fontSize: `${store.fontSizes.base - 1}px` }" 
         class="text-gray-600 dark:text-gray-400 mb-4">
        These cards automatically change color based on min/max thresholds: Good ≤ 10,000, Warning ≤ 25,000, Danger > 25,000
      </p>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <FinanceValueCard 
          title="Low Value (Good)"
          :value="demoValues.lowValue"
          rows="2-row"
          format="currency"
          color="auto"
          :max-good="10000"
          :max-warning="25000"
        />
        <FinanceValueCard 
          title="Medium Value (Warning)"
          :value="demoValues.mediumValue"
          rows="2-row"
          format="currency"
          color="auto"
          :max-good="10000"
          :max-warning="25000"
        />
        <FinanceValueCard 
          title="High Value (Danger)"
          :value="demoValues.highValue"
          rows="2-row"
          format="currency"
          color="auto"
          :max-good="10000"
          :max-warning="25000"
        />
      </div>
    </div>

    <!-- Size Variants -->
    <div>
      <h3 :style="{ fontSize: `${store.fontSizes.base + 2}px` }" 
          class="font-semibold text-gray-900 dark:text-white mb-4">
        Size Variants
      </h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <FinanceValueCard 
          title="Small Card"
          :value="1250"
          rows="2-row"
          format="currency"
          color="positive"
          size="small"
        />
        <FinanceValueCard 
          title="Normal Card"
          :value="2500"
          rows="2-row"
          format="currency"
          color="info"
          size="normal"
        />
        <FinanceValueCard 
          title="Large Card"
          :value="5000"
          rows="2-row"
          format="currency"
          color="warning"
          size="large"
        />
      </div>
    </div>

    <!-- Variant Styles -->
    <div>
      <h3 :style="{ fontSize: `${store.fontSizes.base + 2}px` }" 
          class="font-semibold text-gray-900 dark:text-white mb-4">
        Variant Styles
      </h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <FinanceValueCard 
          title="Default Variant"
          :value="1000"
          rows="2-row"
          format="currency"
          color="neutral"
          variant="default"
        />
        <FinanceValueCard 
          title="Bordered Variant"
          :value="2000"
          rows="2-row"
          format="currency"
          color="info"
          variant="bordered"
        />
        <FinanceValueCard 
          title="Elevated Variant"
          :value="3000"
          rows="2-row"
          format="currency"
          color="positive"
          variant="elevated"
        />
      </div>
    </div>

    <!-- Alignment Options -->
    <div>
      <h3 :style="{ fontSize: `${store.fontSizes.base + 2}px` }" 
          class="font-semibold text-gray-900 dark:text-white mb-4">
        Alignment Options
      </h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <FinanceValueCard 
          title="Left Aligned"
          :value="1500"
          rows="2-row"
          format="currency"
          color="positive"
          alignment="left"
        />
        <FinanceValueCard 
          title="Center Aligned"
          :value="2500"
          rows="2-row"
          format="currency"
          color="info"
          alignment="center"
        />
        <FinanceValueCard 
          title="Right Aligned"
          :value="3500"
          rows="2-row"
          format="currency"
          color="warning"
          alignment="right"
        />
      </div>
    </div>

    <!-- Format Options -->
    <div>
      <h3 :style="{ fontSize: `${store.fontSizes.base + 2}px` }" 
          class="font-semibold text-gray-900 dark:text-white mb-4">
        Format Options
      </h3>
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <FinanceValueCard 
          title="Currency Format"
          :value="12500.75"
          rows="2-row"
          format="currency"
          color="positive"
        />
        <FinanceValueCard 
          title="Number Format"
          :value="1234567"
          rows="2-row"
          format="number"
          color="info"
        />
        <FinanceValueCard 
          title="Percentage Format"
          :value="demoValues.percentage"
          rows="2-row"
          format="percentage"
          color="warning"
        />
        <FinanceValueCard 
          title="Text Format"
          value="Q4 2025"
          rows="2-row"
          format="text"
          color="neutral"
        />
      </div>
    </div>

    <!-- Custom Layout Example -->
    <div>
      <h3 :style="{ fontSize: `${store.fontSizes.base + 2}px` }" 
          class="font-semibold text-gray-900 dark:text-white mb-4">
        Custom Layout (Slot-based)
      </h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <FinanceValueCard 
          title="Custom Layout"
          :value="15000"
          rows="custom"
          format="currency"
          color="auto"
          :max-good="10000"
          :max-warning="20000"
        >
          <template #default="{ title, value, colorClasses, sizeStyles }">
            <div class="flex items-center justify-between">
              <div>
                <div :style="{ fontSize: sizeStyles.titleSize }" class="text-gray-500 dark:text-gray-400">
                  {{ title }}
                </div>
                <div :style="{ fontSize: sizeStyles.valueSize }" :class="['font-bold', colorClasses]">
                  {{ value }}
                </div>
              </div>
              <div class="text-right">
                <i class="fas fa-chart-bar text-3xl text-blue-500"></i>
                <div :style="{ fontSize: sizeStyles.subtitleSize }" class="text-gray-400 mt-1">
                  vs Last Period
                </div>
              </div>
            </div>
          </template>
        </FinanceValueCard>
      </div>
    </div>

    <!-- Interactive Example -->
    <div>
      <h3 :style="{ fontSize: `${store.fontSizes.base + 2}px` }" 
          class="font-semibold text-gray-900 dark:text-white mb-4">
        Interactive Threshold Demo
      </h3>
      <p :style="{ fontSize: `${store.fontSizes.base - 1}px` }" 
         class="text-gray-600 dark:text-gray-400 mb-4">
        Change the value below to see how auto-coloring works with thresholds
      </p>
      
      <div class="mb-4">
        <label :style="{ fontSize: `${store.fontSizes.base - 1}px` }" 
               class="block text-gray-700 dark:text-gray-300 mb-2">
          Value: ${{ demoValues.lowValue.toLocaleString() }}
        </label>
        <input 
          type="range" 
          v-model="demoValues.lowValue" 
          min="0" 
          max="50000" 
          step="500"
          class="w-full h-2 bg-gray-200 dark:bg-gray-600 rounded-lg appearance-none cursor-pointer"
        />
        <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-1">
          <span>$0</span>
          <span class="text-green-600">Good: ≤ $10,000</span>
          <span class="text-yellow-600">Warning: ≤ $25,000</span>
          <span class="text-red-600">Danger: > $25,000</span>
          <span>$50,000</span>
        </div>
      </div>
      
      <FinanceValueCard 
        title="Interactive Value"
        :value="demoValues.lowValue"
        subtitle="Drag slider above to change value"
        rows="3-row"
        format="currency"
        color="auto"
        :max-good="10000"
        :max-warning="25000"
        size="large"
        variant="elevated"
      />
    </div>

    <!-- Code Examples -->
    <div class="mt-8 bg-gray-50 dark:bg-gray-800 rounded-lg p-6">
      <h3 :style="{ fontSize: `${store.fontSizes.base + 2}px` }" 
          class="font-semibold text-gray-900 dark:text-white mb-4">
        Usage Examples
      </h3>
      
      <div class="space-y-4">
        <div>
          <h4 :style="{ fontSize: `${store.fontSizes.base}px` }" 
              class="font-medium text-gray-800 dark:text-gray-200 mb-2">
            Basic 2-Row Card:
          </h4>
          <pre :style="{ fontSize: `${store.fontSizes.base - 2}px` }" 
               class="bg-gray-100 dark:bg-gray-900 p-3 rounded text-gray-800 dark:text-gray-200 overflow-x-auto"><code>&lt;FinanceValueCard 
  title="Total Revenue"
  :value="25000"
  format="currency"
  color="positive"
  rows="2-row"
/&gt;</code></pre>
        </div>

        <div>
          <h4 :style="{ fontSize: `${store.fontSizes.base}px` }" 
              class="font-medium text-gray-800 dark:text-gray-200 mb-2">
            Auto-Coloring with Thresholds:
          </h4>
          <pre :style="{ fontSize: `${store.fontSizes.base - 2}px` }" 
               class="bg-gray-100 dark:bg-gray-900 p-3 rounded text-gray-800 dark:text-gray-200 overflow-x-auto"><code>&lt;FinanceValueCard 
  title="Outstanding Amount"
  :value="15000"
  format="currency"
  color="auto"
  :max-good="10000"
  :max-warning="25000"
/&gt;</code></pre>
        </div>

        <div>
          <h4 :style="{ fontSize: `${store.fontSizes.base}px` }" 
              class="font-medium text-gray-800 dark:text-gray-200 mb-2">
            3-Row KPI Card:
          </h4>
          <pre :style="{ fontSize: `${store.fontSizes.base - 2}px` }" 
               class="bg-gray-100 dark:bg-gray-900 p-3 rounded text-gray-800 dark:text-gray-200 overflow-x-auto"><code>&lt;FinanceValueCard 
  title="Current Period"
  value="Q4 2025"
  subtitle="Journal Entries: 145"
  rows="3-row"
  color="info"
  icon="fas fa-calendar"
/&gt;</code></pre>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Custom slider styling */
input[type="range"]::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 20px;
  height: 20px;
  background: #3b82f6;
  border-radius: 50%;
  cursor: pointer;
  border: 2px solid #ffffff;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

input[type="range"]::-moz-range-thumb {
  width: 20px;
  height: 20px;
  background: #3b82f6;
  border-radius: 50%;
  cursor: pointer;
  border: 2px solid #ffffff;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.dark input[type="range"]::-webkit-slider-thumb {
  background: #60a5fa;
  border-color: #1f2937;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.dark input[type="range"]::-moz-range-thumb {
  background: #60a5fa;
  border-color: #1f2937;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}
</style>
