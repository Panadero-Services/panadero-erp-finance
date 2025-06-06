<template>
  <div class="container mx-auto p-4 sm:p-6 lg:p-8">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-6">Manage Providers</h1>

    <!-- Button to open creation modal -->
    <div class="mb-6">
      <button @click="openModal()" 
              class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2 -mt-0.5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
        </svg>
        Add New Provider
      </button>
    </div>

    <!-- Table to display providers -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-100">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Name</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Type</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Configuration</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-if="providers.length === 0">
              <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">No providers configured yet.</td>
            </tr>
            <tr v-for="provider in providers" :key="provider.id" class="hover:bg-gray-50 transition-colors duration-150">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ provider.name }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ provider.type }}</td>
              <td class="px-6 py-4 text-sm text-gray-600">
                <pre class="bg-gray-50 p-3 rounded-md text-xs max-w-xs sm:max-w-sm md:max-w-md lg:max-w-lg xl:max-w-xl overflow-auto whitespace-pre-wrap break-all font-mono shadow-sm">{{ provider.config }}</pre>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                <button @click="openModal(provider)" 
                        class="text-indigo-600 hover:text-indigo-900 font-semibold py-1 px-3 rounded-md hover:bg-indigo-100 transition-colors duration-150">
                  Edit
                </button>
                <button @click="deleteProvider(provider.id)" 
                        class="text-red-600 hover:text-red-900 font-semibold py-1 px-3 rounded-md hover:bg-red-100 transition-colors duration-150">
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal for Add/Edit Provider -->
    <div v-if="showModal" class="fixed inset-0 z-50 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full flex items-center justify-center transition-opacity duration-300 ease-in-out" id="provider-modal">
      <div class="bg-white p-6 sm:p-8 rounded-lg shadow-xl w-full max-w-lg mx-4 sm:mx-0 transform transition-all duration-300 ease-in-out scale-95" 
           :class="{'scale-100': showModal}">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-xl sm:text-2xl font-bold text-gray-800">{{ editingProvider ? 'Edit' : 'Add New' }} Provider</h2>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors duration-150">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        <form @submit.prevent="saveProvider">
          <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
            <input type="text" v-model="currentProvider.name" id="name" required
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm placeholder-gray-400">
          </div>
          <div class="mb-4">
            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
            <input type="text" v-model="currentProvider.type" id="type" required placeholder="e.g., ApiProvider, BuyBotProvider"
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm placeholder-gray-400">
          </div>
          <div class="mb-6">
            <label for="config" class="block text-sm font-medium text-gray-700 mb-1">Configuration (JSON)</label>
            <textarea v-model="currentProvider.config" id="config" rows="6" required placeholder='{ "apiKey": "your_key", "url": "https://api.example.com" }'
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm font-mono placeholder-gray-400"></textarea>
            <p v-if="configError" class="text-red-500 text-xs mt-1">{{ configError }}</p>
          </div>
          <div class="flex justify-end space-x-3">
            <button type="button" @click="closeModal" 
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition ease-in-out duration-150">
              Cancel
            </button>
            <button type="submit" 
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
              {{ editingProvider ? 'Save Changes' : 'Create Provider' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

