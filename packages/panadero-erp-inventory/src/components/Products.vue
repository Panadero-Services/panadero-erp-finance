<!--
  Products Component
  @version 1.0.13
  @date 17-Sep-2025
  @description Product master data management with groups, types, and categories
-->
<script setup>
import { ref, computed, onMounted } from 'vue'
import { useProducts } from '../composables/useProducts.js'
import { useCommonSnippets } from '../composables/useCommonSnippets.js'

// UI Components
import InventoryButton from './ui/InventoryButton.vue'
import InventoryInput from './ui/InventoryInput.vue'
import InventoryDropdown from './ui/InventoryDropdown.vue'
import StatusBadge from './ui/StatusBadge.vue'
import ActionButton from './ui/ActionButton.vue'
import CleanBadge from './ui/CleanBadge.vue'

// Get common functionality
const { 
  darkModeClasses, 
  scalingStyles, 
  store, 
  statusOptions, 
  typeOptions,
  colorOptions,
  formatCurrency,
  confirmAction 
} = useCommonSnippets()
const { 
  products, 
  productGroups, 
  productCategories,
  addProduct, 
  updateProduct, 
  deleteProduct 
} = useProducts()

// State
const showProductForm = ref(false)
const editingProduct = ref(null)
const productFormData = ref({
  sku: '',
  name: '',
  description: '',
  group: '',
  type: 'items',
  unit: 'units',
  status: 'active'
})

// Options
const groupOptions = computed(() => 
  productGroups.value.map(group => ({ value: group.name, label: group.name }))
)

// typeOptions is now provided by useCommonSnippets

// Dark mode classes are now provided by useCommonSnippets

// Methods
const openProductForm = (product = null) => {
  editingProduct.value = product
  productFormData.value = product ? { ...product } : {
    sku: '',
    name: '',
    description: '',
    group: '',
    type: 'items',
    unit: 'units',
    status: 'active'
  }
  showProductForm.value = true
}

const saveProduct = () => {
  if (editingProduct.value) {
    updateProduct(editingProduct.value.id, productFormData.value)
  } else {
    addProduct(productFormData.value)
  }
  showProductForm.value = false
}

const handleDeleteProduct = (id) => {
  if (confirm('Are you sure you want to delete this product?')) {
    deleteProduct(id)
  }
}

const openGroupForm = (group = null) => {
  // Group form logic here
  console.log('Group form:', group)
}

const deleteGroup = (id) => {
  if (confirm('Are you sure you want to delete this group?')) {
    console.log('Delete group:', id)
  }
}

onMounted(() => {
  store.loadSettings()
})
</script>



