<template>
  <AppLayout title="User Permissions">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        User Permissions Management
      </h2>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-4">
          <!-- Search Users -->
          <div class="mb-4">
            <SearchWithPopup
              v-model="searchQuery"
              placeholder="Search users by name or email..."
              @search="searchUsers"
              class="w-full max-w-md"
            />
          </div>

          <!-- Users Table -->
          <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
              <thead class="bg-gray-50 dark:bg-gray-900/50">
                <tr>
                  <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-1/5">
                    Name
                  </th>
                  <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-1/5">
                    Email
                  </th>
                  <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-1/2">
                    Roles
                  </th>
                  <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-1/10">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                  <!-- Name Column -->
                  <td class="py-2 px-2 text-sm text-gray-900 dark:text-gray-100 whitespace-nowrap w-[20%]">
                    <div class="overflow-hidden text-ellipsis">{{ user.name }}</div>
                  </td>
                  
                  <!-- Email Column -->
                  <td class="py-2 px-2 text-sm text-gray-900 dark:text-gray-100 whitespace-nowrap w-[20%]">
                    <div class="overflow-hidden text-ellipsis">{{ user.email }}</div>
                  </td>
                  
                  <!-- Roles Column -->
                  <td class="py-2 px-2 text-sm w-[50%]">
                    <div class="flex flex-wrap gap-1">
                      <div v-for="role in user.roles" 
                           :key="typeof role === 'object' ? role.id : role"
                           class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ring-1 ring-inset"
                           :class="getRoleClasses(role)">
                        {{ typeof role === 'object' ? role.name : role }}
                      </div>
                    </div>
                  </td>
                  
                  <!-- Actions Column -->
                  <td class="py-2 px-2 text-sm w-[10%]">
                    <div class="flex items-center gap-1.5">
                      <button @click="editUser(user)"
                              class="w-16 rounded px-2 py-1 text-xs ring-1 ring-inset text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover:text-gray-700 dark:hover:ring-indigo-400 dark:hover:text-gray-200 transition-colors duration-200">
                        Edit
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Edit User Modal -->
          <Modal :show="showEditModal" @close="closeEditModal" maxWidth="md">
            <div class="p-4 bg-white dark:bg-gray-800">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">
                Edit Roles for {{ selectedUser?.name }}
              </h3>
              
              <div class="mt-3 space-y-2">
                <div v-for="role in sortRoles(availableRoles)" :key="role.id" 
                     class="flex items-center p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150 border border-gray-100 dark:border-gray-700">
                  <input
                    type="checkbox"
                    :id="'role-' + role.id"
                    v-model="selectedRoles"
                    :value="role.id"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700 dark:checked:bg-indigo-600"
                  >
                  <label :for="'role-' + role.id" class="ml-2 flex flex-col cursor-pointer flex-grow">
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                      {{ capitalizeFirst(role.name) }}
                    </span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                      {{ getRoleDescription(role.name) }}
                    </span>
                  </label>
                  <div class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ring-1 ring-inset"
                       :class="getRoleClasses(role.name)">
                    {{ capitalizeFirst(role.name) }}
                  </div>
                </div>
              </div>

              <div class="mt-4 flex justify-end space-x-2">
                <button
                  @click="closeEditModal"
                  type="button"
                  class="px-3 py-1.5 text-xs font-medium border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                >
                  Cancel
                </button>
                <button
                  @click="saveUserRoles"
                  type="button"
                  class="px-3 py-1.5 text-xs font-medium border border-transparent rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
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

<script setup>
import { ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import SearchWithPopup from '@/components/inputs/SearchWithPopup.vue';
import Modal from '@/components/Modal.vue';

const users = ref([]);
const availableRoles = ref([]);
const searchQuery = ref('');
const showEditModal = ref(false);
const selectedUser = ref(null);
const selectedRoles = ref([]);

// Role hierarchy for sorting
const roleHierarchy = {
  'master': 1,
  'admin': 2,
  'developer': 3,
  'editor': 4,
  'member': 5
};

// Role colors with improved contrast
const roleStyles = {
  master: 'bg-purple-100 text-purple-800 border border-purple-200',
  admin: 'bg-red-100 text-red-800 border border-red-200',
  developer: 'bg-blue-100 text-blue-800 border border-blue-200',
  editor: 'bg-green-100 text-green-800 border border-green-200',
  member: 'bg-gray-100 text-gray-800 border border-gray-200'
};

const roleDescriptions = {
  master: 'Full system access with all privileges',
  admin: 'Administrative access to manage users and settings',
  developer: 'Technical access for development and debugging',
  editor: 'Content management and editing privileges',
  member: 'Basic member access'
};

const sortRoles = (roles) => {
  return [...roles].sort((a, b) => {
    return (roleHierarchy[a.name.toLowerCase()] || 99) - (roleHierarchy[b.name.toLowerCase()] || 99);
  });
};

const getRoleColor = (roleName) => {
  return roleStyles[roleName.toLowerCase()] || 'bg-gray-100 text-gray-800 border border-gray-200';
};

const getRoleDescription = (roleName) => {
  return roleDescriptions[roleName.toLowerCase()] || 'Standard user role';
};

const capitalizeFirst = (str) => {
  return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
};

const getInitials = (name) => {
  return name
    .split(' ')
    .map(word => word[0])
    .join('')
    .toUpperCase()
    .slice(0, 2);
};

const getRoleClasses = (role) => {
  // Handle different possible role formats
  const roleName = typeof role === 'object' ? role.name : String(role);
  
  const classes = {
    'master': 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300 ring-purple-700/10 dark:ring-purple-400/20',
    'admin': 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 ring-blue-700/10 dark:ring-blue-400/20',
    'developer': 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 ring-green-600/20 dark:ring-green-400/20',
    'editor': 'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-300 ring-yellow-600/20 dark:ring-yellow-400/20',
    'member': 'bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-300 ring-gray-500/10 dark:ring-gray-400/20'
  };
  
  // Convert to lowercase safely after ensuring it's a string
  const normalizedRole = roleName.toLowerCase();
  return classes[normalizedRole] || classes.member;
};

onMounted(async () => {
  try {
    await loadUsers();
    await loadRoles();
  } catch (error) {
    console.error('Setup error:', error);
  }
});

const loadUsers = async () => {
  try {
    const response = await axios.get('/api/user-permissions/users');
    users.value = response.data;
  } catch (error) {
    console.error('Error loading users:', error);
  }
};

const loadRoles = async () => {
  try {
    const response = await axios.get('/api/user-permissions/roles');
    availableRoles.value = response.data;
  } catch (error) {
    console.error('Error loading roles:', error);
  }
};

const searchUsers = async () => {
  if (searchQuery.value.length === 0) {
    await loadUsers();
    return;
  }
  
  try {
    const response = await axios.get(`/api/user-permissions/users/search?q=${encodeURIComponent(searchQuery.value)}`);
    users.value = response.data;
  } catch (error) {
    console.error('Error searching users:', error);
  }
};

const editUser = (user) => {
  selectedUser.value = user;
  selectedRoles.value = user.roles.map(role => role.id);
  showEditModal.value = true;
};

const closeEditModal = () => {
  showEditModal.value = false;
  selectedUser.value = null;
  selectedRoles.value = [];
};

const saveUserRoles = async () => {
  try {
    await axios.put(`/api/user-permissions/users/${selectedUser.value.id}/roles`, {
      roles: selectedRoles.value
    });
    await loadUsers();
    closeEditModal();
  } catch (error) {
    console.error('Error saving user roles:', error);
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