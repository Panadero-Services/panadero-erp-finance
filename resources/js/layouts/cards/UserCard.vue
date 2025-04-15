<script setup>
import ApplicationLogo from '@/components/logoSelf.vue';
import { EnvelopeIcon } from '@heroicons/vue/24/outline'

defineProps({
    set: Object
});
</script>

<template>
    <!-- SELF Stakepool 2 Card -->
    <div  class="">
        <div class="scale-100 p-6 h-[500px] bg-white opacity-90 dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-xs shadow-md shadow-gray-400 dark:shadow-none motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-purple-500">
            <div class="-mt-16 absolute inline-flex items-center justify-center w-20 h-20 overflow-hidden bg-indigo-600 rounded-full dark:bg-gray-800">
                <span class="font-bold text-3xl text-gray-100 dark:text-gray-300">LB</span>
            </div>
           <div>

                <div class="mt-8 text-black dark:text-white text-2xl text-center"> {{$page.props.auth.user.name}}</div>
                <div class="text-black dark:text-white text-xs text-center"> Development Manager</div>

                <div class="ml-2 mt-8 divide-y divide-gray-300 ">
                    <div class="text-black dark:text-white text-xs flex">
                        <EnvelopeIcon class="mt-1 mr-2 w-4 h-4 text-blue-700"/>
                        {{$page.props.auth.user.email}}
                    </div>
                    <div class="mt-4">
                        <div>
                            <h2 class="mt-4 text-black dark:text-white text-xs">{{set.settings}}<span class="text-blue-700">Select your Project here!</span> </h2>
                            <h2 class="mt-0 text-black dark:text-white text-xs">{{set.settings}}<span class="text-blue-700">Select your Project here!</span> </h2>
                            <h2 class="mt-0 text-black dark:text-white text-xs">{{set.settings}}<span class="text-blue-700">Select your Project here!</span> </h2>
                            <h2 class="mt-0 text-black dark:text-white text-xs">{{set.settings}}<span class="text-blue-700">Select your Project here!</span> </h2>
                            <h2 class="mt-0 text-black dark:text-white text-xs">{{set.settings}}<span class="text-blue-700">Select your Project here!</span> </h2>
                        </div>
                    </div>

                    <div class="mt-40">
                        <div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</template>