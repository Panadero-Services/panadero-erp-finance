<!--
  Vendors Component
  @version 1.0.14
  @date 17-Sep-2025
  @description Manage vendors, customers, and forwarders with metrics and general info
-->
<script setup>
import { ref, computed, onMounted } from 'vue'
import { useCommonSnippets } from '../composables/useCommonSnippets.js'

// UI Components
import InventoryButton from './ui/InventoryButton.vue'
import InventoryInput from './ui/InventoryInput.vue'
import InventoryDropdown from './ui/InventoryDropdown.vue'
import StatusBadge from './ui/StatusBadge.vue'
import InventoryValueCard from './ui/InventoryValueCard.vue'
import ActionButton from './ui/ActionButton.vue'
import CleanBadge from './ui/CleanBadge.vue'

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

// State
const selectedPartnerType = ref('suppliers') // Filter: suppliers, customers, forwarders
const searchTerm = ref('')
const selectedStatus = ref('all')
const showPartnerForm = ref(false)
const editingPartner = ref(null)
const partnerFormData = ref({
  name: '',
  type: 'supplier',
  contact_person: '',
  email: '',
  phone: '',
  address: '',
  status: 'active',
  notes: ''
})

// Mock data for partners
const partners = ref([
  // Suppliers
  {
    id: 1,
    name: 'Organic Valley',
    type: 'supplier',
    contact_person: 'John Smith',
    email: 'john@organicvalley.com',
    phone: '+1-555-0123',
    address: '123 Farm Road, Organic Valley, CA 90210',
    status: 'active',
    notes: 'Premium organic flour supplier',
    total_orders: 45,
    total_value: 12500.50,
    last_order: '2025-09-15',
    rating: 4.8,
    payment_terms: 'Net 30',
    created_at: '2025-01-15'
  },
  {
    id: 2,
    name: 'KitchenPro Equipment',
    type: 'supplier',
    contact_person: 'Sarah Johnson',
    email: 'sarah@kitchenpro.com',
    phone: '+1-555-0456',
    address: '456 Industrial Blvd, Equipment City, TX 75001',
    status: 'active',
    notes: 'Professional kitchen equipment supplier',
    total_orders: 23,
    total_value: 8750.25,
    last_order: '2025-09-10',
    rating: 4.6,
    payment_terms: 'Net 15',
    created_at: '2025-02-20'
  },
  // Customers
  {
    id: 3,
    name: 'Bakery Chain A',
    type: 'customer',
    contact_person: 'Mike Wilson',
    email: 'mike@bakerychain.com',
    phone: '+1-555-0789',
    address: '789 Main Street, Bakery City, NY 10001',
    status: 'active',
    notes: 'Large bakery chain with 15 locations',
    total_orders: 67,
    total_value: 18750.75,
    last_order: '2025-09-17',
    rating: 4.9,
    payment_terms: 'Net 30',
    created_at: '2025-01-10'
  },
  {
    id: 4,
    name: 'Restaurant Group B',
    type: 'customer',
    contact_person: 'Lisa Brown',
    email: 'lisa@restaurantgroup.com',
    phone: '+1-555-0321',
    address: '321 Restaurant Row, Food City, FL 33101',
    status: 'active',
    notes: 'Upscale restaurant group',
    total_orders: 34,
    total_value: 9250.00,
    last_order: '2025-09-12',
    rating: 4.7,
    payment_terms: 'Net 15',
    created_at: '2025-03-05'
  },
  // Forwarders
  {
    id: 5,
    name: 'Fast Logistics Inc',
    type: 'forwarder',
    contact_person: 'David Lee',
    email: 'david@fastlogistics.com',
    phone: '+1-555-0654',
    address: '654 Logistics Lane, Transport City, IL 60601',
    status: 'active',
    notes: 'Reliable shipping and logistics partner',
    total_orders: 89,
    total_value: 0,
    last_order: '2025-09-16',
    rating: 4.5,
    payment_terms: 'Net 30',
    created_at: '2025-01-25'
  },
  {
    id: 6,
    name: 'Cold Chain Transport',
    type: 'forwarder',
    contact_person: 'Maria Garcia',
    email: 'maria@coldchain.com',
    phone: '+1-555-0987',
    address: '987 Refrigerated Road, Cold City, WA 98101',
    status: 'active',
    notes: 'Specialized in temperature-controlled shipping',
    total_orders: 45,
    total_value: 0,
    last_order: '2025-09-14',
    rating: 4.8,
    payment_terms: 'Net 15',
    created_at: '2025-02-10'
  }
])

