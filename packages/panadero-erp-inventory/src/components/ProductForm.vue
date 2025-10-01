<!--
<!--
  Product Form Component
  @version 1.0.15
  @date 23-Sep-2025
  @description Reusable product form for creating and editing products
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
  productTypes: {
    type: Array,
    default: () => []
  },
  productGroups: {
    type: Array,
    default: () => []
  },
  brands: {
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

// Form data
const productFormData = ref({
  name: '',
  identifier: '',
  erp_product_type_id: '',
  erp_product_group_id: '',
  erp_brand_id: '',
  unit_id: '',
  comment: '',
  is_active: true,
  is_locked: false
})

// Loading state
const isSaving = ref(false)

// Convert raw data to dropdown options format
const productTypeOptions = computed(() => 
  props.productTypes.map(type => ({
    value: type.id,
    label: type.name
  }))
)

const filteredProductGroups = computed(() => {
  if (!productFormData.value.erp_product_type_id) {
    return []
  }
  
  const typeId = parseInt(productFormData.value.erp_product_type_id)
  
  return props.productGroups
    .filter(group => parseInt(group.erp_product_type_id) === typeId)
    .map(group => ({
      value: group.id,
      label: group.name
    }))
})

const brandOptions = computed(() => 
  props.brands.map(brand => ({
    value: brand.id,
    label: brand.name
  }))
)

const unitOptions = computed(() => 
  props.units.map(unit => ({
    value: unit.id,
    label: unit.name
  }))
)

// Watch for product prop changes - handle new vs edit differently
watch(() => props.item, (newProduct) => {
  if (newProduct) {
    // For editing existing product, use the REAL identifier
    productFormData.value = {
      name: newProduct.name || '',
      identifier: newProduct.identifier || '<AUTO>', // Fallback to <AUTO> if empty
      erp_product_type_id: newProduct.erp_product_type_id || '',
      erp_product_group_id: newProduct.erp_product_group_id || '',
      erp_brand_id: newProduct.erp_brand_id || '',
      unit_id: newProduct.unit_id || '',
      comment: newProduct.comment || '',
      is_active: newProduct.is_active !== false,
      is_locked: newProduct.is_locked === true
    }
  } else {
    // For new product, ALWAYS show <AUTO>
    productFormData.value = {
      name: '',
      identifier: '<AUTO>', // ALWAYS <AUTO> for new records
      erp_product_type_id: '',
      erp_product_group_id: '',
      erp_brand_id: '',
      unit_id: '',
      comment: '',
      is_active: true,
      is_locked: false
    }
  }
}, { immediate: true })

// Methods
const resetForm = () => {
  if (props.item) {
    // For editing existing product, reset to original values
    productFormData.value = {
      name: props.item.name || '',
      identifier: props.item.identifier || '<AUTO>',
      erp_product_type_id: props.item.erp_product_type_id || '',
      erp_product_group_id: props.item.erp_product_group_id || '',
      erp_brand_id: props.item.erp_brand_id || '',
      unit_id: props.item.unit_id || '',
      comment: props.item.comment || '',
      is_active: props.item.is_active !== false,
      is_locked: props.item.is_locked === true
    }
  } else {
    // For new product, reset to default values
    productFormData.value = {
      name: '',
      identifier: '<AUTO>',
      erp_product_type_id: '',
      erp_product_group_id: '',
      erp_brand_id: '',
      unit_id: '',
      comment: '',
      is_active: true,
      is_locked: false
    }
  }
  showNotification('Form reset to original values', 'info')
}

const handleSave = async () => {
  // Remove identifier validation - it's auto-generated in backend for NEW records only
  if (!productFormData.value.name) {
    showNotification('Please fill in required fields', 'error')
    return
  }

  if (!productFormData.value.erp_product_type_id) {
    showNotification('Please select a product type', 'error')
    return
  }

  if (!productFormData.value.unit_id) {
    showNotification('Please select a unit', 'error')
    return
  }

  isSaving.value = true

  try {
    // Prepare data to send
    const dataToSend = { ...productFormData.value }
    console.log('ProductForm: Data to send:', dataToSend)
    
    // For NEW records, don't send identifier (let backend generate it)
    // For UPDATES, send the existing identifier
    if (!props.item) {
      // NEW RECORD: Don't send identifier, let backend generate it
      delete dataToSend.identifier
      console.log('ProductForm: NEW record, removed identifier')
    } else {
      console.log('ProductForm: UPDATE record, keeping identifier')
    }

    // Emit the data to parent component (DataTableComplete) to handle API call
    console.log('ProductForm: Emitting saved event with data:', dataToSend)
    emit('saved', dataToSend)
    handleClose()

  } catch (error) {
    console.error('Error preparing product data:', error)
    showNotification('Error preparing product data', 'error')
  } finally {
    isSaving.value = false
  }
}

const handleClose = () => {
  emit('close')
}

const isEditing = computed(() => !!props.item)

// Auto-generate identifier with format: BRAND(3) + GROUP(3) + TEMP(5) = 11 chars
const generateIdentifier = () => {
  // Get brand identifier (3 chars)
  const brand = props.brands.find(b => b.id === productFormData.value.erp_brand_id)
  const brandCode = brand?.identifier || 'XXX'
  const brandPart = brandCode.substring(0, 3).toUpperCase().padEnd(3, 'X')
  
  // Get product group code (3 chars)
  const productGroup = props.productGroups.find(g => g.id === productFormData.value.erp_product_group_id)
  const groupCode = productGroup?.code || 'XXX'
  const groupPart = groupCode.substring(0, 3).toUpperCase().padEnd(3, 'X')
  
  // Generate 5 temporary digits (will be replaced by backend with sequential number)
  const tempPart = '00000' // Just show the pattern
  
  return `${brandPart}${groupPart}${tempPart}`
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div :class="[darkModeClasses.modal, 'rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto']">
      <div class="flex justify-between items-center mb-4">
        <h3 :style="scalingStyles.subtitleFontSize" :class="darkModeClasses.text" class="text-lg font-semibold">
          {{ isEditing ? 'Edit' : 'Add New' }} Product
        </h3>
        <div class="flex items-center gap-2">
          <button 
            @click="resetForm" 
            :disabled="isSaving" 
            :class="darkModeClasses.textSecondary" 
            class="hover:opacity-75 p-1"
            title="Reset Form"
          >
            <i class="fas fa-undo"></i>
          </button>
          <button 
            @click="handleClose" 
            :disabled="isSaving" 
            :class="darkModeClasses.textSecondary" 
            class="hover:opacity-75"
          >
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>

      <form @submit.prevent="handleSave" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <InventoryInput
            v-model="productFormData.identifier"
            label="SKU/Identifier"
            required
            :disabled="isSaving"
            :style="scalingStyles.input"
            :class="darkModeClasses.input"
          />
          <InventoryInput
            v-model="productFormData.name"
            label="Product Name"
            required
            :disabled="isSaving"
            :style="scalingStyles.input"
            :class="darkModeClasses.input"
          />
        </div>

        <InventoryDropdown
          v-model="productFormData.erp_product_type_id"
          label="Product Type"
          :options="productTypeOptions"
          :disabled="isSaving"
          required
          :style="scalingStyles.select"
          :class="darkModeClasses.input"
        />

        <InventoryDropdown
          v-model="productFormData.erp_product_group_id"
          label="Product Group"
          :options="filteredProductGroups"
          :disabled="!productFormData.erp_product_type_id || isSaving"
          :placeholder="productFormData.erp_product_type_id ? 'Select a product group' : 'Select a product type first'"
          :style="scalingStyles.select"
          :class="darkModeClasses.input"
        />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <InventoryDropdown
            v-model="productFormData.erp_brand_id"
            label="Brand"
            :options="brandOptions"
            :disabled="isSaving"
            :style="scalingStyles.select"
            :class="darkModeClasses.input"
          />
          <InventoryDropdown
            v-model="productFormData.unit_id"
            label="Unit"
            :options="unitOptions"
            :disabled="isSaving"
            required
            :style="scalingStyles.select"
            :class="darkModeClasses.input"
          />
        </div>

        <InventoryInput
          v-model="productFormData.comment"
          label="Description"
          type="textarea"
          :disabled="isSaving"
          :style="scalingStyles.textarea"
          :class="darkModeClasses.input"
        />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="flex items-center space-x-2">
            <input
              v-model="productFormData.is_active"
              type="checkbox"
              :disabled="isSaving"
              :class="darkModeClasses.input"
            />
            <label :class="darkModeClasses.text">Active</label>
          </div>
          <div class="flex items-center space-x-2">
            <input
              v-model="productFormData.is_locked"
              type="checkbox"
              :disabled="isSaving"
              :class="darkModeClasses.input"
            />
            <label :class="darkModeClasses.text">Locked</label>
          </div>
        </div>

        <div class="flex justify-end gap-2 pt-4">
          <InventoryButton 
            @click="handleClose" 
            variant="secondary" 
            :disabled="isSaving"
            :style="scalingStyles.button"
          >
            Cancel
          </InventoryButton>
          <InventoryButton 
            type="submit" 
            variant="primary" 
            :disabled="isSaving"
            :style="scalingStyles.button"
          >
            <i v-if="isSaving" class="fas fa-spinner fa-spin mr-2"></i>
            {{ isSaving ? 'Saving...' : (isEditing ? 'Update' : 'Create') }}
          </InventoryButton>
        </div>
      </form>
    </div>
  </div>
</template>
