<script setup>
import {ref, defineEmits} from 'vue';
import ApplicationLogo from '@/Components/logoSelf.vue';

defineProps({
    url : String,
    presaleApr : Number,
    publicApr : Number
});

</script>

<template>
     <!-- SELF StakeLink Card -->
    <a :href="url"
        class="scale-100 p-6 bg-white opacity-90 dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-purple-500">
        <div>
            <div
                class="h-16 w-16 bg-purple-50 dark:bg-purple-800/20 flex items-center justify-center rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    class="w-7 h-7 stroke-purple-500">
                    <path stroke-linecap="round"
                        d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z" />
                </svg>
            </div>
            <h2 class="mt-6 text-xl font-semibold text-gray-900 dark:text-white">Boost your <span class="text-purple-500">SELF</span> stake now!</h2>
            <span class='text-lg text-green-600'><span class="text-gray-800 dark:text-gray-200 text-xs">PRESALE APR:</span> {{ presaleApr }}%</span>
            <span class='text-lg text-green-600'><span class="ml-8 text-gray-800 dark:text-gray-200 text-xs">PUBLIC APR:</span> {{ publicApr }}%</span>

            <p class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                    Unlock a universe of possibilities in the <span class="text-purple-600">SELF</span> ecosystem with a simple act - staking your <span class="text-purple-600">SELF</span> tokens! Revel in a spectrum of unique perks, earn delightful rewards and shape your digital journey. Unleash the true essence of your <span class="text-purple-600">SELF</span>. Embark on your staking adventure today!
            </p>
        </div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
            class="self-center shrink-0 stroke-purple-500 w-6 h-6 mx-6">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75" />
        </svg>
    </a>
</template>