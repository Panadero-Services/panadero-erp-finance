<script setup>
import {ref, defineEmits} from 'vue';
import ApplicationLogo from '@/components/logoSelf.vue';
import Stars from '@/panadero/Stars.vue';

defineProps({
    gameOn: Boolean,
    ready: Boolean, 
    overlay: Boolean,
    callSign: String
});

</script>

<template>
    <!-- SELF Game1 Card -->
    <div class="scale-100 p-2 bg-white opacity-90 dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-purple-500">
        <div class="relative w-full h-[280px]">
            <!-- Stars Background -->
                    <Stars :cvxWidth="565" :cvxHeight="280" :starsCount="25" />

            <!-- Start Game Button -->
            <div class="absolute inset-0 flex items-center justify-center">
                <button 
                    v-if="ready"
                    @click="$emit('start')"
                    class="px-6 py-3 text-xl bg-green-500 hover:bg-green-600 text-white rounded-lg shadow-lg transform hover:scale-105 transition-all"
                >
                    START GAME
                </button>
            </div>
        </div>
    </div>
</template>