const partnerTypeOptions = [
  { value: 'suppliers', label: 'Suppliers' },
  { value: 'customers', label: 'Customers' },
  { value: 'forwarders', label: 'Forwarders' }
]

const partnerStatusOptions = [
  { value: 'all', label: 'All Status' },
  { value: 'active', label: 'Active' },
  { value: 'inactive', label: 'Inactive' },
  { value: 'suspended', label: 'Suspended' }
]

// Computed properties
const currentPartners = computed(() => {
  return partners.value.filter(partner => partner.type === selectedPartnerType.value.slice(0, -1)) // Remove 's' from type
})

const filteredPartners = computed(() => {
  let filtered = [...currentPartners.value]
  
  if (searchTerm.value) {
    filtered = filtered.filter(partner => 
      partner.name.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
      partner.contact_person.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
      partner.email.toLowerCase().includes(searchTerm.value.toLowerCase())
    )
  }
  
  if (selectedStatus.value !== 'all') {
    filtered = filtered.filter(partner => partner.status === selectedStatus.value)
  }
  
  return filtered
})

const partnersByStatus = computed(() => {
  const statuses = ['active', 'inactive', 'suspended']
  return statuses.reduce((acc, status) => {
    acc[status] = currentPartners.value.filter(partner => partner.status === status).length
    return acc
  }, {})
})

const totalValue = computed(() => {
  return currentPartners.value.reduce((total, partner) => total + partner.total_value, 0)
})

const averageRating = computed(() => {
  const ratings = currentPartners.value.map(partner => partner.rating)
  return ratings.length > 0 ? (ratings.reduce((sum, rating) => sum + rating, 0) / ratings.length).toFixed(1) : 0
})

const getStatusColor = (status) => {
  switch (status) {
    case 'active': return 'green'
    case 'inactive': return 'gray'
    case 'suspended': return 'red'
    default: return 'gray'
  }
}

const getPartnerTypeIcon = (type) => {
  switch (type) {
    case 'supplier': return 'fas fa-truck-loading'
    case 'customer': return 'fas fa-users'
    case 'forwarder': return 'fas fa-shipping-fast'
    default: return 'fas fa-building'
  }
}

const getPartnerTypeColor = (type) => {
  switch (type) {
    case 'supplier': return 'blue'
    case 'customer': return 'green'
    case 'forwarder': return 'orange'
    default: return 'gray'
  }
}

// Methods
const openPartnerForm = (partner = null) => {
  editingPartner.value = partner
  partnerFormData.value = partner ? { ...partner } : {
    name: '',
    type: selectedPartnerType.value.slice(0, -1), // Remove 's' from type
    contact_person: '',
    email: '',
    phone: '',
    address: '',
    status: 'active',
    notes: ''
  }
  showPartnerForm.value = true
}

const savePartner = () => {
  if (editingPartner.value) {
    // Update existing partner
    const index = partners.value.findIndex(p => p.id === editingPartner.value.id)
    if (index !== -1) {
      partners.value[index] = { ...partnerFormData.value, id: editingPartner.value.id }
    }
  } else {
    // Add new partner
    const newPartner = {
      ...partnerFormData.value,
      id: Date.now(),
      total_orders: 0,
      total_value: 0,
      last_order: null,
      rating: 5.0,
      payment_terms: 'Net 30',
      created_at: new Date().toISOString().split('T')[0]
    }
    partners.value.push(newPartner)
  }
  showPartnerForm.value = false
}

