<script setup>
import { ref } from 'vue'
import { useScaling } from 'panadero-shared-styling'

// UI Components
import InventoryButton from '../ui/InventoryButton.vue'
import InventoryInput from '../ui/InventoryInput.vue'
import InventoryDropdown from '../ui/InventoryDropdown.vue'
import InventoryValueCard from '../ui/InventoryValueCard.vue'
import StatusBadge from '../ui/StatusBadge.vue'

const { fontSizes, scalingStyles } = useScaling()

// Demo data
const demoInput = ref('')
const demoDropdown = ref('')
const demoCheckbox = ref(false)

const dropdownOptions = [
  { value: 'option1', label: 'Option 1' },
  { value: 'option2', label: 'Option 2' },
  { value: 'option3', label: 'Option 3' }
]

const handleButtonClick = (variant) => {
  console.log(`${variant} button clicked`)
}
</script>

<template>
  <div class="ui-elements-section">
    <!-- Header -->
    <div class="mb-8">
      <h2 :style="scalingStyles.titleFontSize" class="font-bold text-gray-900 dark:text-white mb-2">
        UI Elements Demo
      </h2>
      <p :style="scalingStyles.textFontSize" class="text-gray-600 dark:text-gray-400">
        Interactive demonstration of inventory module UI components
      </p>
    </div>

    <!-- Buttons -->
    <div class="mb-8">
        <h3 :style="scalingStyles.subtitleFontSize" class="font-semibold text-gray-900 dark:text-white mb-4">
        Buttons
      </h3>
      
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex flex-wrap gap-4">
          <InventoryButton
            @click="handleButtonClick('primary')"
            variant="primary"
            size="sm"
          >
            Primary Small
          </InventoryButton>
          
          <InventoryButton
            @click="handleButtonClick('primary')"
            variant="primary"
            size="md"
          >
            Primary Medium
          </InventoryButton>
          
          <InventoryButton
            @click="handleButtonClick('secondary')"
            variant="secondary"
            size="md"
          >
            Secondary
          </InventoryButton>
          
          <InventoryButton
            @click="handleButtonClick('outline')"
            variant="outline"
            size="md"
          >
            Outline
          </InventoryButton>
          
          <InventoryButton
            @click="handleButtonClick('ghost')"
            variant="ghost"
            size="md"
          >
            Ghost
          </InventoryButton>
          
          <InventoryButton
            @click="handleButtonClick('danger')"
            variant="danger"
            size="md"
          >
            Danger
          </InventoryButton>
          
          <InventoryButton
            :loading="true"
            variant="primary"
            size="md"
          >
            Loading
          </InventoryButton>
          
          <InventoryButton
            :disabled="true"
            variant="primary"
            size="md"
          >
            Disabled
          </InventoryButton>
        </div>
      </div>
    </div>

    <!-- Form Elements -->
    <div class="mb-8">
        <h3 :style="scalingStyles.subtitleFontSize" class="font-semibold text-gray-900 dark:text-white mb-4">
        Form Elements
      </h3>
      
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <InventoryInput
            v-model="demoInput"
            label="Text Input"
            placeholder="Enter some text..."
            icon="fas fa-edit"
          />
          
          <InventoryInput
            label="Email Input"
            type="email"
            placeholder="Enter email address..."
            icon="fas fa-envelope"
          />
          
          <InventoryInput
            label="Number Input"
            type="number"
            placeholder="Enter a number..."
            icon="fas fa-hashtag"
          />
          
          <InventoryInput
            label="Date Input"
            type="date"
            icon="fas fa-calendar"
          />
          
          <InventoryDropdown
            v-model="demoDropdown"
            :options="dropdownOptions"
            label="Dropdown"
            placeholder="Select an option..."
          />
          
          <InventoryInput
            label="Textarea"
            type="textarea"
            placeholder="Enter multiple lines of text..."
            :rows="4"
          />
        </div>
      </div>
    </div>

    <!-- Value Cards -->
    <div class="mb-8">
        <h3 :style="scalingStyles.subtitleFontSize" class="font-semibold text-gray-900 dark:text-white mb-4">
        Value Cards
      </h3>
      
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <InventoryValueCard
          title="Total Items"
          value="1,234"
          icon="fas fa-boxes"
          color="blue"
          :trend="12"
        />
        
        <InventoryValueCard
          title="Low Stock"
          value="23"
          icon="fas fa-exclamation-triangle"
          color="red"
          :trend="-5"
        />
        
        <InventoryValueCard
          title="Total Value"
          value="$45,678"
          icon="fas fa-dollar-sign"
          color="green"
          :trend="8"
        />
        
        <InventoryValueCard
          title="Warehouses"
          value="5"
          icon="fas fa-warehouse"
          color="purple"
          :trend="0"
        />
      </div>
    </div>

    <!-- Status Badges -->
    <div class="mb-8">
        <h3 :style="scalingStyles.subtitleFontSize" class="font-semibold text-gray-900 dark:text-white mb-4">
        Status Badges
      </h3>
      
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex flex-wrap gap-4">
          <StatusBadge status="active" text="Active" />
          <StatusBadge status="inactive" text="Inactive" />
          <StatusBadge status="pending" text="Pending" />
          <StatusBadge status="approved" text="Approved" />
          <StatusBadge status="completed" text="Completed" />
          <StatusBadge status="cancelled" text="Cancelled" />
          <StatusBadge status="success" text="Success" />
          <StatusBadge status="warning" text="Warning" />
          <StatusBadge status="error" text="Error" />
          <StatusBadge status="critical" text="Critical" />
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.ui-elements-section {
  padding: 0;
}
</style>
