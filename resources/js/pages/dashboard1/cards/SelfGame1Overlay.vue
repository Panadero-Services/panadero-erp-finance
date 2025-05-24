<script setup>
import {ref, defineEmits} from 'vue';
import ApplicationLogo from '@/components/logoSelf.vue';
import Stars from '@/panadero/Stars.vue';
import Game from '@/panadero/Game.vue';


defineProps({
    w : Number,
    h : Number,
    callSign : String
});

</script>

<template>
    <!-- SELF Game1 Overlay -->
            <div class="absolute m-auto left-0 right-0 max-w-[1600px] max-w-[1200px] z-2 border border-gray-300 dark:border-gray-700 overscroll-none">
                <div >
                    <!-- StarOverlay 
                     -->
                    <Game :cvxWidth="w" :cvxHeight="h" :callSign="callSign" infPanel="true" :spriteSize="6"/>
                </div>
            </div>

</template>