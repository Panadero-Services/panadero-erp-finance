<script setup>
import {ref} from 'vue';
import ApplicationLogo from '@/components/logoSelf.vue';

defineProps({
	set : Object
});

let selfRegister=ref("SELF");


</script>

<template>
    <!-- SELF Registering 1 Card -->
    <div  class="scale-100 p-6 bg-white opacity-90 dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-purple-500">
        <div>
            <div class="h-16 w-16 bg-purple-50 dark:bg-purple-800/20 flex items-center justify-center rounded-full">
                <ApplicationLogo />
            </div>
            <h2 class="mt-6 text-xl font-semibold text-gray-900 dark:text-white"><span class="text-purple-500">SELF</span> Registering</h2>

            <p class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
              Register your SELF identity here. Choose from a number of available names (underscore, min. 5 characters)
            </p>

            <div class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                <div>
                    <div class="mb-2 relative mt-2 rounded-md shadow-sm">
                      <input v-model="selfRegister" class="pl-4 bg-gradient-to-r from-purple-300 to-blue-200 block w-full rounded-md border-0 py-1.5 pr-10 text-black ring-2 ring-inset ring-pink-300 placeholder:text-gray-800 focus:ring-2 focus:ring-inset focus:ring-purple-800 sm:text-sm sm:leading-6" />
                      <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                        <ApplicationLogo class="h-5 w-5 text-purple-500" aria-hidden="true" />
                      </div>
                    </div>
                    <span v-if="selfRegister.length>6" class="mt-2 text-sm text-green-500">This is a valid self address 😍</span>
                    <span v-else class="mt-2 text-sm text-red-500">Oh no 😢 not a valid self address</span>
              </div>
            </div>
        </div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
            class="self-center shrink-0 stroke-purple-500 w-6 h-6 mx-6">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75" />
        </svg>
    </div>

</template>