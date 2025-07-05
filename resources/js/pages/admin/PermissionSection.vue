<script setup>
import { ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import SearchWithPopup from '@/components/inputs/SearchWithPopup.vue';
import Modal from '@/components/Modal.vue';
import Badges from '@/components/colors/Badges.vue';

const roles = ref([]);
const availablePermissions = ref([]);
const searchQuery = ref('');
const showEditModal = ref(false);
const selectedRole = ref(null);
const selectedPermissions = ref([]);

// Permission hierarchy for sorting - include ALL groups from the database
const permissionGroups = {
  'global': 0,  // Add this - it was missing!
  'users': 1,
  'roles': 2,
  'articles': 3,
  'settings': 4
};

// Status mapping for role badges
const roleStatusMapping = {
  'master': 'purple',
  'admin': 'blue', 
  'developer': 'green',
  'editor': 'yellow',
  'member': 'gray'
};

const permissionDescriptions = {
  // User permissions
  'viewUsers': 'Can view user list',
  'createUsers': 'Can create new users',
  'editUsers': 'Can edit user details',
  'deleteUsers': 'Can delete users',
  // Role permissions
  'viewRoles': 'Can view roles',
  'createRoles': 'Can create new roles',
  'editRoles': 'Can edit roles',
  'deleteRoles': 'Can delete roles',
  // Article permissions
  'viewArticles': 'Can view articles',
  'createArticles': 'Can create articles',
  'editArticles': 'Can edit articles',
  'deleteArticles': 'Can delete articles',
  // Post permissions
  'viewPosts': 'Can view posts',
  'createPosts': 'Can create posts',
  'editPosts': 'Can edit posts',
  'deletePosts': 'Can delete posts',
  // Project permissions
  'viewProjects': 'Can view projects',
  'createProjects': 'Can create projects',
  'editProjects': 'Can edit projects',
  'deleteProjects': 'Can delete projects',
  // Future permissions
  'viewFutures': 'Can view futures',
  'createFutures': 'Can create futures',
  'editFutures': 'Can edit futures',
  'deleteFutures': 'Can delete futures',
  // Settings permissions
  'manageSettings': 'Can manage application settings',
  // Global permissions
  'globalView': 'Can view global records',
  'globalCreate': 'Can create global records',
  'globalEdit': 'Can edit global records',
  'globalDelete': 'Can delete global records',
  'canReadAll': 'Full access to read all records',
  'canReadUnlocked': 'Access to read all unlocked records',
  'canReadProject': 'Access to read all project records',
  'canReadByStatus': 'Access to read specific status records',
  'canReadOwn': 'Access to read own records only'
};

// Status mapping for permission badges
const permissionStatusMapping = {
  'view': 'blue',
  'create': 'green',
  'edit': 'yellow',
  'delete': 'red',
  'manage': 'purple',
  'global': 'purple',
  'canRead': 'blue'
};

const getPermissionColor = (permission) => {
  const action = permission.split(/(?=[A-Z])/)[0];
  return permissionStatusMapping[action] || 'gray';
};

const sortPermissions = (permissions) => {
  return [...permissions].sort((a, b) => {
    const groupA = a.group || 'other';
    const groupB = b.group || 'other';
    return (permissionGroups[groupA] || 99) - (permissionGroups[groupB] || 99);
  });
};

const getPermissionDescription = (permissionName) => {
  return permissionDescriptions[permissionName] || 'Standard permission';
};

const capitalizeFirst = (str) => {
  return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
};

onMounted(async () => {
  try {
    await loadRoles();
    await loadPermissions();
  } catch (error) {
    console.error('Setup error:', error);
  }
});

const loadRoles = async () => {
  try {
    const response = await axios.get('/api/user-permissions/roles');
    roles.value = response.data;
  } catch (error) {
    console.error('Error loading roles:', error);
  }
};

const loadPermissions = async () => {
  try {
    const response = await axios.get('/api/permissions');
    availablePermissions.value = response.data;
  } catch (error) {
    console.error('Error loading permissions:', error);
  }
};

const searchRoles = async () => {
  if (searchQuery.value.length === 0) {
    await loadRoles();
    return;
  }
  
  try {
    const response = await axios.get(`/api/user-permissions/roles/search?q=${encodeURIComponent(searchQuery.value)}`);
    roles.value = response.data;
  } catch (error) {
    console.error('Error searching roles:', error);
  }
};

const editRole = (role) => {
  selectedRole.value = role;
  selectedPermissions.value = role.permissions.map(permission => permission.id);
  showEditModal.value = true;
};

const closeEditModal = () => {
  showEditModal.value = false;
  selectedRole.value = null;
  selectedPermissions.value = [];
};

const saveRolePermissions = async () => {
  try {
    await axios.put(`/api/user-permissions/roles/${selectedRole.value.id}/permissions`, {
      permissions: selectedPermissions.value
    });
    await loadRoles();
    closeEditModal();
  } catch (error) {
    console.error('Error saving role permissions:', error);
  }
};
</script>

<style scoped>
.role-badge {
  @apply inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium transition-colors duration-150;
}

.table-row-hover {
  @apply transition-colors duration-150;
}
</style>

<template>
  <AppLayout title="Role Permissions">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Role Permissions Management
      </h2>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-4">
          <!-- Search Roles -->
          <div class="mb-4">
            <SearchWithPopup
              v-model="searchQuery"
              placeholder="Search roles..."
              @search="searchRoles"
              class="w-full max-w-md"
            />
          </div>

          <!-- Roles Table -->
          <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
              <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                  <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-1/5">
                    Role
                  </th>
                  <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-3/5">
                    Permissions
                  </th>
                  <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-1/5">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <tr v-for="role in roles" :key="role.id" class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                  <!-- Role Name Column -->
                  <td class="py-2 px-2 text-sm text-gray-900 dark:text-gray-100 whitespace-nowrap">
                    <Badges
                      :status="role.name"
                      :statusMapping="roleStatusMapping"
                      size="sm"
                      variant="badge"
                    />
                  </td>
                  
                  <!-- Permissions Column -->
                  <td class="py-1 px-2 text-sm">
                    <div class="flex flex-wrap gap-0.5">
                      <Badges
                        v-for="permission in role.permissions"
                        :key="permission.id"
                        :status="permission.name"
                        :statusMapping="permissionStatusMapping"
                        size="xxs"
                        variant="badge"
                      />
                    </div>
                  </td>
                  
                  <!-- Actions Column -->
                  <td class="py-2 px-2 text-sm">
                    <button @click="editRole(role)"
                            class="w-16 rounded px-2 py-1 text-xs ring-1 ring-inset text-gray-600 dark:text-gray-300 ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 hover:ring-gray-600 hover:text-gray-700 dark:hover:ring-indigo-400 dark:hover:text-indigo-300 transition-colors duration-200">
                      Edit
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Edit Role Modal -->
          <Modal :show="showEditModal" @close="closeEditModal" maxWidth="md">
            <div class="p-4">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">
                Edit Permissions for {{ selectedRole?.name }} Role
              </h3>
              
              <div class="mt-3 space-y-2">
                <template v-for="group in Object.keys(permissionGroups)" :key="group">
                  <div class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 mt-4 first:mt-0">
                    {{ capitalizeFirst(group) }}
                  </div>
                  <div v-for="permission in availablePermissions.filter(p => p.group === group)"
                       :key="permission.id" 
                       class="flex items-center p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150 border border-gray-100 dark:border-gray-600">
                    <input
                      type="checkbox"
                      :id="'permission-' + permission.id"
                      v-model="selectedPermissions"
                      :value="permission.id"
                      class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded"
                    >
                    <label :for="'permission-' + permission.id" class="ml-2 flex flex-col cursor-pointer flex-grow">
                      <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ capitalizeFirst(permission.name) }}
                      </span>
                      <span class="text-xs text-gray-500 dark:text-gray-400">
                        {{ getPermissionDescription(permission.name) }}
                      </span>
                    </label>
                    <Badges
                      :status="permission.name"
                      :statusMapping="permissionStatusMapping"
                      size="xxs"
                      variant="badge"
                    />
                  </div>
                </template>
              </div>

              <div class="mt-4 flex justify-end space-x-2">
                <button
                  @click="closeEditModal"
                  type="button"
                  class="px-3 py-1.5 text-xs font-medium border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-indigo-500"
                >
                  Cancel
                </button>
                <button
                  @click="saveRolePermissions"
                  type="button"
                  class="px-3 py-1.5 text-xs font-medium border border-transparent rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-indigo-500"
                >
                  Save
                </button>
              </div>
            </div>
          </Modal>
        </div>
      </div>
    </div>
  </AppLayout>
</template> 