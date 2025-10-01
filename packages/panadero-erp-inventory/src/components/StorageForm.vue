<!--
  Storage Form Component
  @version 1.0.0
  @date 29-Sep-2025
  @description Reusable storage form for creating and editing storages
-->
<script setup>
import { ref, computed, watch } from 'vue'
import axios from 'axios'
import { useCommonSnippets } from '../composables/useCommonSnippets.js'

// UI Components
import InventoryButton from './ui/InventoryButton.vue'
import InventoryInput from './ui/InventoryInput.vue'
import InventoryDropdown from './ui/InventoryDropdown.vue'

// Props
const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  item: {
    type: Object,
    default: null
  },
  sites: {
    type: Array,
    default: () => []
  },
  units: {
    type: Array,
    default: () => []
  }
})

// Emits
const emit = defineEmits(['close', 'saved'])

// Get common functionality
const { 
  darkModeClasses, 
  scalingStyles, 
  showNotification 
} = useCommonSnippets()

// Form state
const isSaving = ref(false)
const formErrors = ref({})

// Form data
const storageFormData = ref({
  code: '',
  type: '',
  site_id: null,
  unit_id: null,
  max_units: 0,
  comment: '',
  is_active: true,
  is_locked: false
})

// Storage type options
const storageTypeOptions = [
  { value: 'Warehouse', label: 'Warehouse' },
  { value: 'Cold Storage', label: 'Cold Storage' },
  { value: 'Dry Storage', label: 'Dry Storage' },
  { value: 'Freezer', label: 'Freezer' }
]

// Computed properties
const isEdit = computed(() => !!props.item)
const formTitle = computed(() => isEdit.value ? 'Edit Storage' : 'Add Storage')
const submitButtonText = computed(() => isSaving.value ? 'Saving...' : (isEdit.value ? 'Update Storage' : 'Create Storage'))

// Methods
const resetForm = () => {
  if (isEdit.value && props.item) {
    // Reset to original values for edit
    storageFormData.value = {
      code: props.item.code || '',
      type: props.item.type || '',
      site_id: props.item.site_id || null,
      unit_id: props.item.unit_id || null,
      max_units: props.item.max_units || 0,
      comment: props.item.comment || '',
      is_active: props.item.is_active ?? true,
      is_locked: props.item.is_locked ?? false
    }
  } else {
    // Reset to default values for new storage
    storageFormData.value = {
      code: '',
      type: '',
      site_id: null,
      unit_id: null,
      max_units: 0,
      comment: '',
      is_active: true,
      is_locked: false
    }
  }
  formErrors.value = {}
}

// Watch for storage prop changes
watch(() => props.item, (newStorage) => {
  if (newStorage) {
    // Populate form with existing storage data
    storageFormData.value = {
      code: newStorage.code || '',
      type: newStorage.type || '',
      site_id: newStorage.site_id || null,
      unit_id: newStorage.unit_id || null,
      max_units: newStorage.max_units || 0,
      comment: newStorage.comment || '',
      is_active: newStorage.is_active ?? true,
      is_locked: newStorage.is_locked ?? false
    }
  } else {
    // Reset form for new storage
    resetForm()
  }
}, { immediate: true })

// Watch for show prop changes
watch(() => props.show, (newShow) => {
  if (newShow) {
    // Clear errors when opening form
    formErrors.value = {}
  }
})

const handleClose = () => {
  emit('close')
}

const handleSave = async () => {
  try {
    isSaving.value = true
    formErrors.value = {}

    // Prepare data to send
    const dataToSend = { ...storageFormData.value }

    // Emit the data to parent component (DataTableComplete) to handle API call
    console.log('StorageForm: Emitting saved event with data:', dataToSend)
    emit('saved', dataToSend)
    handleClose()

  } catch (error) {
    console.error('Error preparing storage data:', error)
    showNotification('Error preparing storage data', 'error')
  } finally {
    isSaving.value = false
  }
}

const hasFieldError = (field) => {
  return formErrors.value[field] && formErrors.value[field].length > 0
}

