<script setup>
import { computed } from 'vue'
import { useFinanceStore } from '../../stores/financeStore'

const props = defineProps({
  steps: {
    type: Array,
    required: true
  },
  currentStep: {
    type: Number,
    default: 0
  },
  orientation: {
    type: String,
    default: 'horizontal',
    validator: value => ['horizontal', 'vertical'].includes(value)
  },
  showEstimatedTime: {
    type: Boolean,
    default: true
  },
  compact: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['step-click'])

const store = useFinanceStore()

// Step status mapping
const stepStatusClasses = {
  pending: 'stepper-step--pending',
  active: 'stepper-step--active',
  completed: 'stepper-step--completed',
  skipped: 'stepper-step--skipped',
  failed: 'stepper-step--failed',
  requires_approval: 'stepper-step--approval',
  approved: 'stepper-step--approved',
  rejected: 'stepper-step--rejected'
}

const stepStatusIcons = {
  pending: 'fas fa-circle',
  active: 'fas fa-play-circle',
  completed: 'fas fa-check-circle',
  skipped: 'fas fa-forward',
  failed: 'fas fa-times-circle',
  requires_approval: 'fas fa-clock',
  approved: 'fas fa-check-circle',
  rejected: 'fas fa-times-circle'
}

// Computed classes for container
const containerClasses = computed(() => [
  'workflow-stepper',
  `workflow-stepper--${props.orientation}`,
  {
    'workflow-stepper--compact': props.compact
  }
])

// Check if step is clickable
const isStepClickable = (step, index) => {
  return index <= props.currentStep && step.status !== 'pending'
}

// Handle step click
const handleStepClick = (step, index) => {
  if (isStepClickable(step, index)) {
    emit('step-click', { step, index })
  }
}

// Format estimated duration
const formatDuration = (duration) => {
  if (!duration) return ''
  return `~${duration}`
}
</script>

<template>
  <div :class="containerClasses">
    <div class="stepper-container">
      <div 
        v-for="(step, index) in steps" 
        :key="step.id"
        class="stepper-step"
        :class="[
          stepStatusClasses[step.status] || stepStatusClasses.pending,
          {
            'stepper-step--clickable': isStepClickable(step, index),
            'stepper-step--current': index === currentStep
          }
        ]"
        @click="handleStepClick(step, index)"
      >
        <!-- Step Number/Icon -->
        <div class="stepper-step__indicator">
          <div class="stepper-step__circle">
            <i 
              :class="stepStatusIcons[step.status] || stepStatusIcons.pending"
              :style="{ fontSize: `${store.fontSizes.base - 2}px` }"
            ></i>
            <span 
              v-if="!compact && step.status === 'pending'"
              class="stepper-step__number"
              :style="{ fontSize: `${store.fontSizes.base - 4}px` }"
            >
              {{ index + 1 }}
            </span>
          </div>
          
          <!-- Connector Line -->
          <div 
            v-if="index < steps.length - 1" 
            class="stepper-step__connector"
          ></div>
        </div>

        <!-- Step Content -->
        <div class="stepper-step__content">
          <div class="stepper-step__header">
            <h3 
              class="stepper-step__title"
              :style="{ fontSize: `${store.fontSizes.base}px` }"
            >
              {{ step.name }}
            </h3>
            
            <!-- Status Badge -->
            <span 
              class="stepper-step__status"
              :style="{ fontSize: `${store.fontSizes.base - 4}px` }"
            >
              {{ step.status.replace('_', ' ').toUpperCase() }}
            </span>
          </div>

          <!-- Description -->
          <p 
            v-if="step.description && !compact"
            class="stepper-step__description"
            :style="{ fontSize: `${store.fontSizes.base - 2}px` }"
          >
            {{ step.description }}
          </p>

          <!-- Estimated Time -->
          <div 
            v-if="showEstimatedTime && step.estimated_duration && !compact"
            class="stepper-step__meta"
          >
            <span 
              class="stepper-step__duration"
              :style="{ fontSize: `${store.fontSizes.base - 3}px` }"
            >
              <i class="fas fa-clock mr-1"></i>
              {{ formatDuration(step.estimated_duration) }}
            </span>
          </div>

          <!-- Approval Info -->
          <div 
            v-if="step.approval_required && !compact"
            class="stepper-step__approval"
          >
            <span 
              class="stepper-step__approval-required"
              :style="{ fontSize: `${store.fontSizes.base - 3}px` }"
            >
              <i class="fas fa-user-check mr-1"></i>
              Approval Required
            </span>
            <div 
              v-if="step.approver_roles"
              class="stepper-step__approvers"
              :style="{ fontSize: `${store.fontSizes.base - 4}px` }"
            >
              {{ step.approver_roles.join(', ') }}
            </div>
          </div>

          <!-- Timestamps -->
          <div 
            v-if="(step.started_at || step.completed_at) && !compact"
            class="stepper-step__timestamps"
          >
            <div 
              v-if="step.started_at"
              class="stepper-step__timestamp"
              :style="{ fontSize: `${store.fontSizes.base - 4}px` }"
            >
              Started: {{ new Date(step.started_at).toLocaleDateString() }}
            </div>
            <div 
              v-if="step.completed_at"
              class="stepper-step__timestamp"
              :style="{ fontSize: `${store.fontSizes.base - 4}px` }"
            >
              Completed: {{ new Date(step.completed_at).toLocaleDateString() }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Base stepper styles */
.workflow-stepper {
  @apply w-full;
}

.stepper-container {
  @apply relative;
}

/* Horizontal Layout */
.workflow-stepper--horizontal .stepper-container {
  @apply flex items-start space-x-4 overflow-x-auto pb-4;
}

.workflow-stepper--horizontal .stepper-step {
  @apply flex-shrink-0 min-w-0 relative;
  flex: 1 1 200px;
}

.workflow-stepper--horizontal .stepper-step__indicator {
  @apply flex flex-col items-center mb-3;
}

.workflow-stepper--horizontal .stepper-step__connector {
  @apply absolute top-4 left-full w-full h-0.5 bg-gray-300 dark:bg-gray-600;
  margin-left: 1rem;
  margin-right: 1rem;
  z-index: 1;
}

/* Vertical Layout */
.workflow-stepper--vertical .stepper-container {
  @apply space-y-6;
}

.workflow-stepper--vertical .stepper-step {
  @apply flex items-start space-x-4;
}

.workflow-stepper--vertical .stepper-step__indicator {
  @apply flex flex-col items-center flex-shrink-0;
}

.workflow-stepper--vertical .stepper-step__connector {
  @apply w-0.5 h-6 bg-gray-300 dark:bg-gray-600 mt-2;
}

.workflow-stepper--vertical .stepper-step__content {
  @apply flex-1 min-w-0;
}

/* Step Indicator */
.stepper-step__circle {
  @apply relative flex items-center justify-center w-8 h-8 rounded-full border-2 transition-all duration-200;
  background-color: #f9fafb;
  border-color: #d1d5db;
  color: #6b7280;
}

.stepper-step__number {
  @apply absolute inset-0 flex items-center justify-center font-medium;
}

/* Step Content */
.stepper-step__content {
  @apply text-center;
}

.workflow-stepper--vertical .stepper-step__content {
  @apply text-left;
}

.stepper-step__header {
  @apply flex flex-col items-center space-y-1 mb-2;
}

.workflow-stepper--vertical .stepper-step__header {
  @apply flex-row justify-between items-start;
}

.stepper-step__title {
  @apply font-semibold text-gray-900 dark:text-white leading-tight;
}

.stepper-step__status {
  @apply px-2 py-1 rounded-full font-medium;
}

.stepper-step__description {
  @apply text-gray-600 dark:text-gray-400 leading-relaxed mb-2;
}

.stepper-step__meta,
.stepper-step__approval,
.stepper-step__timestamps {
  @apply text-gray-500 dark:text-gray-500 space-y-1;
}

.stepper-step__approval-required {
  @apply flex items-center text-orange-600 dark:text-orange-400;
}

.stepper-step__approvers {
  @apply text-gray-500 dark:text-gray-400 italic ml-5;
}

.stepper-step__timestamp {
  @apply text-gray-500 dark:text-gray-400;
}

/* Step States */
.stepper-step--pending .stepper-step__circle {
  background-color: #f9fafb;
  border-color: #d1d5db;
  color: #6b7280;
}

.stepper-step--pending .stepper-step__status {
  @apply bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400;
}

.stepper-step--active .stepper-step__circle {
  background-color: #dbeafe;
  border-color: #3b82f6;
  color: #3b82f6;
}

.stepper-step--active .stepper-step__status {
  @apply bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300;
}

.stepper-step--completed .stepper-step__circle {
  background-color: #dcfce7;
  border-color: #22c55e;
  color: #22c55e;
}

.stepper-step--completed .stepper-step__status {
  @apply bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300;
}

.stepper-step--completed .stepper-step__connector {
  @apply bg-green-400 dark:bg-green-600;
}

.stepper-step--approval .stepper-step__circle {
  background-color: #fef3c7;
  border-color: #f59e0b;
  color: #f59e0b;
}

.stepper-step--approval .stepper-step__status {
  @apply bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300;
}

.stepper-step--approved .stepper-step__circle {
  background-color: #dcfce7;
  border-color: #22c55e;
  color: #22c55e;
}

.stepper-step--approved .stepper-step__status {
  @apply bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300;
}

.stepper-step--rejected .stepper-step__circle {
  background-color: #fee2e2;
  border-color: #ef4444;
  color: #ef4444;
}

.stepper-step--rejected .stepper-step__status {
  @apply bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300;
}

.stepper-step--failed .stepper-step__circle {
  background-color: #fee2e2;
  border-color: #ef4444;
  color: #ef4444;
}

.stepper-step--failed .stepper-step__status {
  @apply bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300;
}

.stepper-step--skipped .stepper-step__circle {
  background-color: #f3f4f6;
  border-color: #9ca3af;
  color: #9ca3af;
}

.stepper-step--skipped .stepper-step__status {
  @apply bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400;
}

/* Interactive States */
.stepper-step--clickable {
  @apply cursor-pointer;
}

.stepper-step--clickable:hover .stepper-step__circle {
  @apply transform scale-105 shadow-md;
}

.stepper-step--current .stepper-step__title {
  @apply text-blue-600 dark:text-blue-400;
}

/* Compact Mode */
.workflow-stepper--compact .stepper-step__content {
  @apply hidden;
}

.workflow-stepper--compact .stepper-step__circle {
  @apply w-6 h-6;
}

.workflow-stepper--compact.workflow-stepper--horizontal .stepper-container {
  @apply justify-center;
}

.workflow-stepper--compact.workflow-stepper--horizontal .stepper-step {
  flex: 0 0 auto;
}

/* Responsive Design */
@media (max-width: 768px) {
  .workflow-stepper--horizontal {
    @apply overflow-x-auto;
  }
  
  .workflow-stepper--horizontal .stepper-step {
    min-width: 150px;
  }
  
  .stepper-step__description {
    @apply text-xs;
  }
}
</style>
