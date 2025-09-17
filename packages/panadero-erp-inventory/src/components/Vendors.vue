<!--
  Vendors Component
  @version 1.0.13
  @date 17-Sep-2025
  @description Manage vendors, customers, and forwarders
-->
<script setup>
import { ref, computed, onMounted } from 'vue'
import { useVendors } from '../composables/useVendors.js'
import { useCommonSnippets } from '../composables/useCommonSnippets.js'

// UI Components
import InventoryButton from './ui/InventoryButton.vue'
import InventoryInput from './ui/InventoryInput.vue'
import InventoryDropdown from './ui/InventoryDropdown.vue'
import StatusBadge from './ui/StatusBadge.vue'
import InventoryValueCard from './ui/InventoryValueCard.vue'
import ActionButton from './ui/ActionButton.vue'

// Get common functionality
const { 
  darkModeClasses, 
  scalingStyles, 
  store, 
  statusOptions, 
  typeOptions,
  formatCurrency,
  formatDate,
  confirmAction 
} = useCommonSnippets()
const { 
  vendors,
  customers,
  forwarders,
  getAllPartners,
  getPartnersByType,
  getActivePartners,
  getPartnerMetrics,
  addPartner,
  updatePartner,
  deletePartner 
} = useVendors()

// State
const activeTab = ref('vendors')
const showPartnerForm = ref(false)
const editingPartner = ref(null)
const partnerFormData = ref({})

const partnerTabs = [
  { id: 'vendors', label: 'Vendors', icon: 'fas fa-truck', component: 'Vendors' },
  { id: 'customers', label: 'Customers', icon: 'fas fa-users', component: 'Customers' },
  { id: 'forwarders', label: 'Forwarders', icon: 'fas fa-shipping-fast', component: 'Forwarders' }
]

// Computed properties
const partnerMetrics = computed(() => getPartnerMetrics.value)

// Dark mode classes are now provided by useCommonSnippets

// Methods
const openPartnerForm = (type, partner = null) => {
  editingPartner.value = partner
  partnerFormData.value = partner ? { ...partner } : {
    name: '',
    type: type,
    contactPerson: '',
    email: '',
    phone: '',
    address: '',
    paymentTerms: '',
    creditLimit: 0,
    rating: 0,
    status: 'active'
  }
  showPartnerForm.value = true
}

const savePartner = () => {
  if (editingPartner.value) {
    updatePartner(editingPartner.value.id, partnerFormData.value)
  } else {
    addPartner(partnerFormData.value)
  }
  showPartnerForm.value = false
}

const handleDeletePartner = (id) => {
  if (confirm('Are you sure you want to delete this partner?')) {
    deletePartner(id)
  }
}

// formatCurrency is now provided by useCommonSnippets

onMounted(() => {
  store.loadSettings()
})
</script>