const getFieldError = (field) => {
  return formErrors.value[field]?.[0] || ''
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="handleClose"></div>
    
    <!-- Modal -->
    <div class="flex min-h-full items-center justify-center p-4">
      <div :class="[darkModeClasses.modal, 'relative w-full max-w-2xl rounded-lg shadow-xl']">
        <!-- Header -->
        <div :class="[darkModeClasses.border, 'flex items-center justify-between border-b px-6 py-4']">
          <h3 :class="darkModeClasses.text" class="text-lg font-semibold">
            {{ formTitle }}
          </h3>
          <div class="flex items-center gap-2">
            <!-- Reset Button -->
            <button
              @click="resetForm"
              :class="[darkModeClasses.textSecondary, 'hover:text-gray-900 dark:hover:text-white']"
              :style="scalingStyles.iconSizeSmall"
              title="Reset Form"
            >
              <i class="fas fa-undo"></i>
            </button>
            <!-- Close Button -->
            <button
              @click="handleClose"
              :class="[darkModeClasses.textSecondary, 'hover:text-gray-900 dark:hover:text-white']"
              :style="scalingStyles.iconSizeSmall"
            >
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>

        <!-- Form Content -->
        <form @submit.prevent="handleSave" class="p-6 space-y-6">
          <!-- Code and Type Row -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Storage Code -->
            <div>
              <label :class="darkModeClasses.text" class="block text-sm font-medium mb-2">
                Storage Code *
              </label>
              <InventoryInput
                v-model="storageFormData.code"
                type="text"
                placeholder="Enter storage code"
                :error="hasFieldError('code')"
                :error-message="getFieldError('code')"
                :dark-mode-classes="darkModeClasses"
                :scaling-styles="scalingStyles"
              />
            </div>

            <!-- Storage Type -->
            <div>
              <label :class="darkModeClasses.text" class="block text-sm font-medium mb-2">
                Storage Type *
              </label>
              <InventoryDropdown
                v-model="storageFormData.type"
                :options="storageTypeOptions"
                placeholder="Select storage type"
                :error="hasFieldError('type')"
                :error-message="getFieldError('type')"
                :dark-mode-classes="darkModeClasses"
                :scaling-styles="scalingStyles"
              />
            </div>
          </div>

          <!-- Site and Unit Row -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Site -->
            <div>
              <label :class="darkModeClasses.text" class="block text-sm font-medium mb-2">
                Site *
              </label>
              <InventoryDropdown
                v-model="storageFormData.site_id"
                :options="sites.map(site => ({ value: site.id, label: site.name }))"
                placeholder="Select site"
                :error="hasFieldError('site_id')"
                :error-message="getFieldError('site_id')"
                :dark-mode-classes="darkModeClasses"
                :scaling-styles="scalingStyles"
              />
            </div>

            <!-- Unit -->
            <div>
              <label :class="darkModeClasses.text" class="block text-sm font-medium mb-2">
                Unit *
              </label>
              <InventoryDropdown
                v-model="storageFormData.unit_id"
                :options="units.map(unit => ({ value: unit.id, label: unit.name }))"
                placeholder="Select unit"
                :error="hasFieldError('unit_id')"
                :error-message="getFieldError('unit_id')"
                :dark-mode-classes="darkModeClasses"
                :scaling-styles="scalingStyles"
              />
            </div>
          </div>

          <!-- Max Units -->
          <div>
            <label :class="darkModeClasses.text" class="block text-sm font-medium mb-2">
              Max Units *
            </label>
            <InventoryInput
              v-model="storageFormData.max_units"
              type="number"
              step="0.01"
              placeholder="Enter maximum units"
              :error="hasFieldError('max_units')"
              :error-message="getFieldError('max_units')"
              :dark-mode-classes="darkModeClasses"
              :scaling-styles="scalingStyles"
            />
          </div>

          <!-- Comment -->
          <div>
            <label :class="darkModeClasses.text" class="block text-sm font-medium mb-2">
              Comment
            </label>
            <textarea
              v-model="storageFormData.comment"
              :class="[
                darkModeClasses.input,
                'w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500'
              ]"
              :style="scalingStyles.input"
              rows="3"
              placeholder="Enter storage comment"
            ></textarea>
          </div>

          <!-- Checkboxes Row -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Active -->
            <div class="flex items-center">
              <input
                v-model="storageFormData.is_active"
                type="checkbox"
                :class="[
                  'h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded',
                  darkModeClasses.input
                ]"
              />
              <label :class="[darkModeClasses.text, 'ml-2 block text-sm']">
                Active
              </label>
            </div>

            <!-- Locked -->
            <div class="flex items-center">
              <input
                v-model="storageFormData.is_locked"
                type="checkbox"
                :class="[
                  'h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded',
                  darkModeClasses.input
                ]"
              />
              <label :class="[darkModeClasses.text, 'ml-2 block text-sm']">
                Locked
              </label>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="flex justify-end gap-3 pt-4 border-t" :class="darkModeClasses.border">
            <InventoryButton
              type="button"
              variant="secondary"
              @click="handleClose"
              :dark-mode-classes="darkModeClasses"
              :scaling-styles="scalingStyles"
            >
              Cancel
            </InventoryButton>
            <InventoryButton
              type="submit"
              variant="primary"
              :disabled="isSaving"
              :dark-mode-classes="darkModeClasses"
              :scaling-styles="scalingStyles"
            >
              {{ submitButtonText }}
            </InventoryButton>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>