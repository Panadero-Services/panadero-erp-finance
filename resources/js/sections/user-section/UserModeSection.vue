<script setup>
import { ref } from 'vue'

import { Switch } from '@headlessui/vue'

const props = defineProps({
    set: Object
});

const _button = "rounded-md border border-indigo-400 py-1 px-3 mr-1 text-sm font-medium shadow-sm hover:bg-indigo-700 hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 mt-2 disabled:opacity-25";

</script>
<template>

<!-- User Mode Buttons -->
<div class="pt-4 mb-4">
	<div class="grid grid-cols-6 text-xs ml-2 text-center">
		
		<!-- Button Spacer  -->
		<div>
			<label for="set.mode.first" class="block text-gray-700 mb-2 dark:text-slate-300">DarkMode</label>
			<Switch v-model="set.dark" :disabled='false' :class="[set.dark ? 'bg-gray-600 dark:bg-gray-800' : 'bg-gray-200 dark:bg-slate-500', 'relative inline-flex h-5 w-10 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2']">
				<span class="sr-only">set.dark</span>
				<span aria-hidden="true"  :class="[set.dark ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white dark:bg-slate-400 shadow ring-0 transition duration-200 ease-in-out']" />
			</Switch>
		</div>

		<!-- Button mode.first  -->
		<div>
			<label for="set.mode.first" class="block text-gray-700 mb-2 dark:text-slate-300">First</label>
			<Switch v-model="set.mode.first" :disabled='false' :class="[set.mode.first ? 'bg-green-600 dark:bg-green-800' : 'bg-gray-200 dark:bg-slate-500', 'relative inline-flex h-5 w-10 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2']">
				<span class="sr-only">set.mode.first</span>
				<span aria-hidden="true"  :class="[set.mode.first ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white dark:bg-slate-400 shadow ring-0 transition duration-200 ease-in-out']" />
			</Switch>
		</div>

		<!-- Button mode.noob  -->
		<div>
			<label for="set.mode.noob" class="block text-gray-700 mb-2 dark:text-slate-300">Noob</label>
			<Switch v-model="set.mode.noob" :disabled='false' :class="[set.mode.noob ? 'bg-green-600 dark:bg-green-800' : 'bg-gray-200 dark:bg-slate-500', 'relative inline-flex h-5 w-10 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2']">
				<span class="sr-only">set.mode.noob</span>
				<span aria-hidden="true"  :class="[set.mode.noob ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white dark:bg-slate-400 shadow ring-0 transition duration-200 ease-in-out']" />
			</Switch>
		</div>

		<!-- Button mode.full  -->
		<div>
			<label for="set.mode.full" class="block text-gray-700 mb-2 dark:text-slate-300">Full</label>
			<Switch v-model="set.mode.full" :disabled='false' :class="[set.mode.full ? 'bg-green-600 dark:bg-green-800' : 'bg-gray-200 dark:bg-slate-500', 'relative inline-flex h-5 w-10 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2']">
				<span class="sr-only">set.mode.full</span>
				<span aria-hidden="true"  :class="[set.mode.full ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white dark:bg-slate-400 shadow ring-0 transition duration-200 ease-in-out']" />
			</Switch>
		</div>

		<!-- Button mode.advanced  -->
		<div>
			<label for="set.mode.advanced" class="block text-gray-700 mb-2 dark:text-slate-300">Advanced</label>
			<Switch v-model="set.mode.advanced" :disabled='false' :class="[set.mode.advanced ? 'bg-green-600 dark:bg-green-800' : 'bg-gray-200 dark:bg-slate-500', 'relative inline-flex h-5 w-10 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2']">
				<span class="sr-only">set.mode.advanced</span>
				<span aria-hidden="true"  :class="[set.mode.advanced ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white dark:bg-slate-400 shadow ring-0 transition duration-200 ease-in-out']" />
			</Switch>
		</div>

		<!-- Button mode.dev  -->
		<div>
			<label for="set.mode.dev" class="block text-gray-700 mb-2 dark:text-slate-300">Dev</label>
			<Switch v-model="set.mode.dev" :disabled='false' :class="[set.mode.dev ? 'bg-green-600 dark:bg-green-800' : 'bg-gray-200 dark:bg-slate-500', 'relative inline-flex h-5 w-10 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2']">
				<span class="sr-only">set.mode.dev</span>
				<span aria-hidden="true"  :class="[set.mode.dev ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white dark:bg-slate-400 shadow ring-0 transition duration-200 ease-in-out']" />
			</Switch>
		</div>

	</div>
</div>

</template>
<style>
	

</style>