<template>
  <div class="products" :class="darkModeClasses.container">
    <!-- Header -->
    <div class="mb-6">
      <h2 :style="scalingStyles.titleFontSize" :class="darkModeClasses.text" class="font-bold">
        Products
      </h2>
      <p :style="scalingStyles.subtitleFontSize" :class="darkModeClasses.textSecondary" class="mt-2">
        Manage product master data, groups, and categories
      </p>
    </div>


    <!-- Product Groups -->
    <div :class="[darkModeClasses.card, 'rounded-lg shadow-sm border mb-6']">
      <div class="p-4 border-b" :class="darkModeClasses.border">
        <div class="flex justify-between items-center">
          <h3 :style="scalingStyles.iconSizeSmall" :class="darkModeClasses.text" class="font-semibold">
            Product Groups
            <i class="fas fa-plus mr-2" :style="scalingStyles.iconSize"></i>
          </h3>
          <InventoryButton @click="openGroupForm()" variant="primary" :style="scalingStyles.iconSize ">
            <i class="fas fa-plus mr-2"></i>
            Add Group2
          </InventoryButton>
        </div>
      </div>
      <div class="p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div v-for="group in productGroups" :key="group.id" 
               :class="[darkModeClasses.card, 'rounded-lg p-4 border']">
            <div class="flex items-center justify-between mb-2">
              <h4 :style="scalingStyles.subtitleFontSize" :class="darkModeClasses.text" class="font-medium">{{ group.name }}</h4>
              <CleanBadge :variant="group.color" :text="group.color" size="xs" />
            </div>
            <p :style="scalingStyles.smallFontSize" :class="darkModeClasses.textSecondary" class="text-sm mb-3">{{ group.description }}</p>

            <!-- Product Groups section -->
            <div class="flex gap-1">
            <ActionButton variant="edit" :iconStyle="scalingStyles.iconSize" @click="openGroupForm(group)" />
            <ActionButton variant="delete" :iconStyle="scalingStyles.iconSize" @click="deleteGroup(group.id)" />
            </div>

          </div>
        </div>
      </div>
    </div>

    <!-- Products Table -->
    <div :class="[darkModeClasses.card, 'rounded-lg shadow-sm border']">
      <div class="p-4 border-b" :class="darkModeClasses.border">
        <div class="flex justify-between items-center">
          <h3 :style="scalingStyles.subtitleFontSize" :class="darkModeClasses.text" class="text-lg font-semibold">
            Products
          </h3>
          <InventoryButton @click="openProductForm()" variant="primary" :style="scalingStyles.button">
            <i class="fas fa-plus mr-2"></i>
            Add Product
          </InventoryButton>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead :class="darkModeClasses.tableHeader">
            <tr>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">SKU</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Name</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Group</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Type</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Status</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody :class="[darkModeClasses.table, 'divide-y', darkModeClasses.border]">
            <tr v-for="product in products" :key="product.id" :class="[darkModeClasses.tableRow, 'hover:bg-gray-50']">
              <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                {{ product.sku }}
              </td>
              <td :style="scalingStyles.textFontSize" class="px-4 py-4 whitespace-nowrap">
                <div :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="text-sm font-medium">{{ product.name }}</div>
                <div :style="scalingStyles.smallFontSize" :class="darkModeClasses.textSecondary" class="text-sm">{{ product.description }}</div>
              </td>
              <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                {{ product.group }}
              </td>
              <td :style="scalingStyles.textFontSize" class="px-4 py-4 whitespace-nowrap">
                <CleanBadge 
                  :variant="product.type === 'items' ? 'blue' : product.type === 'bulk' ? 'green' : 'orange'" 
                  :text="product.type" 
                  size="xs" 
                />
              </td>
              <td :style="scalingStyles.textFontSize" class="px-4 py-4 whitespace-nowrap">
                <CleanBadge 
                  :variant="product.status === 'active' ? 'green' : 'gray'" 
                  :text="product.status" 
                  size="xs" 
                />
              </td>
              <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm font-medium">


<!-- Products table section -->
<div class="flex gap-1">
  <ActionButton variant="edit" :iconStyle="scalingStyles.iconSize" @click="openProductForm(product)" />
  <ActionButton variant="delete" :iconStyle="scalingStyles.iconSize" @click="handleDeleteProduct(product.id)" />
</div>

                
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Product Form Modal -->
    <div v-if="showProductForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div :class="[darkModeClasses.modal, 'rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto']">
        <div class="flex justify-between items-center mb-4">
          <h3 :style="scalingStyles.subtitleFontSize" :class="darkModeClasses.text" class="text-lg font-semibold">
            {{ editingProduct ? 'Edit Product' : 'Add New Product' }}
          </h3>
          <button @click="showProductForm = false" :class="darkModeClasses.textSecondary" class="hover:opacity-75">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <form @submit.prevent="saveProduct" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <InventoryInput
              v-model="productFormData.sku"
              label="SKU"
              required
              :style="scalingStyles.input"
              :class="darkModeClasses.input"
            />
            <InventoryInput
              v-model="productFormData.name"
              label="Name"
              required
              :style="scalingStyles.input"
              :class="darkModeClasses.input"
            />
          </div>

          <InventoryInput
            v-model="productFormData.description"
            label="Description"
            type="textarea"
            :style="scalingStyles.textarea"
            :class="darkModeClasses.input"
          />

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <InventoryDropdown
              v-model="productFormData.group"
              label="Group"
              :options="groupOptions"
              required
              :style="scalingStyles.select"
              :class="darkModeClasses.input"
            />
            <InventoryDropdown
              v-model="productFormData.type"
              label="Type"
              :options="typeOptions"
              required
              :style="scalingStyles.select"
              :class="darkModeClasses.input"
            />
            <InventoryInput
              v-model="productFormData.unit"
              label="Unit"
              required
              :style="scalingStyles.input"
              :class="darkModeClasses.input"
            />
          </div>

          <div class="flex justify-end gap-2 pt-4">
            <InventoryButton @click="showProductForm = false" variant="secondary" :style="scalingStyles.button">
              Cancel
            </InventoryButton>
            <InventoryButton type="submit" variant="primary" :style="scalingStyles.button">
              {{ editingProduct ? 'Update' : 'Create' }}
            </InventoryButton>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

