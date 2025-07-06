<script setup>
import { ref, computed, watch } from 'vue';
import { XMarkIcon, MagnifyingGlassIcon, ChevronDownIcon, ChevronRightIcon } from '@heroicons/vue/24/outline';
import Badges from '@/components/colors/Badges.vue';

const props = defineProps({
  show: Boolean,
  role: Object,
  availablePermissions: Array,
  selectedPermissions: Array
});
const emit = defineEmits(['close', 'save', 'update:selectedPermissions']);

const searchQuery = ref('');
const expandedGroups = ref(new Set());

const filteredPermissions = computed(() => {
  if (!props.availablePermissions) return [];
  if (!searchQuery.value) return props.availablePermissions;
  return props.availablePermissions.filter(p =>
    p.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    (p.description && p.description.toLowerCase().includes(searchQuery.value.toLowerCase()))
  );
});

const groupedPermissions = computed(() => {
  const groups = {};
  filteredPermissions.value.forEach(p => {
    const group = p.group || 'Other';
    if (!groups[group]) groups[group] = [];
    groups[group].push(p);
  });
  return groups;
});

const isGroupExpanded = group => expandedGroups.value.has(group);
const toggleGroup = group => {
  if (expandedGroups.value.has(group)) expandedGroups.value.delete(group);
  else expandedGroups.value.add(group);
};

const isPermissionSelected = id => props.selectedPermissions?.includes(id);
const togglePermission = id => {
  const selected = props.selectedPermissions ? [...props.selectedPermissions] : [];
  const idx = selected.indexOf(id);
  if (idx > -1) selected.splice(idx, 1);
  else selected.push(id);
  emit('update:selectedPermissions', selected);
};

const selectAll = () => {
  const allIds = props.availablePermissions.map(p => p.id);
  emit('update:selectedPermissions', allIds);
};
const deselectAll = () => emit('update:selectedPermissions', []);

const closeModal = () => emit('close');
const savePermissions = () => emit('save', props.selectedPermissions);
</script>

<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">
      <!-- Header -->
      <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
        <div>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
            Advanced Permissions Manager
          </h3>
          <p class="text-sm text-gray-500 dark:text-gray-400">
            {{ role?.name ? `Managing permissions for ${role.name} role` : 'Select permissions' }}
          </p>
        </div>
        <button @click="closeModal" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
          <XMarkIcon class="h-5 w-5" />
        </button>
      </div>

      <!-- Search and Actions -->
      <div class="flex flex-col sm:flex-row gap-2 p-4">
        <div class="relative flex-1">
          <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search permissions..."
            class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
          />
        </div>
        <button @click="selectAll" class="px-3 py-1 text-xs font-medium text-indigo-600 hover:text-indigo-800">Select All</button>
        <button @click="deselectAll" class="px-3 py-1 text-xs font-medium text-gray-600 hover:text-gray-800">Deselect All</button>
      </div>

      <!-- Permissions List -->
      <div class="p-4 space-y-4">
        <div v-for="(permissions, group) in groupedPermissions" :key="group" class="border rounded-lg">
          <!-- Group Header -->
          <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700/50 cursor-pointer" @click="toggleGroup(group)">
            <div class="flex items-center space-x-2">
              <ChevronDownIcon v-if="isGroupExpanded(group)" class="h-4 w-4 text-gray-500" />
              <ChevronRightIcon v-else class="h-4 w-4 text-gray-500" />
              <span class="font-medium text-gray-900 dark:text-gray-100">{{ group }}</span>
            </div>
            <span class="text-xs text-gray-500 dark:text-gray-400">{{ permissions.length }} permissions</span>
          </div>
          <div v-show="isGroupExpanded(group)" class="divide-y divide-gray-100 dark:divide-gray-700">
            <div v-for="permission in permissions" :key="permission.id" class="flex items-center p-2">
              <input
                type="checkbox"
                :id="'permission-' + permission.id"
                :checked="isPermissionSelected(permission.id)"
                @change="togglePermission(permission.id)"
                class="h-4 w-4 text-indigo-600 border-gray-300 rounded"
              >
              <label :for="'permission-' + permission.id" class="ml-2 flex flex-col flex-grow cursor-pointer">
                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ permission.name }}</span>
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ permission.description }}</span>
              </label>
              <Badges :status="permission.group" size="xxs" variant="badge" />
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="flex justify-end gap-2 p-4 border-t border-gray-200 dark:border-gray-700">
        <button @click="closeModal" class="px-4 py-2 rounded bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600">Cancel</button>
        <button @click="savePermissions" class="px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700">Save</button>
      </div>
    </div>
  </div>
</template> 