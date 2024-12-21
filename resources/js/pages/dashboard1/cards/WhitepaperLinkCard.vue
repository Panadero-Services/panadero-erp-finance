<script setup>
import {ref, defineEmits} from 'vue';
import ApplicationLogo from '@/components/logoSelf.vue';

defineProps({
    url : String,
    usd1 : Number,
    usd2 : Number
});

</script>

<template>
     <!-- SELF WhitepaperLink Card -->
    <a :href="url"
         class="scale-100 p-6 bg-white opacity-90 dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-purple-500">
                        <div>
                            <div
                                class="h-16 w-16 bg-purple-50 dark:bg-purple-800/20 flex items-center justify-center rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    class="w-7 h-7 stroke-purple-500">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                </svg>
                            </div>
                            <h2 class="mt-6 text-xl font-semibold text-gray-900 dark:text-white">Welcome to <span class="text-purple-500">SELF</span></h2>
                            <span class="mt-4 text-gray-800 dark:text-gray-200 text-xs">DATABASE: <span class="text-green-600 text-lg"> {{usd1}} $ </span>  </span>
                            <span class="mt-4 text-gray-800 dark:text-gray-200 text-xs">PANCAKESWAP: <span class="text-green-600 text-lg"> {{usd2}} $ </span>  </span>
                            <p class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                                Wouldn’t it be great if you didn’t need extensive explanations to understand a crypto project fully?
                                At <span class="text-purple-600">SELF</span> we believe less is more and that’s why we want to make your life around crypto way easier. Take a couple of minutes to go through our documents......
                            </p>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            class="self-center shrink-0 stroke-purple-500 w-6 h-6 mx-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75" />
                        </svg>
                    </a>
</template>