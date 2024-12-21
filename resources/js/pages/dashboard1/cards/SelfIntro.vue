<script setup>
import {ref} from 'vue';
import ApplicationLogo from '@/components/logoSelf.vue';


</script>

<template>
     <!-- SELF Intro -->
            <div class="py-2 sm:py-3 lg:py-4 ">
                <div class="mx-auto max-w-xl px-6 lg:max-w-3xl lg:px-8 text-center">
                <!--    <ApplicationLogo class="block w-80 h-80" /> -->
                     <img src="@/layouts/logos/original.png">
                   <h1 class="text-xl font-medium text-gray-900 dark:text-white">
                        Welcome to your SELF dashboard!
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400 leading-relaxed text-lg">
                        ecosystem to be a breath of fresh air. We hope you love the new authorization module. 💜
                    </p>
                </div>
            </div>

</template>