const handleDeletePartner = (id) => {
  if (confirmAction('Are you sure you want to delete this partner?')) {
    const index = partners.value.findIndex(p => p.id === id)
    if (index !== -1) {
      partners.value.splice(index, 1)
    }
  }
}

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
        Manage suppliers, customers, and forwarders with metrics and general information
      </p>
    </div>

    <!-- Partner Type Filter -->
    <div :class="[darkModeClasses.card, 'rounded-lg shadow-sm border mb-6']">
      <div class="p-4">
        <div class="flex items-center gap-4">
          <label :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="font-medium">
            Partner Type:
          </label>
          <InventoryDropdown
            v-model="selectedPartnerType"
            :options="partnerTypeOptions"
            :style="scalingStyles.select"
            :class="darkModeClasses.input"
            class="w-48"
          />
          <div class="flex items-center gap-2">
            <i :class="[getPartnerTypeIcon(selectedPartnerType.slice(0, -1)), darkModeClasses.text]" :style="scalingStyles.iconSize"></i>
            <span :style="scalingStyles.textFontSize" :class="darkModeClasses.text">
              {{ partnerTypeOptions.find(opt => opt.value === selectedPartnerType)?.label }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <InventoryValueCard
        title="Total Partners"
        :value="currentPartners.length"
        subtitle="All Types"
        icon="fas fa-building"
        color="blue"
        :scaling-styles="scalingStyles"
        :dark-mode="store.settings.darkMode"
      />
      <InventoryValueCard
        title="Active"
        :value="partnersByStatus.active"
        subtitle="Currently Active"
        icon="fas fa-check-circle"
        color="green"
        :scaling-styles="scalingStyles"
        :dark-mode="store.settings.darkMode"
      />
      <InventoryValueCard
        title="Total Value"
        :value="formatCurrency(totalValue)"
        subtitle="All Transactions"
        icon="fas fa-dollar-sign"
        color="purple"
        :scaling-styles="scalingStyles"
        :dark-mode="store.settings.darkMode"
      />
      <InventoryValueCard
        title="Avg Rating"
        :value="averageRating"
        subtitle="Partner Rating"
        icon="fas fa-star"
        color="orange"
        :scaling-styles="scalingStyles"
        :dark-mode="store.settings.darkMode"
      />
    </div>

    <!-- Filters -->
    <div :class="[darkModeClasses.card, 'p-4 rounded-lg shadow-sm border mb-6']">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <InventoryInput
          v-model="searchTerm"
          placeholder="Search partners..."
          :style="scalingStyles.input"
          :class="darkModeClasses.input"
        >
          <template #prefix>
            <i class="fas fa-search"></i>
          </template>
        </InventoryInput>

        <InventoryDropdown
          v-model="selectedStatus"
          :options="partnerStatusOptions"
          :style="scalingStyles.select"
          :class="darkModeClasses.input"
        />

        <InventoryButton
          @click="openPartnerForm()"
          variant="primary"
          :style="scalingStyles.button"
        >
          <i class="fas fa-plus mr-2"></i>
          New Partner
        </InventoryButton>

        <InventoryButton
          @click="openPartnerForm"
          variant="secondary"
          :style="scalingStyles.button"
        >
          <i class="fas fa-chart-bar mr-2"></i>
          View Reports
        </InventoryButton>
      </div>
    </div>

    <!-- Partners Table -->
    <div :class="[darkModeClasses.card, 'rounded-lg shadow-sm border overflow-hidden']">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead :class="darkModeClasses.tableHeader">
            <tr>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Name</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Contact</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Orders</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Total Value</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Rating</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Status</th>
              <th :style="scalingStyles.tableHeader" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody :class="[darkModeClasses.table, 'divide-y', darkModeClasses.border]">
            <tr v-for="partner in filteredPartners" :key="partner.id" :class="[darkModeClasses.tableRow, 'hover:bg-gray-50']">
              <td :style="scalingStyles.textFontSize" class="px-4 py-4 whitespace-nowrap">
                <div :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="text-sm font-medium">{{ partner.name }}</div>
                <div :style="scalingStyles.smallFontSize" :class="darkModeClasses.textSecondary" class="text-sm">{{ partner.address }}</div>
              </td>
              <td :style="scalingStyles.textFontSize" class="px-4 py-4 whitespace-nowrap">
                <div :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="text-sm">{{ partner.contact_person }}</div>
                <div :style="scalingStyles.smallFontSize" :class="darkModeClasses.textSecondary" class="text-sm">{{ partner.email }}</div>
                <div :style="scalingStyles.smallFontSize" :class="darkModeClasses.textSecondary" class="text-sm">{{ partner.phone }}</div>
              </td>
              <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                {{ partner.total_orders }}
                <div :style="scalingStyles.smallFontSize" :class="darkModeClasses.textSecondary" class="text-xs">
                  Last: {{ formatDate(partner.last_order) }}
                </div>
              </td>
              <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                {{ formatCurrency(partner.total_value) }}
                <div :style="scalingStyles.smallFontSize" :class="darkModeClasses.textSecondary" class="text-xs">
                  {{ partner.payment_terms }}
                </div>
              </td>
              <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm">
                <div class="flex items-center gap-1">
                  <i class="fas fa-star text-yellow-500"></i>
                  <span>{{ partner.rating }}</span>
                </div>
              </td>
              <td :style="scalingStyles.textFontSize" class="px-4 py-4 whitespace-nowrap">
                <CleanBadge 
                  :variant="getStatusColor(partner.status)" 
                  :text="partner.status.charAt(0).toUpperCase() + partner.status.slice(1)" 
                  size="xs" 
                />
              </td>
              <td :style="scalingStyles.textFontSize" :class="darkModeClasses.text" class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex gap-1">
                  <ActionButton variant="edit" :iconStyle="scalingStyles.iconSize" @click="openPartnerForm(partner)" />
                  <ActionButton variant="delete" :iconStyle="scalingStyles.iconSize" @click="handleDeletePartner(partner.id)" />
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Partner Form Modal -->
    <div v-if="showPartnerForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div :class="[darkModeClasses.modal, 'rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto']">
        <div class="flex justify-between items-center mb-4">
          <h3 :style="scalingStyles.subtitleFontSize" :class="darkModeClasses.text" class="text-lg font-semibold">
            {{ editingPartner ? 'Edit Partner' : 'Add New Partner' }}
          </h3>
          <button @click="showPartnerForm = false" :class="darkModeClasses.textSecondary" class="hover:opacity-75">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <form @submit.prevent="savePartner" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <InventoryInput
              v-model="partnerFormData.name"
              label="Partner Name"
              required
              :style="scalingStyles.input"
              :class="darkModeClasses.input"
            />
            <InventoryInput
              v-model="partnerFormData.contact_person"
              label="Contact Person"
              required
              :style="scalingStyles.input"
              :class="darkModeClasses.input"
            />
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <InventoryInput
              v-model="partnerFormData.email"
              label="Email"
              type="email"
              required
              :style="scalingStyles.input"
              :class="darkModeClasses.input"
            />
            <InventoryInput
              v-model="partnerFormData.phone"
              label="Phone"
              required
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

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <InventoryDropdown
              v-model="partnerFormData.status"
              label="Status"
              :options="partnerStatusOptions.filter(opt => opt.value !== 'all')"
              required
              :style="scalingStyles.select"
              :class="darkModeClasses.input"
            />
            <InventoryInput
              v-model="partnerFormData.notes"
              label="Notes"
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