<template>
  <div class="vendors" :class="darkModeClasses.container">
    <!-- Header -->
    <div class="mb-6">
      <h2 :style="scalingStyles.titleFontSize" :class="darkModeClasses.text" class="text-2xl font-bold">
        Vendors & Partners
      </h2>
      <p :style="scalingStyles.subtitleFontSize" :class="darkModeClasses.textSecondary" class="mt-2">
        Manage vendors, customers, and forwarders
      </p>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <InventoryValueCard
        title="Vendors"
        :value="partnerMetrics.activeVendors"
        subtitle="Active Suppliers"
        icon="fas fa-truck"
        color="blue"
        :scaling-styles="scalingStyles"
        :dark-mode="store.settings.darkMode"
      />
      <InventoryValueCard
        title="Customers"
        :value="partnerMetrics.activeCustomers"
        subtitle="Active Buyers"
        icon="fas fa-users"
        color="green"
        :scaling-styles="scalingStyles"
        :dark-mode="store.settings.darkMode"
      />
      <InventoryValueCard
        title="Forwarders"
        :value="partnerMetrics.activeForwarders"
        subtitle="Logistics Partners"
        icon="fas fa-shipping-fast"
        color="orange"
        :scaling-styles="scalingStyles"
        :dark-mode="store.settings.darkMode"
      />
      <InventoryValueCard
        title="Total Partners"
        :value="partnerMetrics.activePartners"
        subtitle="All Active"
        icon="fas fa-handshake"
        color="purple"
        :scaling-styles="scalingStyles"
        :dark-mode="store.settings.darkMode"
      />
    </div>

    <!-- Partner Type Tabs -->
    <div :class="[darkModeClasses.card, 'rounded-lg shadow-sm border mb-6']">
      <div class="border-b" :class="darkModeClasses.border">
        <nav class="flex space-x-8 px-6">
          <button
            v-for="tab in partnerTabs"
            :key="tab.id"
            @click="activeTab = tab.id"
            :class="[
              activeTab === tab.id
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
              'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
            ]"
          >
            <i :class="tab.icon" class="mr-2"></i>
            {{ tab.label }}
          </button>
        </nav>
      </div>

      <div class="p-6">
        <!-- Vendors Tab -->
        <div v-if="activeTab === 'vendors'">
          <div class="flex justify-between items-center mb-4">
            <h3 :style="scalingStyles.subtitleFontSize" :class="darkModeClasses.text" class="text-lg font-semibold">
              Vendors
            </h3>
            <InventoryButton @click="openPartnerForm('vendor')" variant="primary" :style="scalingStyles.button">
              <i class="fas fa-plus mr-2"></i>
              Add Vendor
            </InventoryButton>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead :class="darkModeClasses.tableHeader">
                <tr>
                  <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Name</th>
                  <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Contact</th>
                  <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Rating</th>
                  <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Orders</th>
                  <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Value</th>
                  <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody :class="[darkModeClasses.table, 'divide-y', darkModeClasses.border]">
                <tr v-for="vendor in vendors" :key="vendor.id" :class="[darkModeClasses.tableRow, 'hover:bg-gray-50']">
                  <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                    {{ vendor.name }}
                  </td>
                  <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                    <div>{{ vendor.contactPerson }}</div>
                    <div :style="scalingStyles.textFontSizeSubtext" :class="darkModeClasses.textSecondary" class="text-xs">{{ vendor.email }}</div>
                  </td>
                  <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                    <div class="flex items-center">
                      <span class="mr-1">{{ vendor.rating }}</span>
                      <i class="fas fa-star text-yellow-400"></i>
                    </div>
                  </td>
                  <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                    {{ vendor.totalOrders }}
                  </td>
                  <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                    {{ formatCurrency(vendor.totalValue) }}
                  </td>
                  <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex gap-1">
                      <ActionButton  variant="edit" @click="openPartnerForm('vendor', vendor)" />
                      <ActionButton  variant="delete" @click="handleDeletePartner(vendor.id)" />
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Customers Tab -->
        <div v-if="activeTab === 'customers'">
          <div class="flex justify-between items-center mb-4">
            <h3 :style="scalingStyles.subtitleFontSize" :class="darkModeClasses.text" class="text-lg font-semibold">
              Customers
            </h3>
            <InventoryButton @click="openPartnerForm('customer')" variant="primary" :style="scalingStyles.button">
              <i class="fas fa-plus mr-2"></i>
              Add Customer
            </InventoryButton>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead :class="darkModeClasses.tableHeader">
                <tr>
                  <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Name</th>
                  <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Contact</th>
                  <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Credit Limit</th>
                  <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Orders</th>
                  <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Value</th>
                  <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody :class="[darkModeClasses.table, 'divide-y', darkModeClasses.border]">
                <tr v-for="customer in customers" :key="customer.id" :class="[darkModeClasses.tableRow, 'hover:bg-gray-50']">
                  <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                    {{ customer.name }}
                  </td>
                  <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                    <div>{{ customer.contactPerson }}</div>
                    <div :style="scalingStyles.textFontSizeSubtext" :class="darkModeClasses.textSecondary" class="text-xs">{{ customer.email }}</div>
                  </td>
                  <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                    {{ formatCurrency(customer.creditLimit) }}
                  </td>
                  <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                    {{ customer.totalOrders }}
                  </td>
                  <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                    {{ formatCurrency(customer.totalValue) }}
                  </td>
                  <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex gap-1">
                      <ActionButton  variant="edit" @click="openPartnerForm('customer', customer)" />
                      <ActionButton  variant="delete" @click="handleDeletePartner(customer.id)" />
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Forwarders Tab -->
        <div v-if="activeTab === 'forwarders'">
          <div class="flex justify-between items-center mb-4">
            <h3 :style="scalingStyles.subtitleFontSize" :class="darkModeClasses.text" class="text-lg font-semibold">
              Forwarders
            </h3>
            <InventoryButton @click="openPartnerForm('forwarder')" variant="primary" :style="scalingStyles.button">
              <i class="fas fa-plus mr-2"></i>
              Add Forwarder
            </InventoryButton>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead :class="darkModeClasses.tableHeader">
                <tr>
                  <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Name</th>
                  <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Contact</th>
                  <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Services</th>
                  <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">On-Time %</th>
                  <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Shipments</th>
                  <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody :class="[darkModeClasses.table, 'divide-y', darkModeClasses.border]">
                <tr v-for="forwarder in forwarders" :key="forwarder.id" :class="[darkModeClasses.tableRow, 'hover:bg-gray-50']">
                  <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                    {{ forwarder.name }}
                  </td>
                  <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                    <div>{{ forwarder.contactPerson }}</div>
                    <div :style="scalingStyles.textFontSizeSubtext" :class="darkModeClasses.textSecondary" class="text-xs">{{ forwarder.email }}</div>
                  </td>
                  <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                    {{ forwarder.services.join(', ') }}
                  </td>
                  <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                    {{ forwarder.onTimeDelivery }}%
                  </td>
                  <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                    {{ forwarder.totalShipments }}
                  </td>
                  <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex gap-1">
                      <ActionButton  variant="edit" @click="openPartnerForm('forwarder', forwarder)" />
                      <ActionButton  variant="delete" @click="handleDeletePartner(forwarder.id)" />
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Partner Form Modal -->
    <div v-if="showPartnerForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div :class="[darkModeClasses.modal, 'rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto']">
        <div class="flex justify-between items-center mb-4">
          <h3 :style="scalingStyles.subtitleFontSize" :class="darkModeClasses.text" class="text-lg font-semibold">
            {{ editingPartner ? 'Edit' : 'Add' }} {{ partnerFormData.type?.charAt(0).toUpperCase() + partnerFormData.type?.slice(1) }}
          </h3>
          <button @click="showPartnerForm = false" :class="darkModeClasses.textSecondary" class="hover:opacity-75">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <form @submit.prevent="savePartner" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <InventoryInput
              v-model="partnerFormData.name"
              label="Name"
              required
              :style="scalingStyles.input"
              :class="darkModeClasses.input"
            />
            <InventoryInput
              v-model="partnerFormData.contactPerson"
              label="Contact Person"
              :style="scalingStyles.input"
              :class="darkModeClasses.input"
            />
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <InventoryInput
              v-model="partnerFormData.email"
              label="Email"
              type="email"
              :style="scalingStyles.input"
              :class="darkModeClasses.input"
            />
            <InventoryInput
              v-model="partnerFormData.phone"
              label="Phone"
              :style="scalingStyles.input"
              :class="darkModeClasses.input"
            />
          </div>

          <InventoryInput
            v-model="partnerFormData.address"
            label="Address"
            type="textarea"
            :style="scalingStyles.textarea"
            :class="darkModeClasses.input"
          />

          <div v-if="partnerFormData.type === 'vendor'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <InventoryInput
              v-model="partnerFormData.paymentTerms"
              label="Payment Terms"
              :style="scalingStyles.input"
              :class="darkModeClasses.input"
            />
            <InventoryInput
              v-model.number="partnerFormData.rating"
              label="Rating"
              type="number"
              min="0"
              max="5"
              step="0.1"
              :style="scalingStyles.input"
              :class="darkModeClasses.input"
            />
          </div>

          <div v-if="partnerFormData.type === 'customer'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <InventoryInput
              v-model.number="partnerFormData.creditLimit"
              label="Credit Limit"
              type="number"
              :style="scalingStyles.input"
              :class="darkModeClasses.input"
            />
            <InventoryInput
              v-model.number="partnerFormData.rating"
              label="Rating"
              type="number"
              min="0"
              max="5"
              step="0.1"
              :style="scalingStyles.input"
              :class="darkModeClasses.input"
            />
          </div>

          <div v-if="partnerFormData.type === 'forwarder'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <InventoryInput
              v-model="partnerFormData.services"
              label="Services (comma separated)"
              :style="scalingStyles.input"
              :class="darkModeClasses.input"
            />
            <InventoryInput
              v-model.number="partnerFormData.onTimeDelivery"
              label="On-Time Delivery %"
              type="number"
              min="0"
              max="100"
              :style="scalingStyles.input"
              :class="darkModeClasses.input"
            />
          </div>

          <div class="flex justify-end gap-2 pt-4">
            <InventoryButton @click="showPartnerForm = false" variant="secondary" :style="scalingStyles.button">
              Cancel
            </InventoryButton>
            <InventoryButton type="submit" variant="primary" :style="scalingStyles.button">
              {{ editingPartner ? 'Update' : 'Create' }}
            </InventoryButton>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

