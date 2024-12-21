<script setup>
import ApplicationLogo from '@/Components/logoSelf.vue';

defineProps({
	url : String,
	supply : Number,
	on: Boolean
});
</script>

<template>
    <!-- SELF Or explore family 3 Card --> 

    <div  class="col-span-3 md:col-span-1"  >
        <a :href="url"
            class="scale-100 p-6 h-32 bg-white opacity-90 dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-purple-500">
            <div class="h-14 w-14 bg-purple-50 dark:bg-purple-800/20 flex items-center justify-center rounded-full"><ApplicationLogo /></div>
            <div class="ml-4">
                <h2 class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">Or explore <span class="text-purple-500">SELF</span> family</h2>
                <span v-if="on" class='text-lg text-green-600'><span class="text-gray-800 dark:text-gray-200 text-xs">NAMES REGISTERED: </span> {{supply}}</span>
            </div>
        </a>
    </div>
</template>