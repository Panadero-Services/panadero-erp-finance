<script setup>
import ApplicationLogo from '@/components/logoSelf.vue';

defineProps({
	wallet : String,
	names : Array,
	on: Boolean
});
</script>

<template>
    <!-- SELF WideScreen Wallet Card -->
    <div
        class=" col-span-3 md:mb-2 scale-100 opacity-90 p-6 bg-white dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-purple-500">
        <div class="h-14 w-14 bg-purple-50 dark:bg-purple-800/20 flex items-center justify-center rounded-full">
            <ApplicationLogo />
        </div>
        <div class="ml-4 max-h-24 overflow-scroll">
            <h2 class="mt-1 text-xl text-gray-900 dark:text-white font-semibold">Wallet <span class="text-green-600 font-normal ">{{wallet}}</span></h2>
            <span v-if="names.length > 2 && on" class='text-sm text-green-600'><span class="ml-8 text-gray-800 dark:text-gray-200 text-xs">OWNS: </span> {{names}}</span>
        </div>
    </div>
</template>