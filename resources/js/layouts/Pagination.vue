<script setup>
  import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/20/solid'
  import { Link } from '@inertiajs/vue3'
  import { computed } from "vue";
  import NavLink from '@/components/NavLink.vue';

  const props = defineProps(['meta']);

  // Ensure we're getting single values, not arrays
  const from = computed(() => Array.isArray(props.meta.from) ? props.meta.from[0] : props.meta.from);
  const to = computed(() => Array.isArray(props.meta.to) ? props.meta.to[0] : props.meta.to);
  const total = computed(() => Array.isArray(props.meta.total) ? props.meta.total[0] : props.meta.total);

  // Get unique links
  const links = computed(() => {
    if (!Array.isArray(props.meta.links)) return props.meta.links;
    // Filter out duplicates based on URL
    return [...new Map(props.meta.links.map(link => [link.url, link])).values()];
  });

  const previousUrl = computed(() => links.value[0]?.url);
  const nextUrl = computed(() => links.value[links.value.length - 1]?.url);
</script>

<template>
  <div class="flex items-center justify-between px-4 py-3 sm:px-6">
    <div class="flex flex-1 justify-between sm:hidden">
      <Link v-if="previousUrl" :href="previousUrl" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Previous</Link>
      <span v-else class="relative inline-flex items-center rounded-md border border-gray-300 bg-gray-100 px-4 py-2 text-sm font-medium text-gray-400 cursor-not-allowed">Previous</span>
      
      <Link v-if="nextUrl" :href="nextUrl" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Next</Link>
      <span v-else class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-gray-100 px-4 py-2 text-sm font-medium text-gray-400 cursor-not-allowed">Next</span>
    </div>
    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
      <div>
        <p class="text-sm text-gray-500">
          Showing
          {{ ' ' }}
          <span class="font-medium">{{ from }}</span>
          {{ ' ' }}
          to
          {{ ' ' }}
          <span class="font-medium">{{ to }}</span>
          {{ ' ' }}
          of
          {{ ' ' }}
          <span class="font-medium">{{ total }}</span>
          {{ ' ' }}
          results
        </p>
      </div>
      
      <div>
          <!-- Current: "z-10 bg-indigo-600 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600", Default: "text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:outline-offset-0" -->
        <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm bg-white dark:bg-black" aria-label="Pagination">
          <template v-for="link in links" :key="link.label">
            <!-- Disabled link (when url is null) -->
            <span v-if="!link.url" 
                class="relative inline-flex items-center first-of-type:rounded-l-md last-of-type:rounded-r-md text-xs px-3 py-2 text-gray-400 dark:text-gray-600 ring-1 ring-inset ring-gray-300 dark:ring-gray-600 cursor-not-allowed"
                v-html="link.label">
            </span>
            <!-- Active/clickable link -->
            <NavLink v-else
                :href="link.url" 
                class="relative inline-flex items-center first-of-type:rounded-l-md last-of-type:rounded-r-md text-xs px-3 py-2"
                :class="{
                  'z-10 bg-indigo-600 dark:bg-indigo-500 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600': link.active,
                  'text-gray-900 dark:text-gray-300 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-offset-0': !link.active}
                  "
                v-html="link.label">
            </NavLink>
          </template>
        </nav>
      </div>

    </div>
  </div>
</template>

